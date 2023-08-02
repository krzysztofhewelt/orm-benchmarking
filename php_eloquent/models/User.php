<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

/**
 * User
 *
 * @mixin Builder
 */
class User extends Model
{
	use HasFactory;

	public $timestamps = false;

	protected $guarded = [''];

	protected $hidden = ['password', 'pivot'];

	protected $casts = [
		'email_verified_at' => 'datetime',
		'last_success_login' => 'datetime',
		'last_wrong_login' => 'datetime',
	];

	/*
	 * Relations
	 */

	public function courses(): BelongsToMany
	{
		return $this->belongsToMany(Course::class, 'course_enrollments', 'user_ID', 'course_ID');
	}

	public function student(): HasMany
	{
		return $this->hasMany(Student::class, 'user_ID', 'id');
	}

	public function teacher(): HasOne
	{
		return $this->hasOne(Teacher::class, 'user_ID', 'id');
	}
}
