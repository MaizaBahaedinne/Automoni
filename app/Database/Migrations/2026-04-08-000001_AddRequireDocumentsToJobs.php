<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRequireDocumentsToJobs extends Migration
{
    public function up(): void
    {
        $this->db->query('
            ALTER TABLE jobs
                ADD COLUMN IF NOT EXISTS require_cv           TINYINT(1) NOT NULL DEFAULT 1 AFTER visibility_level,
                ADD COLUMN IF NOT EXISTS require_cover_letter TINYINT(1) NOT NULL DEFAULT 0 AFTER require_cv
        ');
    }

    public function down(): void
    {
        $this->db->query('
            ALTER TABLE jobs
                DROP COLUMN IF EXISTS require_cv,
                DROP COLUMN IF EXISTS require_cover_letter
        ');
    }
}
