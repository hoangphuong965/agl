<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class keyword extends Model
{
    use HasFactory;

    protected $table = "keywords";

    protected $fillable = ['No', 'key_word', 'title', 'url'];
}
