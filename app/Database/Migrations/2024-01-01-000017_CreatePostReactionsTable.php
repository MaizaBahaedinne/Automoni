<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostReactionsTable extends Migration
{
    public function up(): void
    {
        $this->db->query('
            CREATE TABLE IF NOT EXISTS `post_reactions` (
                `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `post_id`       INT UNSIGNED NOT NULL,
                `user_id`       INT UNSIGNED NOT NULL,
                `reaction_type` VARCHAR(20) NOT NULL DEFAULT \'like\',
                `created_at`    DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `post_user` (`post_id`, `user_id`),
                KEY `post_id` (`post_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ');
    }

    public function down(): void
    {
        $this->forge->dropTable('post_reactions', true);
    }
}
