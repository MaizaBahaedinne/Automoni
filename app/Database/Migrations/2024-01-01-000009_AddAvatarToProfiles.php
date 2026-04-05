<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAvatarToProfiles extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('profiles', [
            'avatar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'completeness',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('profiles', 'avatar');
    }
}
