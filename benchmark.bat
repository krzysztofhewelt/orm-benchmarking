@REM INSTALL DEPENDENCIES, SEED DATABASE, GENERATE RANDOM INSTANCES
@REM call install.bat

@echo off

@REM RUN ORM BENCHMARKS
echo ---------------------------------------
echo Running Eloquent benchmark
echo ---------------------------------------
cd php\eloquent
php Benchmark.php

echo ---------------------------------------
echo Running Sequelize benchmark
echo ---------------------------------------
cd ..\..\js\sequelize
node benchmark.js

echo ---------------------------------------
echo Running Entity Framework benchmark + No ORM
echo ---------------------------------------
cd ..\..\dotnet\entity-framework_no-orm
dotnet run

@REM RUN NON-ORM BENCHMARKS
echo ---------------------------------------
echo No ORM PHP benchmark
echo ---------------------------------------
cd ..\..\php\no-orm
php Benchmark.php

echo ---------------------------------------
echo No ORM JavaScript benchmark
echo ---------------------------------------
cd ..\..\js\no-orm
node benchmark.js

echo Benchmark done! Check the report.
echo Benchmark done! Check the report.
echo Benchmark done! Check the report.