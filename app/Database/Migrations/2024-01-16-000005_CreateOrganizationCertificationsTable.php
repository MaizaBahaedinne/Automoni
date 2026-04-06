<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrganizationCertificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'organization_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'issuer'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'issued_at'       => ['type' => 'DATE', 'null' => true],
            'expires_at'      => ['type' => 'DATE', 'null' => true],
            'url'             => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', false, false, 'PRIMARY');
        $this->forge->addKey('organization_id');
        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('organization_certifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('organization_certifications', true);
    }
}
