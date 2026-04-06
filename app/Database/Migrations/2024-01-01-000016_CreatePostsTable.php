<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePostsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                   => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'              => ['type' => 'INT', 'unsigned' => true],
            'type'                 => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'text'],
            'content'              => ['type' => 'TEXT', 'null' => true, 'default' => null],
            'media_file'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
            'video_url'            => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'default' => null],
            'announcement_subtype' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'default' => null],
            'reactions_count'      => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'comments_count'       => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
            'updated_at'           => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('created_at');
        $this->forge->createTable('posts');
    }

    public function down(): void
    {
        $this->forge->dropTable('posts', true);
    }
}
