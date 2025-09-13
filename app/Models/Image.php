<?php
// app/Models/Image.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Image extends Model
{
    // falls du mehrere DB-Verbindungen hast, sonst weglassen
    protected $connection = 'mysql';          // oder 'mariadb'

    protected $table      = 'images';

    // Füllbare Felder (optional)
    protected $fillable   = [
        'shortname',
        'heading_alt',
        'camera_id',
        'image_categories_id',
        'pub',
        'created_at',
    ];

    /* -------------------------------------------------
     *  Beziehungen
     * ------------------------------------------------- */
    // public function camera()
    // {
    // //    return $this->belongsTo(Camera::class);
    // }

    public function category()
    {
        return $this->belongsTo(ImageCategory::class, 'image_categories_id');
    }

    /* -------------------------------------------------
     *  Scope: Standard-Filter (z. B. Suchbegriff)
     * ------------------------------------------------- */
    public function scopeFilterDefault(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $s = $filters['search'];

            $query->where(function ($q) use ($s) {
                $q->where('name',      'like', "%{$s}%")
                  ->orWhere('message',  'like', "%{$s}%")
                  ->orWhere('images.created_at',  'like', "%{$s}%");
            });
        }

        return $query;
    }
    public function scopePublished($query)
    {
        return $query->whereIn('pub', [1, 2]);
    }
}
