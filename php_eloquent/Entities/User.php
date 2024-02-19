<?php
declare(strict_types=1);

namespace App\Entities;

use Illuminate\Database\Eloquent\Builder;
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
	public $timestamps = false;

	protected $guarded = [''];

	protected $hidden = ['password', 'pivot'];

	public function courses() : BelongsToMany
	{
		return $this->belongsToMany(Course::class, 'course_enrollments', 'user_id', 'course_id');
	}

	public function student() : HasMany
	{
		return $this->hasMany(Student::class, 'user_id', 'id');
	}

	public function teacher() : HasOne
	{
		return $this->hasOne(Teacher::class, 'user_id', 'id');
	}
}
