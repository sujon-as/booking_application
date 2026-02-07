<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffService extends Model
{
    use HasFactory;

    protected $table = 'staff_services';

    protected $fillable = [
        'user_id',
        'staff_id',
        'service_id',
        'duration_id',
        'price',
    ];
}
