using System.Diagnostics;
using System.Reflection;
using System.Text;
using System.Text.Json;
using dotnet_entity_framework.Entities;
using MySqlConnector;
using Task = dotnet_entity_framework.Entities.Task;

namespace dotnet_entity_framework;

public class BenchmarkNoOrm
{
    private readonly int _numberOfRepeats = 100;
    private readonly int[] _numberOfRecords = [1, 50, 100, 500, 1000];
    private readonly BenchmarkUtils _benchmarkUtils;
    private List<BenchmarkResult> _benchmarks;
    private MySqlConnection _mySqlConnection;

    public BenchmarkNoOrm()
    {
        string mysqlConnectionString = PrepareConnectionString();
        OpenMySqlConnection(mysqlConnectionString);

        _benchmarks = new List<BenchmarkResult>();
        _benchmarkUtils = new BenchmarkUtils();

        Run("SelectSimpleUsers", type: "select", name: "Select n first users");
        Run("SelectComplexStudentsWithInformationAndCourses", type: "select", name: "Select first n students and their courses, order by surname");
        Run("SelectComplexUsersTasks", type: "select", name: "Select tasks to do for n first students");

        Run("InsertUsers", type: "insert", table: "users", name: "Insert n users with additional information using transaction");
        Run("InsertCourses", type: "insert", table: "courses", name: "Insert n courses");

        Run("UpdateCoursesEndDate", type: "update", name: "Prolong available to date for n courses");

        Run("DetachUsersFromCourses", type: "delete", name: "Remove n first users from their courses");
        Run("DeleteCourses", type: "delete", name: "Delete n courses");
    }

    private string PrepareConnectionString()
    {
        DbCredentials dbCredentials = (new DbCredentialsLoader()).LoadDbCredentials();
        string connectionString = string.Format("server={0};port={1};database={2};uid={3};pwd={4}",
            dbCredentials.host,
            dbCredentials.port, dbCredentials.database, dbCredentials.username, dbCredentials.password);

        return connectionString;
    }

    private void OpenMySqlConnection(string connectionString)
    {
        _mySqlConnection = new MySqlConnection(connectionString);
        _mySqlConnection.Open();
    }

    private List<User> SelectSimpleUsers(int quantity)
    {
        List<User> users = new List<User>();

        string query = $"SELECT * FROM users LIMIT {quantity}";
        MySqlCommand command = new MySqlCommand(query, _mySqlConnection);
        using (MySqlDataReader reader = command.ExecuteReader())
        {
            while (reader.Read())
            {
                User user = new User
                {
                    Id = Convert.ToInt32(reader["id"]),
                    Name = reader["name"].ToString(),
                    Surname = reader["surname"].ToString(),
                    Email = reader["email"].ToString(),
                    Password = reader["password"].ToString(),
                    AccountRole = reader["account_role"].ToString(),
                    Active = Convert.ToBoolean(reader["active"])
                };
                users.Add(user);
            }
        }

        return users;
    }

    private List<User> SelectComplexStudentsWithInformationAndCourses(int quantity)
    {
        string query =
            $"SELECT * FROM (SELECT * FROM users WHERE account_role = 'student' ORDER BY surname LIMIT {quantity}) as us INNER JOIN student_info ON us.id = student_info.user_id INNER JOIN orm_benchmarking.course_enrollments ce on us.id = ce.user_id INNER JOIN orm_benchmarking.courses c on ce.course_id = c.id";
        Dictionary<int, User> users = new Dictionary<int, User>();

        MySqlCommand command = new MySqlCommand(query, _mySqlConnection);
        using (MySqlDataReader reader = command.ExecuteReader())
        {
            while (reader.Read())
            {
                int userId = Convert.ToInt32(reader["id"]);
                if (!users.ContainsKey(userId))
                {
                    User user = new User
                    {
                        Id = userId,
                        Name = reader["name"].ToString(),
                        Surname = reader["surname"].ToString(),
                        Email = reader["email"].ToString(),
                        Password = reader["password"].ToString(),
                        AccountRole = reader["account_role"].ToString(),
                        Active = Convert.ToBoolean(reader["active"])
                    };

                    users.Add(userId, user);
                }

                Student student = new Student();
                student.UserId = Convert.ToInt32(reader["user_id"]);
                student.FieldOfStudy = reader["field_of_study"].ToString();
                student.Semester = Convert.ToInt32(reader["semester"]);
                student.YearOfStudy = reader["year_of_study"].ToString();
                student.ModeOfStudy = reader["mode_of_study"].ToString();
                users[userId].Student = student;


                if (users.ContainsKey(userId))
                {
                    User user = users[userId];
                    user.Courses.Add(new Course
                    {
                        Id = Convert.ToInt32(reader["course_id"]),
                        Name = reader["name"].ToString(),
                        Description = reader["description"].ToString(),
                        AvailableFrom = Convert.ToDateTime(reader["available_from"]),
                        AvailableTo = Convert.ToDateTime(reader["available_to"])
                    });
                }
            }
        }

        return new List<User>(users.Values);
    }

