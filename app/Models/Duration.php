<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duration extends Model
{
    use HasFactory;
    protected $fillable = [
        'time_duration',
        'time_unit',
        'status',
    ];
    public static function rules($id = null)
    {
        $rules = [
            'time_duration' => 'required|string|min:1|max:60',
            'time_unit' => 'required|string|in:Minutes,Hours',
            'status' => 'required|in:Active,Inactive',
        ];

        return $rules;
    }

}
