<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProfilesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'          => ['type' => 'INT', 'unsigned' => true],
            'headline'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'summary'          => ['type' => 'TEXT', 'null' => true],
            'phone'            => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'city'             => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'country'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'linkedin'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'github'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'portfolio'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'cv_file'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'cv_original_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'desired_salary'   => ['type' => 'INT', 'null' => true],
            'desired_contract' => ['type' => 'SET', 'constraint' => ['CDI', 'CDD', 'Freelance', 'Internship', 'PartTime'], 'null' => true],
            'desired_location' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'availability'     => ['type' => 'DATE', 'null' => true],
            'completeness'     => ['type' => 'TINYINT', 'constraint' => 3, 'default' => 0],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id', false, false, 'unique');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('profiles');
    }

    public function down(): void
    {
        $this->forge->dropTable('profiles');
    }
}
