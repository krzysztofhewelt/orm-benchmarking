using System.ComponentModel.DataAnnotations.Schema;

namespace dotnet_entity_framework.Models;

[Table("users")]
public class User
{
    public int Id { get; set; }
    public string Name { get; set; }
    public string Surname { get; set; }
    public string Email { get; set; }
    public string Password { get; set; }
    public string AccountRole { get; set; }
    public bool Active { get; set; }

    public Teacher? Teacher { get; set; }
    public ICollection<Student> Student { get; } = new List<Student>();

    public List<Course> Courses { get; } = new();
}
