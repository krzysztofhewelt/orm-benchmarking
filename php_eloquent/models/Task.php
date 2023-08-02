<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Task
 *
 * @mixin Builder
 */
class Task extends Model
{
	use HasFactory;

	public $timestamps = false;
	protected $guarded = [];
	protected $hidden = ['pivot'];

	public function course(): BelongsTo
	{
		return $this->belongsTo(Course::class, 'course_ID', 'id');
	}
}
