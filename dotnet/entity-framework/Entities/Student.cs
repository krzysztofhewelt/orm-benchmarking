using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace dotnet_entity_framework.Entities;

[Table("student_info")]
public class Student
{
    [Key, ForeignKey("User")]
    public int UserId { get; set; }
    public string FieldOfStudy { get; set; }
    public int Semester { get; set; }
    public string YearOfStudy { get; set; }
    public string ModeOfStudy { get; set; }

    public User User { get; set; } = null!;
}
