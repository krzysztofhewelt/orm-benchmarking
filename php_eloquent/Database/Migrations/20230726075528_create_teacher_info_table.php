<?php
declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Database\Migrations\Migration;

final class CreateTeacherInfoTable extends Migration
{
    public function up() : void
    {
        $this->schema->create('teacher_info', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->string('scien_degree', 50);
            $table->string('business_email', 255);
            $table->string('contact_number', 20)->nullable();
            $table->string('room', 20)->nullable();
            $table->string('consultation_hours', 255)->nullable();
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down() : void
    {
        $this->schema->dropIfExists('teacher_info');
    }
}
