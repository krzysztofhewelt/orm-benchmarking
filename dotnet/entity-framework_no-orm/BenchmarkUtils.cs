using System.Diagnostics;
using System.Text;
using dotnet_entity_framework.Entities;
using Newtonsoft.Json;

namespace dotnet_entity_framework;

public class BenchmarkUtils
{
    private List<Course>? _generatedCourses;
    private List<User>? _generatedUsers;

    public BenchmarkUtils()
    {
        LoadUsers();
        LoadCourses();
    }

    private void LoadCourses()
    {
        using (StreamReader r = new StreamReader(@"C:\xampp\htdocs\orm_benchmarking\courses.json"))
        {
            string json = r.ReadToEnd();
            _generatedCourses = JsonConvert.DeserializeObject<List<Course>>(json);
        }
    }

    private void LoadUsers()
    {
        using (StreamReader r = new StreamReader(@"C:\xampp\htdocs\orm_benchmarking\users.json"))
        {
            string json = r.ReadToEnd();
            _generatedUsers = JsonConvert.DeserializeObject<List<User>>(json);
        }
    }

    public void RemoveAssignedIds()
    {
        foreach (User user in _generatedUsers!)
        {
            user.Id = 0;

            if (user.Student != null)
                user.Student.UserId = 0;

            if (user.Teacher != null)
                user.Teacher.UserId = 0;
        }

        foreach (Course course in _generatedCourses!)
        {
            course.Id = 0;
        }
    }

    public dynamic GetMethodArgumentForMethod(string type, string table = "", int quantity = 1)
    {
        if (type == "select" || type == "update" || type == "delete")
            return quantity;

        if (type == "insert")
        {
            RemoveAssignedIds();
            if (table == "users")
                return _generatedUsers!.Take(quantity).ToList();

            if (table == "courses")
                return _generatedCourses!.Take(quantity).ToList();
        }

        return "";
    }
    
    public void RestoreDatabase()
    {
        ProcessStartInfo psi = new ProcessStartInfo
        {
            FileName = "php",
            WorkingDirectory = "C:\\xampp\\htdocs\\orm_benchmarking",
            Arguments = @"databaseRestore.php",
            RedirectStandardOutput = true,
            UseShellExecute = false,
            CreateNoWindow = true
        };

        Process process = new Process { StartInfo = psi };
        process.Start();
        process.WaitForExit();
    }
}