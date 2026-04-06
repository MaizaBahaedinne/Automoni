<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostCommentsTable extends Migration
{
    public function up(): void
    {
        $this->db->query('
            CREATE TABLE IF NOT EXISTS `post_comments` (
                `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `post_id`    INT UNSIGNED NOT NULL,
                `user_id`    INT UNSIGNED NOT NULL,
                `content`    TEXT NOT NULL,
                `created_at` DATETIME DEFAULT NULL,
                `updated_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `post_id` (`post_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ');
    }

    public function down(): void
    {
        $this->forge->dropTable('post_comments', true);
    }
}
