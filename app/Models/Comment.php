<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'user_id',
        'post_id',
    ];

    protected $appends = [
        'formatted_created_at'
    ];

    /**
     * @param $query
     * @param string $pattern
     * @return mixed
     */
    public function scopeSearchPattern($query, $pattern = '')
    {
        if (strlen($pattern) > 0) {
            return $query->whereHas('post', fn ($q) => $q->where(DB::raw('LOWER(post_title)'), 'like', '%' . strtolower($pattern) . '%'))
                ->orWhereHas('user', fn ($q) => $q->where(DB::raw('LOWER(name)'), 'like', '%' . strtolower($pattern) . '%'))
                ->orWhereHas('user', fn ($q) => $q->where(DB::raw('LOWER(email)'), 'like', '%' . strtolower($pattern) . '%'))
                ->orWhere(DB::raw('LOWER(body)'), 'like', '%' . strtolower($pattern) . '%');
        } else {
            return $query;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }
}