    private void SelectComplexUsersTasks(int quantity)
    {
        string query =
            $"SELECT * FROM tasks INNER JOIN orm_benchmarking.courses c on tasks.course_id = c.id INNER JOIN orm_benchmarking.course_enrollments ce on c.id = ce.course_id INNER JOIN (SELECT * FROM users LIMIT {quantity}) u on ce.user_id = u.id;";
        Dictionary<int, User> users = new Dictionary<int, User>();

        MySqlCommand command = new MySqlCommand(query, _mySqlConnection);
        using (MySqlDataReader reader = command.ExecuteReader())
        {
            while (reader.Read())
            {
                int userId = Convert.ToInt32(reader["id"]);
                if (!users.ContainsKey(userId))
                {
                    User user = new User
                    {
                        Id = userId,
                        Name = reader["name"].ToString(),
                        Surname = reader["surname"].ToString(),
                        Email = reader["email"].ToString(),
                        Password = reader["password"].ToString(),
                        AccountRole = reader["account_role"].ToString(),
                        Active = Convert.ToBoolean(reader["active"])
                    };
                    users.Add(userId, user);
                }

                User currentUser = users[userId];
                int courseId = Convert.ToInt32(reader["course_id"]);
                if (!currentUser.Courses.Any(c => c.Id == courseId))
                {
                    currentUser.Courses.Add(new Course
                    {
                        Id = courseId,
                        Name = reader["name"].ToString(),
                        Description =
                            reader["description"].ToString(),
                        AvailableFrom = Convert.ToDateTime(reader["available_from"]),
                        AvailableTo = Convert.ToDateTime(reader["available_to"]),
                    });
                }

                Course currentCourse = currentUser.Courses.FirstOrDefault(c => c.Id == courseId);
                currentCourse.Tasks.Add(new Task
                {
                    Id = Convert.ToInt32(reader["id"]),
                    CourseId = Convert.ToInt32(reader["course_id"]),
                    Description = reader["description"].ToString(),
                    Name = reader["name"].ToString(),
                    AvailableFrom = Convert.ToDateTime(reader["available_from"]),
                    AvailableTo = Convert.ToDateTime(reader["available_to"]),
                });
            }
        }
    }

    private void InsertUsers(List<User> users)
    {
        string insertUserQuery =
            "INSERT INTO users (name, surname, email, password, account_role, active) VALUES (@name, @surname, @email, @password, @account_role, @active)";
        string insertStudentQuery =
            "INSERT INTO student_info (user_id, field_of_study, semester, year_of_study, mode_of_study) VALUES (@user_id, @field_of_study, @semester, @year_of_study, @mode_of_study)";
        string insertTeacherQuery =
            "INSERT INTO teacher_info (user_id, scien_degree, business_email, contact_number, room, consultation_hours) VALUES (@user_id, @scien_degree, @business_email, @contact_number, @room, @consultation_hours)";

        foreach (User user in users)
        {
            MySqlTransaction transaction = _mySqlConnection.BeginTransaction();
            MySqlCommand userCommand = new MySqlCommand(insertUserQuery, _mySqlConnection);
            userCommand.Transaction = transaction;

            try
            {
                userCommand.Parameters.AddWithValue("@name", user.Name);
                userCommand.Parameters.AddWithValue("@surname", user.Surname);
                userCommand.Parameters.AddWithValue("@email", user.Email);
                userCommand.Parameters.AddWithValue("@password", user.Password);
                userCommand.Parameters.AddWithValue("@account_role", user.AccountRole);
                userCommand.Parameters.AddWithValue("@active", user.Active);

                userCommand.ExecuteNonQuery();

                if (user.Student != null)
                {
                    MySqlCommand studentCommand = new MySqlCommand(insertStudentQuery, _mySqlConnection);
                    studentCommand.Transaction = transaction;

                    studentCommand.Parameters.AddWithValue("@user_id", userCommand.LastInsertedId);
                    studentCommand.Parameters.AddWithValue("@field_of_study", user.Student.FieldOfStudy);
                    studentCommand.Parameters.AddWithValue("@semester", user.Student.Semester);
                    studentCommand.Parameters.AddWithValue("@year_of_study", user.Student.YearOfStudy);
                    studentCommand.Parameters.AddWithValue("@mode_of_study", user.Student.ModeOfStudy);

                    studentCommand.ExecuteNonQuery();
                }
                else if (user.Teacher != null)
                {
                    MySqlCommand teacherCommand = new MySqlCommand(insertTeacherQuery, _mySqlConnection);
                    teacherCommand.Transaction = transaction;

                    teacherCommand.Parameters.AddWithValue("@user_id", userCommand.LastInsertedId);
                    teacherCommand.Parameters.AddWithValue("@scien_degree", user.Teacher.ScienDegree);
                    teacherCommand.Parameters.AddWithValue("@business_email", user.Teacher.BusinessEmail);
                    teacherCommand.Parameters.AddWithValue("@contact_number", user.Teacher.ContactNumber);
                    teacherCommand.Parameters.AddWithValue("@room", user.Teacher.Room);
                    teacherCommand.Parameters.AddWithValue("@consultation_hours", user.Teacher.ConsultationHours);

                    teacherCommand.ExecuteNonQuery();
                }

                transaction.Commit();
            }
            catch (MySqlException ex)
            {
                Console.WriteLine("An database error occured " + ex);
                transaction.Rollback();
            }
        }
    }

