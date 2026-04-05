<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceExperiencesTable extends Migration
{
    public function up(): void
    {
        $fields = [
            'level' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'contract',
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'level',
            ],
            'manager_user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'description',
            ],
            'manager_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'after'      => 'manager_user_id',
            ],
            'skills_gained' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'manager_name',
            ],
        ];

        $this->forge->addColumn('experiences', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('experiences', ['level', 'department', 'manager_user_id', 'manager_name', 'skills_gained']);
    }
}
