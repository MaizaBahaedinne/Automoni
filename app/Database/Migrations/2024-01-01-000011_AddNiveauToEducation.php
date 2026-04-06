<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNiveauToEducation extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('education', [
            'niveau' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'degree',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('education', 'niveau');
    }
}
