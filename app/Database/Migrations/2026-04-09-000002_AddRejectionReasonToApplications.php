<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRejectionReasonToApplications extends Migration
{
    public function up(): void
    {
        $this->db->query('
            ALTER TABLE applications
                ADD COLUMN IF NOT EXISTS rejection_reason TEXT NULL AFTER recruiter_note
        ');
    }

    public function down(): void
    {
        $this->db->query('
            ALTER TABLE applications
                DROP COLUMN IF EXISTS rejection_reason
        ');
    }
}
