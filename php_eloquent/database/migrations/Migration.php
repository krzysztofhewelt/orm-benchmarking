<?php

namespace MyProject\Database\Migration;

require "bootstrap.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;
use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration {
    public Builder $schema;

    public function init()
    {
        $this->schema = Capsule::schema();
    }
}
