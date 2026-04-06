<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostCommentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'post_id'    => ['type' => 'INT', 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'content'    => ['type' => 'TEXT'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('post_id');
        $this->forge->createTable('post_comments');
    }

    public function down(): void
    {
        $this->forge->dropTable('post_comments', true);
    }
}
