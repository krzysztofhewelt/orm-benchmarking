npm install && cd php/eloquent && composer install && vendor/bin/phinx rollback -t 0 && vendor/bin/phinx migrate && php DatabaseSeeder.php && php generateRandomInstances.php
