@REM npm install
@REM cd php\eloquent
@REM composer install

cd php\eloquent
vendor\bin\phinx rollback -t 0 & vendor\bin\phinx migrate & php DatabaseSeeder.php & php generateRandomInstances.php
