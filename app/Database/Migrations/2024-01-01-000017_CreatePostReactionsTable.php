<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostReactionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'post_id'       => ['type' => 'INT', 'unsigned' => true],
            'user_id'       => ['type' => 'INT', 'unsigned' => true],
            'reaction_type' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'like'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['post_id', 'user_id']);
        $this->forge->addKey('post_id');
        $this->forge->createTable('post_reactions');
    }

    public function down(): void
    {
        $this->forge->dropTable('post_reactions', true);
    }
}
