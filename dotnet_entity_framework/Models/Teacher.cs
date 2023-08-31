using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace dotnet_entity_framework.Models;

[Table("teacher_info")]
public class Teacher
{
    [Key, ForeignKey("User")]
    public int UserId { get; set; }
    public string ScienDegree { get; set; }
    public string BusinessEmail { get; set; }
    public string? ContactNumber { get; set; }
    public string? Room { get; set; }
    public string? ConsultationHours { get; set; }

    public User User { get; set; } = null!;
}
