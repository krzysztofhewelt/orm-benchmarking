<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use \MyProject\Database\Migration\Migration;

final class CreateTeacherInfoTable extends Migration
{
    public function up()
    {
        $this->schema->create('teacher_info', function (Blueprint $table) {
            $table->unsignedBigInteger('user_ID')->primary();
            $table->string('scien_degree', 50);
            $table->string('business_email', 255);
            $table->string('contact_number', 20)->nullable();
            $table->string('room', 20)->nullable();
            $table->string('consultation_hours', 255)->nullable();
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
        $this->schema->dropIfExists('teacher_info');
    }
}
