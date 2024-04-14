using System.Data.Common;
using System.Text.RegularExpressions;
using dotnet_entity_framework.Entities;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Logging;
using Task = System.Threading.Tasks.Task;

namespace dotnet_entity_framework;

public class BenchmarkContext : DbContext
{
    public DbSet<User> Users { get; set; }
    public DbSet<Teacher> Teacher { get; set; }
    public DbSet<Student> Student { get; set; }
    public DbSet<Course> Courses { get; set; }
    public DbSet<dotnet_entity_framework.Entities.Task> Tasks { get; set; }

    private DbCredentials dbCredentials = (new DbCredentialsLoader()).LoadDbCredentials(); 

    protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder) =>
        optionsBuilder
            .UseNpgsql(
                string.Format("Host={0};Port={1};Database={2};Username={3};Password={4}", dbCredentials.host, dbCredentials.port, dbCredentials.database, dbCredentials.username, dbCredentials.password)
            )
            .UseSnakeCaseNamingConvention()
            .LogTo(message =>
            {
                if (message.Contains("CommandExecuted"))
                {
                    var messageSplitted = message.Split(Environment.NewLine);
                    string messageJoined = string.Join(" ", messageSplitted.Skip(2));
                    string messageTrimmed = Regex.Replace(messageJoined, @"\s{2,}", " ");
                    
                    Benchmark.lastQuery = messageTrimmed;
                }
            }, new[] { DbLoggerCategory.Database.Command.Name }, LogLevel.Information);


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
