<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrganizationTypesTable extends Migration
{
    public function up(): void
    {
        $this->db->query('
            CREATE TABLE IF NOT EXISTS `organization_types` (
                `id`          INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name`        VARCHAR(100) NOT NULL,
                `slug`        VARCHAR(100) NOT NULL,
                `description` TEXT NULL,
                `created_at`  DATETIME NULL,
                `updated_at`  DATETIME NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ');
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS `organization_types`;');
    }
}
