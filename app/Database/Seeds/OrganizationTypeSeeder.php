<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OrganizationTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Company',
                'slug' => 'company',
                'description' => 'Private or public company',
            ],
            [
                'name' => 'NGO',
                'slug' => 'ngo',
                'description' => 'Non-Governmental Organization',
            ],
            [
                'name' => 'Association',
                'slug' => 'association',
                'description' => 'Association or non-profit organization',
            ],
            [
                'name' => 'Government Agency',
                'slug' => 'government-agency',
                'description' => 'Government institution or public agency',
            ],
            [
                'name' => 'Educational Institution',
                'slug' => 'educational-institution',
                'description' => 'University, school, or training center',
            ],
            [
                'name' => 'Healthcare Organization',
                'slug' => 'healthcare-organization',
                'description' => 'Hospital, clinic, or healthcare provider',
            ],
        ];

        foreach ($data as $row) {
            $row['created_at'] = date('Y-m-d H:i:s');
            $row['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('organization_types')->insert($row);
        }
    }
}
