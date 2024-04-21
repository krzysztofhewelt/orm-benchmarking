using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using Newtonsoft.Json;

namespace dotnet_entity_framework.Entities;

[Table("tasks")]
public class Task
{
    public int Id { get; set; }
    public string Name { get; set; }
    public string Description { get; set; }
    
    [JsonProperty(PropertyName = "available_from")]
    public DateTime AvailableFrom { get; set; }
    
    [JsonProperty(PropertyName = "available_to")]
    public DateTime? AvailableTo { get; set; }
    public double MaxPoints { get; set; }

    [Key, ForeignKey("Course")]
    public int CourseId { get; set; }

    public Course Course { get; set; } = null!;
}
