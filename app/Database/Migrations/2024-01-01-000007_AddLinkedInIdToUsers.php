<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLinkedInIdToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'linkedin_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'remember_token',
            ],
        ]);

        // Index for fast lookup on login
        $this->db->query('ALTER TABLE users ADD INDEX idx_linkedin_id (linkedin_id)');
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'linkedin_id');
    }
}
