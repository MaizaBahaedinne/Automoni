<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicationAnswersTable extends Migration
{
    public function up(): void
    {
        $this->db->query('
            CREATE TABLE IF NOT EXISTS application_answers (
                id             INT UNSIGNED AUTO_INCREMENT,
                application_id INT UNSIGNED NOT NULL,
                question_id    INT UNSIGNED NOT NULL,
                answer_text    TEXT         NOT NULL,
                PRIMARY KEY (id),
                INDEX idx_app_id (application_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS application_answers');
    }
}
