REM Delete results file
rm results.json

REM Run Eloquent Benchmark
cd php_eloquent
php -r Benchmark.php

REM Run Sequelize Benchmark
cd ../js_sequelize
node index.js

REM Run Entity Framework Benchmark
cd ../dotnet_entity_framework
dotnet run

REM Run Stock Benchmark
cd ../stock_PDO
php -r Benchmark.php

REM Show report
echo Benchmarks done successfully
echo opening report.html...
start "report.html"
