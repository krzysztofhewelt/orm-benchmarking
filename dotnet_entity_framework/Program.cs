using dotnet_entity_framework;

public class Program
{
    public static async Task Main(string[] args)
    {
        Benchmark benchmark = new Benchmark();
        await benchmark.SendSaveResults();
    }
}
