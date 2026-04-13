<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up(): void
    {
        $this->db->query('
            CREATE TABLE IF NOT EXISTS notifications (
                id         INT UNSIGNED AUTO_INCREMENT,
                user_id    INT UNSIGNED NOT NULL,
                type       VARCHAR(64)  NOT NULL DEFAULT \'info\',
                title      VARCHAR(255) NOT NULL,
                body       TEXT         NULL,
                link       VARCHAR(500) NULL,
                is_read    TINYINT(1)   NOT NULL DEFAULT 0,
                created_at DATETIME     NULL,
                PRIMARY KEY (id),
                KEY idx_notif_user_read (user_id, is_read),
                CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS notifications');
    }
}
