<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Database\Migrations\Migration;

final class CreateTasksTable extends Migration
{
    public function up() : void
    {
        $this->schema->create('tasks', function (Blueprint $table) {
            $table
                ->id()
                ->autoIncrement()
                ->index();
            $table->string('name', 100);
            $table->text('description');
            $table->dateTime('available_from');
            $table->dateTime('available_to')->nullable();
            $table->float('max_points');
            $table->unsignedBigInteger('course_id');
            $table
                ->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');
        });
    }

    public function down() : void
    {
        $this->schema->dropIfExists('tasks');
    }
}
