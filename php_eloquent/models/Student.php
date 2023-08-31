<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Student
 *
 * @mixin Builder
 */
class Student extends Model
{
	protected $table = 'student_info';
	protected $primaryKey = 'user_id';
	public $timestamps = false;
	protected $guarded = [];
	protected $hidden = ['pivot'];

	public function user() : BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
}
