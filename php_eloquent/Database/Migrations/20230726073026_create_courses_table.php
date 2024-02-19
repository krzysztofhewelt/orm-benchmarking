<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Database\Migrations\Migration;

final class CreateCoursesTable extends Migration
{
    public function up() : void
    {
        $this->schema->create('courses', function (Blueprint $table) {
            $table
                ->id()
                ->autoIncrement()
                ->index();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->dateTime('available_from');
            $table->dateTime('available_to')->nullable();
        });
    }

    public function down() : void
    {
        $this->schema->dropIfExists('courses');
    }
}
