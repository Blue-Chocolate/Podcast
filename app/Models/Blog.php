<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'header_image',
        'user_id',
        'title',
        'description',
        'content',
        'category',
        'status',
        'publish_date',
        'views',
        'image',
        'announcement',
        'footer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /*
    |--------------------------------------------------------------------------
    | Cached Queries (Safe for Serialization)
    |--------------------------------------------------------------------------
    | We convert collections to arrays before caching to avoid PDO serialization
    | errors. When retrieving, we rehydrate them back into Eloquent collections.
    |--------------------------------------------------------------------------
    */
    public static function cachedAll()
    {
        $cacheKey = 'blogs:all';

        $cached = Cache::remember($cacheKey, now()->addMinutes(10), function () {
            return self::with('user')
                ->orderByDesc('publish_date')
                ->get()
                ->toArray(); // convert to array before caching
        });

        // Rehydrate array back into Eloquent collection
        return self::hydrate($cached);
    }

    public static function cachedPublished()
    {
        $cacheKey = 'blogs:published:with-user';

        $cached = Cache::remember($cacheKey, now()->addMinutes(15), function () {
            return self::published()
                ->with('user')
                ->orderByDesc('publish_date')
                ->get()
                ->toArray();
        });

        return self::hydrate($cached);
    }

    /*
    |--------------------------------------------------------------------------
    | Cache Invalidation
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::saved(fn() => self::clearCache());
        static::deleted(fn() => self::clearCache());
    }

    public static function clearCache()
    {
        if (Cache::supportsTags()) {
            Cache::tags(['blogs'])->flush();
        } else {
            Cache::forget('blogs:all');
            Cache::forget('blogs:published:with-user');
        }
    }
}
