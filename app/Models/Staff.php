<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;
    protected $table = 'staff';

    public static function rules($id = null)
    {
        $rules = [
            'name' => 'required|string|max:191',
            'email' => 'required|string|max:191|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:191|unique:users,phone,' . $id,
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password',
            'status' => 'required|in:Active,Inactive',
        ];

        return $rules;
    }
    public static function updateRules($id = null)
    {
        $rules = [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'status' => 'required|in:Active,Inactive',
        ];

        return $rules;
    }
    public static function staffStausUpdateRules()
    {
        $rules = [
            'id' => 'required|string|exists:users,id',
            'status' => 'required|in:Active,Inactive',
        ];

        return $rules;
    }
}
