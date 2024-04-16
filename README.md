# ORM Benchmarking (in development)

**ORM's:**
* C# (.NET):
  * Entity Framework
  * raw SQL
* PHP:
  * Eloquent
  * raw SQL (PDO)
* JavaScript:
  * Sequelize
  * raw SQL

# Database
We are simulating an LMS (Learning Management System). There are simplified database based on [Learnin](https://github.com/krzysztofhewelt/learnin), my LMS implementation.

![database.png](screenshots/database.png)

# Benchmark cases
1. Select queries:
* Select n first users
* Select n students and their courses order by surname
* For n students select tasks to do (with course information)
2. Insert queries:
* Insert n users with additional information (student or teacher) using transaction
* Insert n courses
3. Update queries:
* For n courses prolong available to date
4. Delete queries:
* For n users take them out of courses
* Delete n courses

# Results
_not done yet..._

# Analysis
_not done yet..._
