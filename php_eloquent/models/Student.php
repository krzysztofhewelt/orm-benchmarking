<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Student
 *
 * @mixin Builder
 */
class Student extends Model
{
	use HasFactory;

	protected $table = 'student_info';
	protected $primaryKey = 'user_ID';
	public $timestamps = false;
	protected $guarded = [];
	protected $hidden = ['pivot'];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_ID', 'id');
	}
}
