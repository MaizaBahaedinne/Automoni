<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVolunteeringTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'      => ['type' => 'INT', 'unsigned' => true],
            'organization' => ['type' => 'VARCHAR', 'constraint' => 255],
            'position'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
            'start_date'   => ['type' => 'DATE', 'null' => true, 'default' => null],
            'end_date'     => ['type' => 'DATE', 'null' => true, 'default' => null],
            'is_current'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'description'  => ['type' => 'TEXT', 'null' => true, 'default' => null],
            'sort_order'   => ['type' => 'INT', 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id');
        $this->forge->createTable('volunteering');
    }

    public function down(): void
    {
        $this->forge->dropTable('volunteering', true);
    }
}
