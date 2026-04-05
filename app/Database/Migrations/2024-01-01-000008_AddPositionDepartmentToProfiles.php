<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPositionDepartmentToProfiles extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('profiles', [
            'position'   => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'headline',
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
                'after'      => 'position',
            ],
            'phone_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => null,
                'after'      => 'country',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('profiles', ['position', 'department', 'phone_code']);
    }
}
