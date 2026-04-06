<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'company_id'       => ['type' => 'INT', 'unsigned' => true],
            'user_id'          => ['type' => 'INT', 'unsigned' => true],
            'title'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'             => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'description'      => ['type' => 'LONGTEXT'],
            'requirements'     => ['type' => 'TEXT', 'null' => true],
            'benefits'         => ['type' => 'TEXT', 'null' => true],
            'contract_type'    => ['type' => 'ENUM', 'constraint' => ['CDI', 'CDD', 'Freelance', 'Internship', 'PartTime'], 'default' => 'CDI'],
            'location'         => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'remote'           => ['type' => 'ENUM', 'constraint' => ['onsite', 'remote', 'hybrid'], 'default' => 'onsite'],
            'salary_min'       => ['type' => 'INT', 'null' => true],
            'salary_max'       => ['type' => 'INT', 'null' => true],
            'salary_currency'  => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'EUR'],
            'experience_level' => ['type' => 'ENUM', 'constraint' => ['junior', 'mid', 'senior', 'lead', 'any'], 'default' => 'any'],
            'status'           => ['type' => 'ENUM', 'constraint' => ['draft', 'active', 'paused', 'closed'], 'default' => 'active'],
            'views'            => ['type' => 'INT', 'default' => 0],
            'expires_at'       => ['type' => 'DATE', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('status');
        $this->forge->addKey('contract_type');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jobs');

        // Job ↔ Skill pivot
        $this->forge->addField([
            'job_id'     => ['type' => 'INT', 'unsigned' => true],
            'skill_name' => ['type' => 'VARCHAR', 'constraint' => 100],
        ]);
        $this->forge->addKey(['job_id', 'skill_name'], true);
        $this->forge->addForeignKey('job_id', 'jobs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('job_skills');
    }

    public function down(): void
    {
        $this->forge->dropTable('job_skills');
        $this->forge->dropTable('jobs');
    }
}
