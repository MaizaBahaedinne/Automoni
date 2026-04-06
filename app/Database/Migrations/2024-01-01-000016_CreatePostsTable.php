<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostsTable extends Migration
{
    public function up(): void
    {
        $this->db->query('
            CREATE TABLE IF NOT EXISTS `posts` (
                `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id`              INT UNSIGNED NOT NULL,
                `type`                 VARCHAR(20) NOT NULL DEFAULT \'text\',
                `content`              TEXT,
                `media_file`           VARCHAR(255) DEFAULT NULL,
                `video_url`            VARCHAR(500) DEFAULT NULL,
                `announcement_subtype` VARCHAR(30) DEFAULT NULL,
                `reactions_count`      INT UNSIGNED NOT NULL DEFAULT 0,
                `comments_count`       INT UNSIGNED NOT NULL DEFAULT 0,
                `created_at`           DATETIME DEFAULT NULL,
                `updated_at`           DATETIME DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ');
    }

    public function down(): void
    {
        $this->forge->dropTable('posts', true);
    }
}
