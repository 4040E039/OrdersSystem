<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RaiseOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
      'restaurant_id',
      'user_id',
      'raise_order_token',
      'raise_order_theme',
      'start_time',
      'end_time',
      'memo',
      'is_found'
  ];
}
