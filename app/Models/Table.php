<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Table extends Model
{
    use HasFactory;
    public function scopeFilterBlog(Builder $query, $filters)
    {
        if (!empty($filters['search'])) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($filters['search']) . '%'])
            ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($filters['search']) . '%']);
        }

        return $query;

    }
    protected $table = 'admin_table';
}

