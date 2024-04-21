using System.Text.Json.Serialization;

namespace dotnet_entity_framework;

public class BenchmarkResultCase(double time, double min, double max, int numberOfQueries, List<string> queries)
{
    [JsonPropertyName("time")]
    public double Time { get; } = time;
    
    [JsonPropertyName("min")]
    public double Min { get; } = min;
    
    [JsonPropertyName("max")]
    public double Max { get; } = max;
    
    [JsonPropertyName("numberOfQueries")]
    public int NumberOfQueries { get; } = numberOfQueries;
    
    [JsonPropertyName("queries")]
    public List<string> Queries { get; } = queries;
}