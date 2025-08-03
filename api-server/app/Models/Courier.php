<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    /**
     * 可被大量賦值的屬性。
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'past_late_count',
        'leave_frequency',
        'avg_delivery_time',
        'rating',
    ];
}
