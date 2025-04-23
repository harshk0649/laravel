<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['user_id', 'file_path', 'description']; // Allow mass assignment for user_id, file_path, and description
}
