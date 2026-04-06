<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table      = 'posts';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [
        'user_id', 'type', 'content', 'media_file', 'video_url',
        'announcement_subtype', 'reactions_count', 'comments_count',
    ];
    protected $useTimestamps = true;

    public function getFeed(int $limit = 15, int $offset = 0): array
    {
        return $this
            ->select('posts.*, users.first_name, users.last_name, profiles.avatar, profiles.headline, profiles.position as user_position')
            ->join('users', 'users.id = posts.user_id')
            ->join('profiles', 'profiles.user_id = posts.user_id', 'left')
            ->orderBy('posts.created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }
}
