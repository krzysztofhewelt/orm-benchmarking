using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using Microsoft.EntityFrameworkCore;

namespace dotnet_entity_framework.Models;

[Table("tasks")]
public class Task
{
    public int Id { get; set; }
    public string Name { get; set; }
    public string Description { get; set; }
    public DateTime AvailableFrom { get; set; }
    public DateTime? AvailableTo { get; set; }
    public double MaxPoints { get; set; }

    [Key, ForeignKey("Course")]
    public int CourseId { get; set; }

    public Course Course { get; set; } = null!;
}
