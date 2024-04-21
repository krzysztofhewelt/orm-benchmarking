using System.ComponentModel.DataAnnotations.Schema;
using Newtonsoft.Json;

namespace dotnet_entity_framework.Entities;

[Table("users")]
public class User
{
    public int Id { get; set; }
    public string Name { get; set; }
    public string Surname { get; set; }
    public string Email { get; set; }
    public string Password { get; set; }
    
    [JsonProperty(PropertyName = "account_role")]
    public string AccountRole { get; set; }
    public bool Active { get; set; }

    public Teacher? Teacher { get; set; }
    public Student? Student { get; set; }

    public List<Course> Courses { get; } = new();
}
