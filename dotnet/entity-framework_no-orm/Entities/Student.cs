using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using Newtonsoft.Json;

namespace dotnet_entity_framework.Entities;

[Table("student_info")]
public class Student
{
    [Key, ForeignKey("User")]
    public int UserId { get; set; }
    
    [JsonProperty(PropertyName = "field_of_study")]
    public string FieldOfStudy { get; set; }
    
    public int Semester { get; set; }
    
    [JsonProperty(PropertyName = "year_of_study")]
    public string YearOfStudy { get; set; }
    
    [JsonProperty(PropertyName = "mode_of_study")]
    public string ModeOfStudy { get; set; }

    public User User { get; set; } = null!;
}
