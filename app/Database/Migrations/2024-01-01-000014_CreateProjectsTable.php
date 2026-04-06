<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'start_date'  => ['type' => 'DATE', 'null' => true, 'default' => null],
            'end_date'    => ['type' => 'DATE', 'null' => true, 'default' => null],
            'is_current'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'description' => ['type' => 'TEXT', 'null' => true, 'default' => null],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('projects');

        // Project team members (many-to-many: project ↔ user)
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'project_id' => ['type' => 'INT', 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['project_id', 'user_id']);
        $this->forge->createTable('project_members');
    }

    public function down(): void
    {
        $this->forge->dropTable('project_members', true);
        $this->forge->dropTable('projects', true);
    }
}
