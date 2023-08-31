using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace dotnet_entity_framework.Models;

[Table("courses")]
public class Course
{
    [Key]
    public int Id { get; set; }
    public string Name { get; set; }
    public string? Description { get; set; }
    public DateTime AvailableFrom { get; set; }
    public DateTime? AvailableTo { get; set; }

    public List<User> Users { get; } = new();
    public ICollection<Task> Tasks { get; } = new List<Task>();
}
