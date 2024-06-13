using System.Configuration;
using Newtonsoft.Json;

namespace dotnet_entity_framework;

public class DbCredentialsLoader
{
    private readonly string _workingDirectory = ConfigurationManager.AppSettings.Get("WorkingDirectory")!;

    public DbCredentials LoadDbCredentials()
    {
        string dbCredentialsFilePath = Path.Combine(_workingDirectory!, "dbCredentials.json");
        string file = File.ReadAllText(dbCredentialsFilePath);
        return JsonConvert.DeserializeObject<DbCredentials>(file)!;
    }
}
