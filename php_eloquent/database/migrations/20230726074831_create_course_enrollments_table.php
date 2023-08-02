<?php
declare(strict_types=1);

use \MyProject\Database\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;

final class CreateCourseEnrollmentsTable extends Migration
{
    public function up()
    {
        $this->schema->create('course_enrollments', function (Blueprint $table) {
            $table
                ->id()
                ->autoIncrement()
                ->index();
            $table->unsignedBigInteger('user_ID');
            $table->unsignedBigInteger('course_ID');
            $table
                ->foreign('user_ID')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table
                ->foreign('course_ID')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        $this->schema->dropIfExists('course_enrollments');
    }
}
