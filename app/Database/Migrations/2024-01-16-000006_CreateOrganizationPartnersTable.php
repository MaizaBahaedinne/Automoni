<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrganizationPartnersTable extends Migration
{
    public function up(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `organization_partners` (
                `id`               INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `organization_id`  INT(11) UNSIGNED NOT NULL,
                `partner_id`       INT(11) UNSIGNED NOT NULL,
                `partnership_type` VARCHAR(100) NULL,
                `description`      TEXT NULL,
                `started_at`       DATE NULL,
                `ended_at`         DATE NULL,
                `is_active`        TINYINT(1) NOT NULL DEFAULT 1,
                `created_at`       DATETIME NULL,
                `updated_at`       DATETIME NULL,
                PRIMARY KEY (`id`),
                KEY `organization_id` (`organization_id`),
                KEY `partner_id` (`partner_id`),
                KEY `uk_org_partner` (`organization_id`, `partner_id`),
                CONSTRAINT `fk_org_partners_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT `fk_org_partners_partner` FOREIGN KEY (`partner_id`) REFERENCES `organizations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->db->query("DROP TABLE IF EXISTS `organization_partners`;");
    }
}
