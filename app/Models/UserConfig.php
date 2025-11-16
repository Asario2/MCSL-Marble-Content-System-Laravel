<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConfig extends Model
{
    use HasFactory;

    protected $table = 'users_config'; // Tabellenname in DB
    protected $fillable = ['users_id'];
}
