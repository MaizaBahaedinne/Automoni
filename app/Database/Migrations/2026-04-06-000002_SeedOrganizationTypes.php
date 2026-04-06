<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedOrganizationTypes extends Migration
{
    public function up(): void
    {
        $now = date('Y-m-d H:i:s');

        $types = [
            ['name' => 'Company',                  'slug' => 'company',                  'description' => 'Private or public company'],
            ['name' => 'NGO',                      'slug' => 'ngo',                      'description' => 'Non-Governmental Organization'],
            ['name' => 'Association',              'slug' => 'association',              'description' => 'Association or non-profit organization'],
            ['name' => 'Government Agency',        'slug' => 'government-agency',        'description' => 'Government institution or public agency'],
            ['name' => 'Educational Institution',  'slug' => 'educational-institution',  'description' => 'University, school, or training center'],
            ['name' => 'Healthcare Organization',  'slug' => 'healthcare-organization',  'description' => 'Hospital, clinic, or healthcare provider'],
        ];

        foreach ($types as $type) {
            $this->db->query(
                "INSERT IGNORE INTO `organization_types` (`name`, `slug`, `description`, `created_at`, `updated_at`)
                 VALUES (?, ?, ?, ?, ?)",
                [$type['name'], $type['slug'], $type['description'], $now, $now]
            );
        }
    }

    public function down(): void
    {
        $this->db->query("DELETE FROM `organization_types` WHERE `slug` IN (
            'company', 'ngo', 'association', 'government-agency',
            'educational-institution', 'healthcare-organization'
        )");
    }
}
