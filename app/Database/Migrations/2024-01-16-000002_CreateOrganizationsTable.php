<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrganizationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'parent_id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'type_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'              => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'description'       => ['type' => 'LONGTEXT', 'null' => true],
            'logo'              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'website'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'phone'             => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'             => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'address'           => ['type' => 'TEXT', 'null' => true],
            'latitude'          => ['type' => 'DECIMAL', 'constraint' => '10,8', 'null' => true],
            'longitude'         => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => true],
            'employee_count'    => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'industry'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'founded_at'        => ['type' => 'DATE', 'null' => true],
            'status'            => ['type' => 'ENUM', 'constraint' => ['active', 'inactive', 'archived'], 'default' => 'active'],
            'is_verified'       => ['type' => 'BOOLEAN', 'default' => false],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', false, false, 'PRIMARY');
        $this->forge->addKey('type_id');
        $this->forge->addKey('parent_id');
        $this->forge->addKey('slug');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('type_id', 'organization_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('parent_id', 'organizations', 'id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('organizations', true);
    }

    public function down()
    {
        $this->forge->dropTable('organizations', true);
    }
}
