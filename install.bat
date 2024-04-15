@REM cd php\eloquent
@REM composer install
@REM
@REM cd ..\js\sequelize
@REM npm install

cd php\eloquent
vendor\bin\phinx rollback -t 0 & vendor\bin\phinx migrate & php DatabaseSeeder.php
