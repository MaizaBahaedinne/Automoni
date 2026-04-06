<?php

namespace App\Models;

use CodeIgniter\Model;

class PostCommentModel extends Model
{
    protected $table      = 'post_comments';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = ['post_id', 'user_id', 'content'];
    protected $useTimestamps = true;

    public function getForPost(int $postId, int $limit = 20): array
    {
        return $this
            ->select('post_comments.*, users.first_name, users.last_name, profiles.avatar')
            ->join('users', 'users.id = post_comments.user_id')
            ->join('profiles', 'profiles.user_id = post_comments.user_id', 'left')
            ->where('post_comments.post_id', $postId)
            ->orderBy('post_comments.created_at', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    public function addComment(int $postId, int $userId, string $content): void
    {
        $this->db->transStart();
        $this->insert(['post_id' => $postId, 'user_id' => $userId, 'content' => $content]);
        $this->db->query(
            'UPDATE posts SET comments_count = comments_count + 1 WHERE id = ?',
            [$postId]
        );
        $this->db->transComplete();
    }
}
