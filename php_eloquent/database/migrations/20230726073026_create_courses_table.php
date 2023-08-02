<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use \MyProject\Database\Migration\Migration;

final class CreateCoursesTable extends Migration
{
    public function up()
    {
        $this->schema->create('courses', function (Blueprint $table) {
            $table
                ->id()
                ->autoIncrement()
                ->index();
            $table->string('name', 60);
            $table->text('description')->nullable();
            $table->dateTime('available_from');
            $table->dateTime('available_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema->dropIfExists('courses');
    }
}