    private void InsertCourses(List<Course> courses)
    {
        string insertCourseQuery =
            "INSERT INTO courses (name, description, available_from, available_to) VALUES (@name, @description, @available_from, @available_to)";

        foreach (Course course in courses)
        {
            MySqlCommand command = new MySqlCommand(insertCourseQuery, _mySqlConnection);
            command.Parameters.AddWithValue("@name", course.Name);
            command.Parameters.AddWithValue("@description", course.Description);
            command.Parameters.AddWithValue("@available_from", course.AvailableFrom);
            command.Parameters.AddWithValue("@available_to", course.AvailableTo);
            command.ExecuteNonQuery();
        }
    }

    private void UpdateCoursesEndDate(int quantity)
    {
        string updateCoursesQuery = $"UPDATE courses SET available_to = '2024-10-01' LIMIT {quantity}";
        MySqlCommand command = new MySqlCommand(updateCoursesQuery, _mySqlConnection);
        command.ExecuteNonQuery();
    }

    private void DetachUsersFromCourses(int quantityUsers)
    {
        string getNUsersQuery = $"SELECT * FROM users LIMIT {quantityUsers}";
        string deleteUserFromCourse = "DELETE FROM course_enrollments WHERE user_id=@user_id";

        MySqlCommand userSelectCommand = new MySqlCommand(getNUsersQuery, _mySqlConnection);
        List<int> usersId = new List<int>();
        using (MySqlDataReader reader = userSelectCommand.ExecuteReader())
        {
            while (reader.Read())
            {
                usersId.Add(Convert.ToInt32(reader["id"]));
            }
        }

        foreach (int userId in usersId)
        {
            MySqlCommand deleteUserFromCourseCommand = new MySqlCommand(deleteUserFromCourse, _mySqlConnection);
            deleteUserFromCourseCommand.Parameters.AddWithValue("@user_id", userId);
            deleteUserFromCourseCommand.ExecuteNonQuery();
        }
    }

    private void DeleteCourses(int quantity)
    {
        string query = $"DELETE FROM courses LIMIT {quantity}";
        MySqlCommand command = new MySqlCommand(query, _mySqlConnection);
        command.ExecuteNonQuery();
    }

    public void Run(string method, string type = "", string table = "", string name = "")
    {
        Stopwatch sw = new Stopwatch();
        double[] tempTimes = new double[_numberOfRepeats];
        Dictionary<int, BenchmarkResultCase> benchmarkResultCases = new Dictionary<int, BenchmarkResultCase>();

        Console.WriteLine("Benchmarking \"" + method + "\":");

        foreach (int numberOfRecord in _numberOfRecords)
        {
            var methodArgument = _benchmarkUtils.GetMethodArgumentForMethod(type, table, numberOfRecord);

            for (int i = 0; i < _numberOfRepeats; i++)
            {
                sw.Restart();
                GetType().GetMethod(method, BindingFlags.NonPublic | BindingFlags.Instance)
                    ?.Invoke(this, new[] { methodArgument });
                sw.Stop();
                tempTimes[i] = sw.Elapsed.TotalMilliseconds;

                if (type != "select")
                    _benchmarkUtils.RestoreDatabase();
            }

            tempTimes[0] = tempTimes[1];

            double avgTime = _benchmarkUtils.CalculateAverage(tempTimes);
            double stdTime = _benchmarkUtils.CalculateStandardDeviationTime(tempTimes);

            benchmarkResultCases.Add(numberOfRecord, new BenchmarkResultCase(avgTime, stdTime, 0, new List<string>()));

            Console.WriteLine($" - {numberOfRecord}: avg={avgTime}; std={stdTime}");
        }

        AddBenchmark(name, benchmarkResultCases);
    }

    private void AddBenchmark(string benchmark, Dictionary<int, BenchmarkResultCase> benchmarkResultCases)
    {
        _benchmarks.Add(new BenchmarkResult(benchmark, benchmarkResultCases));
    }

    public async System.Threading.Tasks.Task SendSaveResults()
    {
        string jsonData = JsonSerializer.Serialize(
            new
            {
                orm_name = "C# NO-ORM",
                orm_language = "C#/.NET",
                orm_version = "8.0.2",
                benchmarks = _benchmarks
            }
        );

        HttpClient httpClient = new HttpClient();

        using HttpResponseMessage response = await httpClient.PostAsync(
            $"http://localhost/orm_benchmarking/index.php?save-results",
            new StringContent(jsonData, Encoding.UTF8, "application/json")
        );

        try
        {
            response.EnsureSuccessStatusCode();
            Console.WriteLine("Results saved successfully.");
        }
        catch (HttpRequestException exception)
        {
            Console.WriteLine("Results has not been saved:");
            Console.WriteLine(exception);
        }
    }
}