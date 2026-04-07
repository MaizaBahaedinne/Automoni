<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateError404LogsTable extends Migration
{
    public function up(): void
    {
        $this->db->query('
            CREATE TABLE IF NOT EXISTS error_404_logs (
                id         INT UNSIGNED AUTO_INCREMENT,
                url        VARCHAR(2048)  NOT NULL,
                method     VARCHAR(10)    NOT NULL DEFAULT \'GET\',
                user_id    INT UNSIGNED   NULL,
                ip         VARCHAR(45)    NOT NULL DEFAULT \'\',
                user_agent VARCHAR(512)   NOT NULL DEFAULT \'\',
                referer    VARCHAR(2048)  NOT NULL DEFAULT \'\',
                created_at DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                INDEX idx_created_at (created_at),
                INDEX idx_url (url(255)),
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS error_404_logs');
    }
}
