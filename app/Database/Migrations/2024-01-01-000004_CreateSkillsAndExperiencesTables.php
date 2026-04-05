<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSkillsAndExperiencesTables extends Migration
{
    public function up(): void
    {
        // Skills reference table
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'category'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('skills');

        // User ↔ Skill pivot
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'skill_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'skill_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'level'      => ['type' => 'ENUM', 'constraint' => ['beginner', 'intermediate', 'advanced', 'expert'], 'default' => 'intermediate'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'skill_name']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_skills');

        // Experiences
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 200],
            'company'     => ['type' => 'VARCHAR', 'constraint' => 200],
            'location'    => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'contract'    => ['type' => 'ENUM', 'constraint' => ['CDI', 'CDD', 'Freelance', 'Internship', 'PartTime'], 'null' => true],
            'start_date'  => ['type' => 'DATE'],
            'end_date'    => ['type' => 'DATE', 'null' => true],
            'is_current'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'description' => ['type' => 'TEXT', 'null' => true],
            'sort_order'  => ['type' => 'TINYINT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('experiences');

        // Education
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'      => ['type' => 'INT', 'unsigned' => true],
            'degree'       => ['type' => 'VARCHAR', 'constraint' => 200],
            'field'        => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'institution'  => ['type' => 'VARCHAR', 'constraint' => 200],
            'location'     => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'start_year'   => ['type' => 'YEAR'],
            'end_year'     => ['type' => 'YEAR', 'null' => true],
            'is_current'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'description'  => ['type' => 'TEXT', 'null' => true],
            'sort_order'   => ['type' => 'TINYINT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('education');
    }

    public function down(): void
    {
        $this->forge->dropTable('education');
        $this->forge->dropTable('experiences');
        $this->forge->dropTable('user_skills');
        $this->forge->dropTable('skills');
    }
}
