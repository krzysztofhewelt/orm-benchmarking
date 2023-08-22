<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Teacher
 *
 * @mixin Builder
 */
class Teacher extends Model
{
	protected $table = 'teacher_info';
	protected $primaryKey = 'user_ID';
	public $timestamps = false;
	protected $hidden = ['pivot'];

	protected $guarded = [];

	public function user() : BelongsTo
	{
		return $this->belongsTo(User::class, 'user_ID', 'id');
	}
}
