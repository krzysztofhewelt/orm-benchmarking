<?php
declare(strict_types=1);

namespace App\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Course
 *
 * @mixin Builder
 */
class Course extends Model
{
	protected $table = 'courses';
	public $timestamps = false;

	protected $fillable = ['name', 'description', 'available_from', 'available_to'];

	protected $hidden = ['pivot'];

	public function users() : BelongsToMany
	{
		return $this->belongsToMany(User::class, 'course_enrollments', 'course_id', 'user_id')
			->orderByDesc('account_role')
			->orderBy('surname');
	}

	public function tasks() : HasMany
	{
		return $this->hasMany(Task::class, 'course_id', 'id');
	}
}
