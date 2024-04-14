<?php
declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;
use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration {
    public Builder $schema;

    public function init() : void
    {
        $this->schema = Capsule::schema();
    }
}
