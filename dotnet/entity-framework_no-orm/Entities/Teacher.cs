using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using Newtonsoft.Json;

namespace dotnet_entity_framework.Entities;

[Table("teacher_info")]
public class Teacher
{
    [Key, ForeignKey("User")]
    public int UserId { get; set; }
    
    [JsonProperty(PropertyName = "scien_degree")]
    public string ScienDegree { get; set; }
    
    [JsonProperty(PropertyName = "business_email")]
    public string BusinessEmail { get; set; }
    
    [JsonProperty(PropertyName = "contact_number")]
    public string? ContactNumber { get; set; }
    
    public string? Room { get; set; }
    
    [JsonProperty(PropertyName = "consultation_hours")]
    public string? ConsultationHours { get; set; }

    public User User { get; set; } = null!;
}
