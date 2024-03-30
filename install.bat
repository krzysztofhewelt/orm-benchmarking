@REM cd php_eloquent
@REM composer install
@REM
@REM cd ../js_sequelize
@REM npm install

cd php_eloquent
vendor\bin\phinx rollback -t 0 & vendor\bin\phinx migrate & php DatabaseSeeder.php
