<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompaniesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 200],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 200, 'unique' => true],
            'logo'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'website'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'industry'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'size'        => ['type' => 'ENUM', 'constraint' => ['1-10', '11-50', '51-200', '201-500', '500+'], 'null' => true],
            'country'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'city'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'linkedin'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('companies');
    }

    public function down(): void
    {
        $this->forge->dropTable('companies');
    }
}
