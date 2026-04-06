<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrganizationPartnersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'organization_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'partner_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'partnership_type'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'description'        => ['type' => 'TEXT', 'null' => true],
            'started_at'         => ['type' => 'DATE', 'null' => true],
            'ended_at'           => ['type' => 'DATE', 'null' => true],
            'is_active'          => ['type' => 'BOOLEAN', 'default' => true],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', false, false, 'PRIMARY');
        $this->forge->addKey('organization_id');
        $this->forge->addKey('partner_id');
        $this->forge->addKey(['organization_id', 'partner_id'], false, false, 'uk_org_partner');
        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('partner_id', 'organizations', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('organization_partners', true);
    }

    public function down()
    {
        $this->forge->dropTable('organization_partners', true);
    }
}
