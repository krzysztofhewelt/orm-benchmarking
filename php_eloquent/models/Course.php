<?php

namespace App;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * Course
 *
 * @mixin Builder
 */
class Course extends Model
{
	use HasFactory;

	protected $table = 'courses';
	public $timestamps = false;

	protected $fillable = ['name', 'description', 'available_from', 'available_to'];

	protected $hidden = ['pivot'];

	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'course_enrollments', 'course_ID', 'user_ID')
			->orderByDesc('account_role')
			->orderBy('surname');
	}

	public function tasks(): HasMany
	{
		return $this->hasMany(Task::class, 'course_ID', 'id');
	}
}
