<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInterviewsTable extends Migration
{
    public function up(): void
    {
        $this->db->query('
            CREATE TABLE IF NOT EXISTS `interviews` (
                `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `application_id`  INT UNSIGNED NOT NULL,
                `recruiter_id`    INT UNSIGNED NOT NULL,
                `type`            ENUM(\'onsite\',\'remote\') NOT NULL DEFAULT \'onsite\',
                `scheduled_at`    DATETIME NOT NULL,
                `duration_min`    SMALLINT UNSIGNED NOT NULL DEFAULT 60,
                `location`        VARCHAR(500) NULL COMMENT \'Address (onsite) or meeting link (remote)\',
                `notes`           TEXT NULL,
                `status`          ENUM(\'scheduled\',\'done\',\'cancelled\') NOT NULL DEFAULT \'scheduled\',
                `created_at`      DATETIME NULL,
                `updated_at`      DATETIME NULL,
                PRIMARY KEY (`id`),
                KEY `idx_interviews_application` (`application_id`),
                CONSTRAINT `fk_interviews_app`
                    FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT `fk_interviews_recruiter`
                    FOREIGN KEY (`recruiter_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS `interviews`');
    }
}
