<?php

namespace App\Models;

use CodeIgniter\Model;

class PostReactionModel extends Model
{
    protected $table      = 'post_reactions';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields    = ['post_id', 'user_id', 'reaction_type'];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    /**
     * Toggle reaction. Returns true if added, false if removed.
     */
    public function toggle(int $postId, int $userId, string $type = 'like'): bool
    {
        $existing = $this->where('post_id', $postId)->where('user_id', $userId)->first();
        if ($existing) {
            $this->delete($existing->id);
            $this->db->query(
                'UPDATE posts SET reactions_count = GREATEST(0, reactions_count - 1) WHERE id = ?',
                [$postId]
            );
            return false;
        }
        $this->insert(['post_id' => $postId, 'user_id' => $userId, 'reaction_type' => $type]);
        $this->db->query(
            'UPDATE posts SET reactions_count = reactions_count + 1 WHERE id = ?',
            [$postId]
        );
        return true;
    }
}
