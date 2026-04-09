<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsActiveToOrganizationMembers extends Migration
{
    public function up(): void
    {
        $this->db->query('
            ALTER TABLE `organization_members`
            ADD COLUMN `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 AFTER `role`
        ');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE `organization_members` DROP COLUMN `is_active`');
    }
}
