<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradingRecord extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
      'user_id',
      'trading_item',
      'trading_cost',
      'memo',
  ];
}
