using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;

namespace dotnet_entity_framework.Models;

public class BenchmarkContext : DbContext
{
    public DbSet<User> Users { get; set; }
    public DbSet<Teacher> Teacher { get; set; }
    public DbSet<Student> Student { get; set; }
    public DbSet<Course> Courses { get; set; }
    public DbSet<Task> Tasks { get; set; }

    protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder) =>
        optionsBuilder
            .UseNpgsql(
                "Host=localhost;Database=orm_benchmarking;Username=postgres;Password=superpassword"
            )
            .UseSnakeCaseNamingConvention()
            .LogTo(Console.WriteLine, LogLevel.Information);

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        modelBuilder
            .Entity<User>()
            .HasMany(u => u.Courses)
            .WithMany(c => c.Users)
            .UsingEntity<Dictionary<string, object>>(
                "course_enrollments",
                r => r.HasOne<Course>().WithMany().HasForeignKey("course_id"),
                l => l.HasOne<User>().WithMany().HasForeignKey("user_id")
            );
    }
}
