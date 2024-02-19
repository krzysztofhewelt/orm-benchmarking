<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up() : void
    {
        $this->schema->create('users', function (Blueprint $table) {
            $table
                ->id()
                ->autoIncrement()
                ->index();
            $table->string('name', 45);
            $table->string('surname', 45);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->string('account_role', 20);
            $table->boolean('active');
        });
    }

    public function down() : void
    {
        $this->schema->dropIfExists('users');
    }
}
