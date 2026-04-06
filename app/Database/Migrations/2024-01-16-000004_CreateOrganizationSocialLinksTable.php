<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrganizationSocialLinksTable extends Migration
{
    public function up(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `organization_social_links` (
                `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `organization_id` INT(11) UNSIGNED NOT NULL,
                `platform`        VARCHAR(50) NOT NULL,
                `url`             VARCHAR(500) NOT NULL,
                `created_at`      DATETIME NULL,
                `updated_at`      DATETIME NULL,
                PRIMARY KEY (`id`),
                KEY `organization_id` (`organization_id`),
                CONSTRAINT `fk_org_social_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->db->query("DROP TABLE IF EXISTS `organization_social_links`;");
    }
}
