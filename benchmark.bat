@echo off

REM Delete results file
rm results.json

REM Run Eloquent Benchmark
cd php_eloquent
php Benchmark.php

@REM REM Run Sequelize Benchmark
@REM cd ../js_sequelize
@REM node index.js
@REM
@REM REM Run Entity Framework Benchmark
@REM cd ../dotnet_entity_framework
@REM dotnet run
@REM
@REM REM Run Stock Benchmark
@REM cd ../stock_PDO
@REM php Benchmark.php
@REM
@REM REM Show report
@REM echo Benchmarks done successfully
@REM echo opening report.html...
@REM start "report.html"
