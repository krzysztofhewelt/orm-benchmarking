<?php
declare(strict_types=1);

use Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

final class CreateCourseEnrollmentsTable extends Migration
{
    public function up() : void
    {
        $this->schema->create('course_enrollments', function (Blueprint $table) {
            $table
                ->id()
                ->autoIncrement()
                ->index();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table
                ->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');
        });
    }

    public function down() : void
    {
        $this->schema->dropIfExists('course_enrollments');
    }
}
