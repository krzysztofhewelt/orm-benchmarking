using dotnet_entity_framework;

public class Program
{
    public static async Task Main(string[] args)
    {
        // Entity Framework Benchmark
        Console.WriteLine("Testing Entity Framework...");
        BenchmarkEF benchmarkEf = new BenchmarkEF();
        await benchmarkEf.SendSaveResults();
        
        // No ORM benchmark
        Console.WriteLine("Testing NO-ORM...");
        BenchmarkNoOrm benchmarkNoOrm = new BenchmarkNoOrm();
        await benchmarkNoOrm.SendSaveResults();
    }
}
