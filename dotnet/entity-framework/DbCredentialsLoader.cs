using System.Reflection;
using Newtonsoft.Json;

namespace dotnet_entity_framework;

public class DbCredentialsLoader
{
    private readonly string _filename = "dbCredentials.json";

    public DbCredentials LoadDbCredentials()
    {
        string path = Path.Combine(Directory.GetParent(System.IO.Directory.GetCurrentDirectory()).Parent.Parent.Parent.FullName, _filename);
        
        if (!File.Exists(path))
        {
            throw new FileNotFoundException("File does not exists");
        }

        string file = File.ReadAllText(path);
        return JsonConvert.DeserializeObject<DbCredentials>(file)!;
    }
}
