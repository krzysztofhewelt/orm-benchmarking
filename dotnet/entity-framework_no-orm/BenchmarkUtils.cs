﻿using System.Diagnostics;
using System.Configuration;
using dotnet_entity_framework.Entities;
using Newtonsoft.Json;

namespace dotnet_entity_framework;

public class BenchmarkUtils
{
    private List<Course>? _generatedCourses;
    private List<User>? _generatedUsers;
    private readonly string _workingDirectory = ConfigurationManager.AppSettings.Get("WorkingDirectory")!;

    public BenchmarkUtils()
    {
        LoadUsers();
        LoadCourses();
    }

    private void LoadCourses()
    {
        string coursesFilePath = Path.Combine(_workingDirectory!, "courses.json");
        
        using (StreamReader r = new StreamReader(coursesFilePath))
        {
            string json = r.ReadToEnd();
            _generatedCourses = JsonConvert.DeserializeObject<List<Course>>(json);
        }
    }

    private void LoadUsers()
    {
        string usersFilePath = Path.Combine(_workingDirectory, "users.json");
        
        using (StreamReader r = new StreamReader(usersFilePath))
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
            WorkingDirectory = _workingDirectory,
            Arguments = @"databaseRestore.php",
            RedirectStandardOutput = true,
            UseShellExecute = false,
            CreateNoWindow = true
        };

        Process process = new Process { StartInfo = psi };
        process.Start();
        process.WaitForExit();
    }

    public double CalculateAverage(double[] array)
    {
        return Double.Round(array.Average(), 2);
    }

    public double CalculateStandardDeviationTime(double[] array)
    {
        int numOfElements = array.Length;
        
        double variance = 0.0;
        double mean = array.Average();
        
        foreach (int element in array)
        {
            variance += double.Pow(element - mean, 2);
        }

        return double.Round(double.Sqrt(variance / numOfElements), 2);
    }
}