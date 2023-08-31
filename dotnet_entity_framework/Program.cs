using System.Text;
using System.Text.Json;
using dotnet_entity_framework.Models;
using Microsoft.EntityFrameworkCore;

public class Program
{
    public static void Main(string[] args)
    {
        using (var db = new BenchmarkContext())
        {
            var result = db.Users
                .Include(user => user.Courses)
                .ThenInclude(c => c.Tasks)
                .FirstOrDefault(u => u.Id == 725);
            // Console.WriteLine("COUNT: " + result.Courses[0].Tasks);

            // var result = db.Users.Include(user => user.Teacher).First();
            // Console.WriteLine("COUNT " + result.Teacher.Room);
        }
    }

    async void sendResults()
    {
        var jsonData = JsonSerializer.Serialize(
            new { orm_name = "Entity Framework", orm_version = "7.0.4" }
        );

        using (var httpClient = new HttpClient())
        {
            var response = await httpClient.PostAsync(
                "http://localhost/index.php?save-results",
                new StringContent(jsonData, Encoding.UTF8, "application/json")
            );
        }
    }

    // get queries
    // measure time
}
