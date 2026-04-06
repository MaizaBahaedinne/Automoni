<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserConnectionsTable extends Migration
{
    public function up(): void
    {
        $this->db->query('
            CREATE TABLE IF NOT EXISTS user_connections (
                id           INT UNSIGNED AUTO_INCREMENT,
                requester_id INT UNSIGNED NOT NULL,
                receiver_id  INT UNSIGNED NOT NULL,
                status       ENUM(\'pending\',\'accepted\',\'rejected\') NOT NULL DEFAULT \'pending\',
                created_at   DATETIME NULL,
                updated_at   DATETIME NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_connection (requester_id, receiver_id),
                CONSTRAINT fk_conn_requester FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE,
                CONSTRAINT fk_conn_receiver  FOREIGN KEY (receiver_id)  REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS user_connections');
    }
}
