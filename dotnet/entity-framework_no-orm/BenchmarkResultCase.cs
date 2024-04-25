using System.Text.Json.Serialization;

namespace dotnet_entity_framework;

public class BenchmarkResultCase(double avgTime, double stdTime, int numberOfQueries, List<string> queries)
{
    [JsonPropertyName("avgTime")]
    public double AvgTime { get; } = avgTime;
    
    [JsonPropertyName("stdTime")]
    public double StdTime { get; } = stdTime;
    
    [JsonPropertyName("numberOfQueries")]
    public int NumberOfQueries { get; } = numberOfQueries;
    
    [JsonPropertyName("queries")]
    public List<string> Queries { get; } = queries;
}