using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using Newtonsoft.Json;

namespace dotnet_entity_framework.Entities;

[Table("courses")]
public class Course
{
    [Key]
    public int Id { get; set; }
    public string Name { get; set; }
    public string? Description { get; set; }
    
    [JsonProperty(PropertyName = "available_from")]
    public DateTime AvailableFrom { get; set; }
    
    [JsonProperty(PropertyName = "available_to")]
    public DateTime? AvailableTo { get; set; }

    public List<User> Users { get; } = new();
    public ICollection<Task> Tasks { get; } = new List<Task>();
}
