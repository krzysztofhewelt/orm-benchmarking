using System.Diagnostics;
using System.Text;
using System.Text.Json;
using dotnet_entity_framework.Models;
using Microsoft.EntityFrameworkCore;

namespace dotnet_entity_framework;

public class Benchmark
{
    private List<Result> benchmarks;
    private const int NumberOfRepeats = 100;
    public static string lastQuery;

    public Benchmark()
    {
        benchmarks = new List<Result>();
        
        // run all benchmark methods...
        Run("Test1");
    }

    public dynamic Test1()
    {
        using var db = new BenchmarkContext();

        return db.Users.Include(user => user.Courses).ThenInclude(c => c.Tasks).First();
    }

    public void Test2()
    {
        using var db = new BenchmarkContext();

        var user = new User
        {
            AccountRole = "student",
            Active = true,
            Email = "aaaa@emails.com",
            Name = "Jan",
            Surname = "Kowalski",
            Password = "123456"
        };
        db.Add(user);
        db.SaveChanges();
    }

    public void Run(string method, int times = NumberOfRepeats)
    {
        Stopwatch sw = new Stopwatch();
        double[] tempTimes = new double[times];

        for (int i = 0; i < times; i++)
        {
            sw.Restart();
            GetType().GetMethod(method)?.Invoke(this, null);
            sw.Stop();
            tempTimes[i] = sw.ElapsedMilliseconds;
        }

        double avgTime = tempTimes.Sum() / times;

        AddBenchmark(method, avgTime, new List<string>() { lastQuery });

        Console.WriteLine("AVG time of benchmark \"" + method + "\": " + avgTime + " ms.");
    }

    private void AddBenchmark(string benchmark, double avgTimes, List<string> queries)
    {
        benchmarks.Add(new Result(benchmark, avgTimes, queries));
    }

    public async System.Threading.Tasks.Task SendSaveResults()
    {
        string jsonData = JsonSerializer.Serialize(
            new
            {
                orm_name = "Entity Framework",
                orm_version = "7.0.4",
                benchmarks
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
