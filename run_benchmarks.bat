@REM INSTALL DEPENDENCIES, SEED DATABASE, GENERATE RANDOM INSTANCES
call install.bat

@REM RUN ORM BENCHMARKS
php php\eloquent\Benchmark.php
node js\sequelize\benchmark.js
dotnet run dotnet\entity-framework

@REM RUN NON-ORM BENCHMARKS
php php\no-orm\Benchmark.php
node js\no-orm\benchmark.js
dotnet run dotnet\no-orm

@REM RUN LOCAL NODE SERVER FOR REPORT
npm index.js
start "" http://localhost:8000"