<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use \MyProject\Database\Migration\Migration;

final class CreateTasksTable extends Migration
{
    public function up()
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
            $table->unsignedBigInteger('course_ID');
            $table
                ->foreign('course_ID')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->dropIfExists('tasks');
    }
}
