<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrganizationSocialLinksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'organization_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'platform'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'url'             => ['type' => 'VARCHAR', 'constraint' => 500],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', false, false, 'PRIMARY');
        $this->forge->addKey('organization_id');
        $this->forge->addForeignKey('organization_id', 'organizations', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('organization_social_links', true);
    }

    public function down()
    {
        $this->forge->dropTable('organization_social_links', true);
    }
}
