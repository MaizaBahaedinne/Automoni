<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run()
    {
        // Get type IDs for reference
        $this->db->table('organization_types')->select('id')->orderBy('id', 'ASC');
        $types = $this->db->get()->getResultArray();
        $companyTypeId = $types[0]['id'] ?? 1;

        $organizations = [
            // Parent organizations
            [
                'type_id' => $companyTypeId,
                'name' => 'Global Tech Solutions',
                'slug' => 'global-tech-solutions-' . time(),
                'description' => 'Leading technology company with offices worldwide.',
                'website' => 'https://www.globaltech.example.com',
                'email' => 'contact@globaltech.example.com',
                'phone' => '+1-555-0100',
                'address' => '123 Tech Boulevard, San Francisco, CA 94105, USA',
                'latitude' => 37.7749,
                'longitude' => -122.4194,
                'industry' => 'Information Technology',
                'employee_count' => 5000,
                'founded_at' => '2010-03-15',
                'status' => 'active',
                'is_verified' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'type_id' => $companyTypeId,
                'name' => 'Innovation Labs Inc',
                'slug' => 'innovation-labs-' . time(),
                'description' => 'Cutting-edge research and development facility.',
                'website' => 'https://www.innovationlabs.example.com',
                'email' => 'info@innovationlabs.example.com',
                'phone' => '+1-555-0101',
                'address' => '456 Research Drive, Boston, MA 02108, USA',
                'latitude' => 42.3601,
                'longitude' => -71.0589,
                'industry' => 'Research & Development',
                'employee_count' => 2500,
                'founded_at' => '2015-07-20',
                'status' => 'active',
                'is_verified' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'type_id' => $companyTypeId,
                'name' => 'Future Ventures',
                'slug' => 'future-ventures-' . time(),
                'description' => 'Venture capital and investment firm.',
                'website' => 'https://www.futureventures.example.com',
                'email' => 'partnerships@futureventures.example.com',
                'phone' => '+1-555-0102',
                'address' => '789 Finance Street, New York, NY 10001, USA',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'industry' => 'Finance & Investment',
                'employee_count' => 150,
                'founded_at' => '2018-01-10',
                'status' => 'active',
                'is_verified' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $insertedIds = [];
        foreach ($organizations as $row) {
            $this->db->table('organizations')->insert($row);
            $insertedIds[] = $this->db->insertID();
        }

        // Add subsidiaries (children of Global Tech Solutions)
        if (!empty($insertedIds)) {
            $parentId = $insertedIds[0];

            $subsidiaries = [
                [
                    'parent_id' => $parentId,
                    'type_id' => $companyTypeId,
                    'name' => 'Global Tech - Europe Division',
                    'slug' => 'global-tech-europe-' . time(),
                    'description' => 'European operations of Global Tech Solutions.',
                    'website' => 'https://www.globaltech-eu.example.com',
                    'email' => 'eu@globaltech.example.com',
                    'phone' => '+33-1-xxxx-xxxx',
                    'address' => 'La Défense, Paris, France',
                    'latitude' => 48.8920,
                    'longitude' => 2.2379,
                    'industry' => 'Information Technology',
                    'employee_count' => 1500,
                    'founded_at' => '2012-06-01',
                    'status' => 'active',
                    'is_verified' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'parent_id' => $parentId,
                    'type_id' => $companyTypeId,
                    'name' => 'Global Tech - Asia Pacific',
                    'slug' => 'global-tech-apac-' . time(),
                    'description' => 'Asia-Pacific operations of Global Tech Solutions.',
                    'website' => 'https://www.globaltech-apac.example.com',
                    'email' => 'apac@globaltech.example.com',
                    'phone' => '+65-xxxx-xxxx',
                    'address' => 'Changi Business Park, Singapore',
                    'latitude' => 1.3521,
                    'longitude' => 103.8198,
                    'industry' => 'Information Technology',
                    'employee_count' => 2000,
                    'founded_at' => '2014-09-15',
                    'status' => 'active',
                    'is_verified' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ];

            foreach ($subsidiaries as $row) {
                $this->db->table('organizations')->insert($row);
            }
        }

        echo "Organizations seeded successfully!";
    }
}
