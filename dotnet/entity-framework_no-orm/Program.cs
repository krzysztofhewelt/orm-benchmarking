using dotnet_entity_framework;

public class Program
{
    public static async Task Main(string[] args)
    {
        // // Entity Framework Benchmark
        // BenchmarkEF benchmarkEf = new BenchmarkEF();
        // await benchmarkEf.SendSaveResults();
        
        // No ORM benchmark
        BenchmarkNoORM benchmarkNoOrm = new BenchmarkNoORM();
        // await benchmarkNoOrm.SendSaveResults();
    }
}
