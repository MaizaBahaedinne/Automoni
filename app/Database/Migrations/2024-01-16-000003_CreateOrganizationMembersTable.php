<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrganizationMembersTable extends Migration
{
    public function up(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `organization_members` (
                `id`              INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `organization_id` INT(11) UNSIGNED NOT NULL,
                `user_id`         INT(11) UNSIGNED NOT NULL,
                `role`            ENUM('owner','manager','viewer') NOT NULL DEFAULT 'viewer',
                `joined_at`       DATETIME NULL,
                `created_at`      DATETIME NULL,
                `updated_at`      DATETIME NULL,
                PRIMARY KEY (`id`),
                KEY `uk_org_user` (`organization_id`, `user_id`),
                CONSTRAINT `fk_org_members_org` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT `fk_org_members_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->db->query("DROP TABLE IF EXISTS `organization_members`;");
    }
}
