<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrganizationsTable extends Migration
{
    public function up(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `organizations` (
                `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `parent_id`      INT(11) UNSIGNED NULL,
                `type_id`        INT(11) UNSIGNED NOT NULL,
                `name`           VARCHAR(255) NOT NULL,
                `slug`           VARCHAR(255) NOT NULL,
                `description`    LONGTEXT NULL,
                `logo`           VARCHAR(255) NULL,
                `website`        VARCHAR(255) NULL,
                `phone`          VARCHAR(20) NULL,
                `email`          VARCHAR(255) NULL,
                `address`        TEXT NULL,
                `latitude`       DECIMAL(10,8) NULL,
                `longitude`      DECIMAL(11,8) NULL,
                `employee_count` INT(11) NULL,
                `industry`       VARCHAR(100) NULL,
                `founded_at`     DATE NULL,
                `status`         ENUM('active','inactive','archived') NOT NULL DEFAULT 'active',
                `is_verified`    TINYINT(1) NOT NULL DEFAULT 0,
                `deleted_at`     DATETIME NULL,
                `created_at`     DATETIME NULL,
                `updated_at`     DATETIME NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `slug` (`slug`),
                KEY `type_id` (`type_id`),
                KEY `parent_id` (`parent_id`),
                KEY `status` (`status`),
                CONSTRAINT `fk_orgs_type` FOREIGN KEY (`type_id`) REFERENCES `organization_types` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT `fk_orgs_parent` FOREIGN KEY (`parent_id`) REFERENCES `organizations` (`id`) ON UPDATE CASCADE ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS `organizations`;');
    }
}