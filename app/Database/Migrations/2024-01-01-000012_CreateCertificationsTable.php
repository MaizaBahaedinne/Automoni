<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCertificationsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'        => ['type' => 'INT', 'unsigned' => true],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'organization'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
            'issue_date'     => ['type' => 'DATE', 'null' => true, 'default' => null],
            'expiry_date'    => ['type' => 'DATE', 'null' => true, 'default' => null],
            'credential_url' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'default' => null],
            'logo_file'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
            'sort_order'     => ['type' => 'INT', 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id');
        $this->forge->createTable('certifications');
    }

    public function down(): void
    {
        $this->forge->dropTable('certifications', true);
    }
}
