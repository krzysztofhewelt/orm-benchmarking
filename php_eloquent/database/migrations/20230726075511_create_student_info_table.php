<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use \MyProject\Database\Migration\Migration;

final class CreateStudentInfoTable extends Migration
{
    public function up()
    {
        $this->schema->create('student_info', function (Blueprint $table) {
            $table->unsignedBigInteger('user_ID');
            $table->string('field_of_study', 50);
            $table->integer('semester');
            $table->string('year_of_study', 10);
            $table->string('mode_of_study', 20);
            $table
                ->foreign('user_ID')
                ->references('id')
                ->on('users')
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
        $this->schema->dropIfExists('student_info');
    }
}
