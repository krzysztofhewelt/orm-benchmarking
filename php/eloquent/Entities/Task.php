<?php
declare(strict_types=1);

namespace App\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Task
 *
 * @mixin Builder
 */
class Task extends Model
{
	public $timestamps = false;
	protected $guarded = [];
	protected $hidden = ['pivot'];

	public function course() : BelongsTo
	{
		return $this->belongsTo(Course::class, 'course_id', 'id');
	}
}
