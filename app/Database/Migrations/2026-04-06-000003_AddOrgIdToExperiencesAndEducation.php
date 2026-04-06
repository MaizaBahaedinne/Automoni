<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrgIdToExperiencesAndEducation extends Migration
{
    public function up(): void
    {
        $this->db->query('
            ALTER TABLE experiences
            ADD COLUMN org_id INT UNSIGNED NULL DEFAULT NULL AFTER company,
            ADD CONSTRAINT fk_exp_org
                FOREIGN KEY (org_id) REFERENCES organizations(id)
                ON DELETE SET NULL ON UPDATE CASCADE
        ');

        $this->db->query('
            ALTER TABLE education
            ADD COLUMN org_id INT UNSIGNED NULL DEFAULT NULL AFTER institution,
            ADD CONSTRAINT fk_edu_org
                FOREIGN KEY (org_id) REFERENCES organizations(id)
                ON DELETE SET NULL ON UPDATE CASCADE
        ');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE experiences DROP FOREIGN KEY fk_exp_org');
        $this->db->query('ALTER TABLE experiences DROP COLUMN org_id');
        $this->db->query('ALTER TABLE education DROP FOREIGN KEY fk_edu_org');
        $this->db->query('ALTER TABLE education DROP COLUMN org_id');
    }
}
