<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Database\Migrations\Migration;

final class CreateStudentInfoTable extends Migration
{
    public function up() : void
    {
        $this->schema->create('student_info', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->string('field_of_study', 50);
            $table->integer('semester');
            $table->string('year_of_study', 10);
            $table->string('mode_of_study', 20);
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down() : void
    {
        $this->schema->dropIfExists('student_info');
    }
}
