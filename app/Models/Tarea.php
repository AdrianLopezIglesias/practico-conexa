<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model {
	use HasFactory;

	protected $fillable = [
		'texto',
	];
	public $timestamps = false;
	public function latest($column = 'id') {
		return $this->orderBy($column, 'desc');
	}
}
