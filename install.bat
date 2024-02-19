cd php_eloquent
composer install

cd ../js_sequelize
npm install

cd ../php_eloquent
vendor/bin/phinx rollback -t 0
vendor/bin/phinx migrate
