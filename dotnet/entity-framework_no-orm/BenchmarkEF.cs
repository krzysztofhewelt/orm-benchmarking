using System.Configuration;
using System.Diagnostics;
using System.Reflection;
using System.Text;
using System.Text.Json;
using dotnet_entity_framework.Entities;
using Microsoft.EntityFrameworkCore;

namespace dotnet_entity_framework;

public class BenchmarkEF
{
    private readonly int _numberOfRepeats = 100;
    private readonly int[] _numberOfRecords = [1, 50, 100, 500, 1000];
    private readonly DatabaseContext _db;
    private readonly BenchmarkUtils _benchmarkUtils;
    private List<BenchmarkResult> _benchmarks;
    public static List<string>? GeneratedQueries;

    public BenchmarkEF()
    {
        _benchmarks = new List<BenchmarkResult>();
        _db = new DatabaseContext();
        _benchmarkUtils = new BenchmarkUtils();
        GeneratedQueries = new List<string>();

        // run all benchmark methods...
        Run("SelectSimpleUsers", type: "select", name: "Select n first users");
        Run("SelectComplexStudentsWithInformationAndCourses", type: "select", name: "Select first n students and their courses, order by surname");
        Run("SelectComplexUsersTasks", type: "select", name: "Select tasks to do for n first students");
        
        Run("InsertUsers", type: "insert", table: "users", name: "Insert n users with additional information using transaction");
        Run("InsertCourses", type: "insert", table: "courses", name: "Insert n courses");
        
        Run("UpdateCoursesEndDate", type: "update", name: "Prolong available to date for n courses");
        
        Run("DetachUsersFromCourses", type: "delete", name: "Remove n first users from their courses");
        Run("DeleteCourses", type: "delete", name: "Delete n courses");

    }

    private List<User> SelectSimpleUsers(int quantity)
    {
        return _db.Users.Take(quantity).ToList();
    }

    private List<User> SelectComplexStudentsWithInformationAndCourses(int quantity)
    {
        return _db.Users
            .Where(u => u.AccountRole == "student")
            .Include(u => u.Student)
            .Include(u => u.Student)
            .OrderBy(u => u.Surname)
            .Take(quantity)
            .ToList();
    }

    private List<User> SelectComplexUsersTasks(int quantity)
    {
        return _db.Users
            .Include(u => u.Courses)
            .ThenInclude(c => c.Tasks)
            .Take(quantity)
            .ToList();
    }

    private void InsertUsers(List<User> users)
    {
        using var transaction = _db.Database.BeginTransaction();
        try
        {
            _db.Users.AddRange(users);
            _db.SaveChanges();
            transaction.Commit();
        }
        catch (Exception ex)
        {
            transaction.Rollback();
            Console.WriteLine(ex);
        }
    }

    private void InsertCourses(List<Course> courses)
    {
        _db.Courses.AddRange(courses);
        _db.SaveChanges();
    }

    private void UpdateCoursesEndDate(int quantity)
    {
        _db.Courses.Take(quantity)
            .ExecuteUpdate(u => u.SetProperty(c => c.AvailableTo, c => new DateTime(2024, 10, 1)));
        _db.SaveChanges();
    }

    private void DetachUsersFromCourses(int quantityUsers)
    {
        _db.Users
            .Include(u => u.Courses)
            .Take(quantityUsers)
            .ToList()
            .ForEach(user => user.Courses.Clear());

        _db.SaveChanges();
    }

    private void DeleteCourses(int quantity)
    {
        _db.Courses.Take(quantity).ExecuteDelete();
        _db.SaveChanges();
    }

    public void Run(string method, string type = "", string table = "", string name = "")
    {
        Stopwatch sw = new Stopwatch();
        double[] tempTimes = new double[_numberOfRepeats];
        Dictionary<int, BenchmarkResultCase> benchmarkResultCases = new Dictionary<int, BenchmarkResultCase>();

        Console.WriteLine("Benchmarking \"" + method + "\":");
        
        foreach (int numberOfRecord in _numberOfRecords)
        {
            var methodArgument = _benchmarkUtils.GetMethodArgumentForMethod(type, table, numberOfRecord);

            for (int i = 0; i < _numberOfRepeats; i++)
            {
                GeneratedQueries = [];

                sw.Restart();
                GetType().GetMethod(method, BindingFlags.NonPublic | BindingFlags.Instance)
                    ?.Invoke(this, new object[] { methodArgument });
                sw.Stop();
                tempTimes[i] = sw.Elapsed.TotalMilliseconds;

                if (type != "select")
                    _benchmarkUtils.RestoreDatabase();

                DetachEntries();
            }

            tempTimes[0] = tempTimes[1]; // remove outlier, cold start causes high value
            double avgTime = _benchmarkUtils.CalculateAverage(tempTimes);
            double stdTime = _benchmarkUtils.CalculateStandardDeviationTime(tempTimes);

            if (GeneratedQueries.Count > 10)
                GeneratedQueries = GeneratedQueries[..9];

            benchmarkResultCases.Add(numberOfRecord,
                new BenchmarkResultCase(avgTime, stdTime, GeneratedQueries!.Count, GeneratedQueries));

            Console.WriteLine($" - {numberOfRecord}: avg={avgTime}; std={stdTime}");
        }

        AddBenchmark(name, benchmarkResultCases);
    }

    private void DetachEntries()
    {
        foreach (var entry in _db.ChangeTracker.Entries())
        {
            entry.State = EntityState.Detached;
        }
    }

    private void AddBenchmark(string benchmark, Dictionary<int, BenchmarkResultCase> benchmarkResultCases)
    {
        _benchmarks.Add(new BenchmarkResult(benchmark, benchmarkResultCases));
    }

    public async System.Threading.Tasks.Task SendSaveResults()
    {
        string jsonData = JsonSerializer.Serialize(
            new
            {
                orm_name = "Entity Framework",
                orm_language = "C#/.NET",
                orm_version = "8.0.2",
                benchmarks = _benchmarks
            }
        );
        
        HttpClient httpClient = new HttpClient();
        string saveResultsEndpoint = ConfigurationManager.AppSettings.Get("SaveResultsEndpoint")!;

        using HttpResponseMessage response = await httpClient.PostAsync(
            saveResultsEndpoint,
            new StringContent(jsonData, Encoding.UTF8, "application/json")
        );

        try
        {
            response.EnsureSuccessStatusCode();
            Console.WriteLine("Results saved successfully.");
        }
        catch (HttpRequestException exception)
        {
            Console.WriteLine("Results has not been saved:");
            Console.WriteLine(exception);
        }
    }
}