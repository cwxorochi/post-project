<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_title',
        'post_body',
        'user_id'
    ];

    protected $appends = [
        'post_id', 'total_number_of_comments'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user ()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return mixed
     */
    public function getPostIdAttribute ()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTotalNumberOfCommentsAttribute ()
    {
        return $this->comments()->count();
    }
}
