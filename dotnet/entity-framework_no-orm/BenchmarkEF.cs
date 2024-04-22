﻿using System.Diagnostics;
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

        // // run all benchmark methods...
        Run("SelectSimpleUsers", type: "select");
        Run("SelectComplexStudentsWithInformationAndCourses", type: "select");
        // Run("SelectComplexUsersTasks", type: "select");

        // Run("InsertCourses", "insert", "courses");
        // Run("InsertUsers", "insert", "users");

        // Run("UpdateCoursesEndDate", type: "update");

        // Run("DetachUsersFromCourses", type: "delete");
        // Run("DeleteCourses", type: "delete");
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

    public void Run(string method, string type = "", string table = "")
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
                tempTimes[i] = sw.ElapsedMilliseconds;

                if (type != "select")
                    _benchmarkUtils.RestoreDatabase();

                DetachEntries();
            }

            tempTimes[0] = tempTimes[1]; // remove outlier, cold start causes high value
            double avgTime = tempTimes.Average();
            double minTime = tempTimes.Min();
            double maxTime = tempTimes.Max();

            benchmarkResultCases.Add(numberOfRecord,
                new BenchmarkResultCase(avgTime, minTime, maxTime, GeneratedQueries!.Count, GeneratedQueries));

            Console.WriteLine($" - {numberOfRecord}: {avgTime}; min={minTime}, max={maxTime}");
        }

        AddBenchmark(method, benchmarkResultCases);
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
                orm_version = "8.0.2",
                benchmarks = _benchmarks
            }
        );
        
        HttpClient httpClient = new HttpClient();

        using HttpResponseMessage response = await httpClient.PostAsync(
            $"http://localhost/orm_benchmarking/index.php?save-results",
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