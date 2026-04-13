<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrganizationIdToJobs extends Migration
{
    public function up(): void
    {
        $this->db->query('
            ALTER TABLE `jobs`
            ADD COLUMN `organization_id` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `company_id`,
            ADD KEY `idx_jobs_organization_id` (`organization_id`)
        ');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE `jobs` DROP COLUMN `organization_id`');
    }
}
