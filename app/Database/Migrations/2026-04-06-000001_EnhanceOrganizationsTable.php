<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceOrganizationsTable extends Migration
{
    public function up()
    {
        // Add new columns for better address breakdown
        $this->forge->addColumn('organizations', [
            'street_address'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'address'],
            'city'              => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'street_address'],
            'postal_code'       => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'city'],
            'country'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'postal_code'],
            'country_code'      => ['type' => 'CHAR', 'constraint' => 2, 'null' => true, 'after' => 'country'],
            'phone_country_code' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true, 'after' => 'phone'],
            'phone_number'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'phone_country_code'],
            'tax_id'            => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'phone_number'],
            'legal_name'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'name'],
            'sectors'           => ['type' => 'JSON', 'null' => true, 'after' => 'industry'],
            'map_link'          => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'after' => 'longitude'],
        ]);

        // Add indexes for better query performance
        $this->forge->addKey('city');
        $this->forge->addKey('country');
        $this->forge->addKey('country_code');
    }

    public function down()
    {
        // Remove added columns
        $this->forge->dropColumn('organizations', ['street_address', 'city', 'postal_code', 'country', 'country_code', 'phone_country_code', 'phone_number', 'tax_id', 'legal_name', 'sectors', 'map_link']);
    }
}
