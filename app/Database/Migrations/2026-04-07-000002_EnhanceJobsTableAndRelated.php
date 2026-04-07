<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceJobsTableAndRelated extends Migration
{
    public function up(): void
    {
        // ── 1. New columns on jobs ─────────────────────────────────────────────
        $this->db->query('
            ALTER TABLE jobs
                ADD COLUMN IF NOT EXISTS internal_ref        VARCHAR(100)  NULL          AFTER id,
                ADD COLUMN IF NOT EXISTS department          VARCHAR(200)  NULL          AFTER internal_ref,
                ADD COLUMN IF NOT EXISTS num_positions       TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER department,
                ADD COLUMN IF NOT EXISTS hierarchical_level  VARCHAR(50)   NULL          AFTER num_positions,
                ADD COLUMN IF NOT EXISTS direct_manager      VARCHAR(200)  NULL          AFTER hierarchical_level,
                ADD COLUMN IF NOT EXISTS min_experience_years TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER experience_level,
                ADD COLUMN IF NOT EXISTS education_level     VARCHAR(100)  NULL          AFTER min_experience_years,
                ADD COLUMN IF NOT EXISTS education_field     VARCHAR(200)  NULL          AFTER education_level,
                ADD COLUMN IF NOT EXISTS mission_context     TEXT          NULL          AFTER description,
                ADD COLUMN IF NOT EXISTS salary_period       ENUM(\'annual\',\'monthly\',\'daily\',\'hourly\') NOT NULL DEFAULT \'annual\' AFTER salary_currency,
                ADD COLUMN IF NOT EXISTS salary_public       TINYINT(1)   NOT NULL DEFAULT 0 AFTER salary_period,
                ADD COLUMN IF NOT EXISTS salary_variable     TINYINT(1)   NOT NULL DEFAULT 0 AFTER salary_public,
                ADD COLUMN IF NOT EXISTS salary_bonus_pct    TINYINT UNSIGNED NULL      AFTER salary_variable,
                ADD COLUMN IF NOT EXISTS recruitment_notes   TEXT          NULL          AFTER benefits,
                ADD COLUMN IF NOT EXISTS internal_only       TINYINT(1)   NOT NULL DEFAULT 0 AFTER recruitment_notes,
                ADD COLUMN IF NOT EXISTS visibility_level    ENUM(\'public\',\'logged_in\',\'apply_only\') NOT NULL DEFAULT \'public\' AFTER internal_only
        ');

        // ── 2. job_languages ──────────────────────────────────────────────────
        $this->db->query('
            CREATE TABLE IF NOT EXISTS job_languages (
                id         INT UNSIGNED AUTO_INCREMENT,
                job_id     INT UNSIGNED NOT NULL,
                language   VARCHAR(100) NOT NULL,
                level_code VARCHAR(10)  NOT NULL,
                is_required TINYINT(1)  NOT NULL DEFAULT 1,
                PRIMARY KEY (id),
                INDEX idx_job_id (job_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // ── 3. job_certifications ─────────────────────────────────────────────
        $this->db->query('
            CREATE TABLE IF NOT EXISTS job_certifications (
                id                  INT UNSIGNED AUTO_INCREMENT,
                job_id              INT UNSIGNED NOT NULL,
                certification_name  VARCHAR(500) NOT NULL,
                is_required         TINYINT(1)   NOT NULL DEFAULT 0,
                delay_months        TINYINT UNSIGNED NULL,
                PRIMARY KEY (id),
                INDEX idx_job_id (job_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // ── 4. job_prescreening_questions ─────────────────────────────────────
        $this->db->query('
            CREATE TABLE IF NOT EXISTS job_prescreening_questions (
                id               INT UNSIGNED AUTO_INCREMENT,
                job_id           INT UNSIGNED NOT NULL,
                sort_order       TINYINT UNSIGNED NOT NULL DEFAULT 0,
                question_text    TEXT         NOT NULL,
                question_type    ENUM(\'yes_no\',\'text\',\'number\') NOT NULL DEFAULT \'yes_no\',
                expected_answer  VARCHAR(500) NULL,
                is_eliminatory   TINYINT(1)   NOT NULL DEFAULT 0,
                PRIMARY KEY (id),
                INDEX idx_job_id (job_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // ── 5. job_recruitment_steps ──────────────────────────────────────────
        $this->db->query('
            CREATE TABLE IF NOT EXISTS job_recruitment_steps (
                id             INT UNSIGNED AUTO_INCREMENT,
                job_id         INT UNSIGNED NOT NULL,
                step_order     TINYINT UNSIGNED NOT NULL DEFAULT 0,
                step_name      VARCHAR(200) NOT NULL,
                description    TEXT         NULL,
                responsible    VARCHAR(200) NULL,
                duration_days  TINYINT UNSIGNED NULL,
                PRIMARY KEY (id),
                INDEX idx_job_id (job_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS job_recruitment_steps');
        $this->db->query('DROP TABLE IF EXISTS job_prescreening_questions');
        $this->db->query('DROP TABLE IF EXISTS job_certifications');
        $this->db->query('DROP TABLE IF EXISTS job_languages');
        // Removing added columns (best-effort — MariaDB may ignore IF EXISTS here)
        $this->db->query('
            ALTER TABLE jobs
                DROP COLUMN IF EXISTS internal_ref,
                DROP COLUMN IF EXISTS department,
                DROP COLUMN IF EXISTS num_positions,
                DROP COLUMN IF EXISTS hierarchical_level,
                DROP COLUMN IF EXISTS direct_manager,
                DROP COLUMN IF EXISTS min_experience_years,
                DROP COLUMN IF EXISTS education_level,
                DROP COLUMN IF EXISTS education_field,
                DROP COLUMN IF EXISTS mission_context,
                DROP COLUMN IF EXISTS salary_period,
                DROP COLUMN IF EXISTS salary_public,
                DROP COLUMN IF EXISTS salary_variable,
                DROP COLUMN IF EXISTS salary_bonus_pct,
                DROP COLUMN IF EXISTS recruitment_notes,
                DROP COLUMN IF EXISTS internal_only,
                DROP COLUMN IF EXISTS visibility_level
        ');
    }
}
