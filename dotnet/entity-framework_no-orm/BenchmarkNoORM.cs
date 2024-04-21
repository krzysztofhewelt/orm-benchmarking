using dotnet_entity_framework.Entities;
using MySqlConnector;

namespace dotnet_entity_framework;

public class BenchmarkNoORM
{
    private MySqlConnection _mySqlConnection;

    public BenchmarkNoORM()
    {
        string mysqlConnectionString = PrepareConnectionString();
        OpenMySqlConnection(mysqlConnectionString);

        SelectSimpleUsers(10);
    }

    private string PrepareConnectionString()
    { 
        DbCredentials dbCredentials = (new DbCredentialsLoader()).LoadDbCredentials();
        string connectionString = string.Format("server={0};port={1};database={2};uid={3};pwd={4}",
            dbCredentials.host,
            dbCredentials.port, dbCredentials.database, dbCredentials.username, dbCredentials.password);

        return connectionString;
    }

    private void OpenMySqlConnection(string connectionString)
    {
        _mySqlConnection = new MySqlConnection(connectionString);
        _mySqlConnection.Open();
    }
    
    private void SelectSimpleUsers(int quantity)
    {
        string query = $"SELECT * FROM users LIMIT {quantity}";
        using (var command = new MySqlCommand(query, _mySqlConnection))
        using (var reader = command.ExecuteReader())
        {
            List<User> users = new List<User>();
            while (reader.Read())
            {
                Console.WriteLine();
                User user = new User
                {
                    // Populate User object from reader
                };
                users.Add(user);
            }
            
            Console.WriteLine(users[0].Email);
        }
    }
}