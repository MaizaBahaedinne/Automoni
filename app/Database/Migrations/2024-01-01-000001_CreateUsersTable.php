<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'first_name'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'last_name'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'password'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'           => ['type' => 'ENUM', 'constraint' => ['job_seeker', 'recruiter', 'admin'], 'default' => 'job_seeker'],
            'avatar'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'email_verified' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'status'         => ['type' => 'ENUM', 'constraint' => ['active', 'inactive', 'banned'], 'default' => 'active'],
            'remember_token' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('role');
        $this->forge->createTable('users');
    }

    public function down(): void
    {
        $this->forge->dropTable('users');
    }
}
