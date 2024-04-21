using System.Text.Json.Serialization;

namespace dotnet_entity_framework;

public class BenchmarkResult(string name, Dictionary<int, BenchmarkResultCase> numberOfRecords)
{
    [JsonPropertyName("name")]
    public string Name { get; } = name;

    [JsonPropertyName("numberOfRecords")]
    public Dictionary<int, BenchmarkResultCase> NumberOfRecords { get; } = numberOfRecords;
}