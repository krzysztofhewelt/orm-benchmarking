using System.Text.Json.Serialization;

namespace dotnet_entity_framework;

public class Result
{
    [JsonPropertyName("name")]
    public string Name { get; set; }
    [JsonPropertyName("time")]
    public double Time { get; set; }
    [JsonPropertyName("queries")]
    public List<string> Queries { get; set; }

    public Result() {}
    
    public Result(string name, double time, List<string> queries)
    {
        Name = name;
        Time = time;
        Queries = queries;
    }
}