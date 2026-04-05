<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicationsAndAlertsTable extends Migration
{
    public function up(): void
    {
        // Applications
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'job_id'       => ['type' => 'INT', 'unsigned' => true],
            'user_id'      => ['type' => 'INT', 'unsigned' => true],
            'cover_letter' => ['type' => 'TEXT', 'null' => true],
            'cv_file'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'       => ['type' => 'ENUM', 'constraint' => ['pending', 'reviewed', 'shortlisted', 'rejected', 'hired'], 'default' => 'pending'],
            'recruiter_note' => ['type' => 'TEXT', 'null' => true],
            'applied_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['job_id', 'user_id'], false, false, 'unique_application');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('job_id', 'jobs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('applications');

        // Job Alerts
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'       => ['type' => 'INT', 'unsigned' => true],
            'keywords'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'location'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'contract_type' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'frequency'     => ['type' => 'ENUM', 'constraint' => ['instant', 'daily', 'weekly'], 'default' => 'daily'],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'last_sent'     => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'is_active']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('job_alerts');

        // Saved / Bookmarked Jobs
        $this->forge->addField([
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'job_id'     => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey(['user_id', 'job_id'], true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('job_id', 'jobs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('saved_jobs');
    }

    public function down(): void
    {
        $this->forge->dropTable('saved_jobs');
        $this->forge->dropTable('job_alerts');
        $this->forge->dropTable('applications');
    }
}
