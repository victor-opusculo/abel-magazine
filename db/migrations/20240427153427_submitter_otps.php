<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class SubmitterOtps extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $submitter_otps = $this->table('submitter_otps');
        $submitter_otps
        ->addColumn('submitter_id', 'integer', [ 'signed' => false, 'null' => false])
        ->addColumn('otp', 'string', [ 'limit' => 100, 'null' => false ])
        ->addColumn('expiry_datetime', 'datetime', [ 'null' => false ])
        ->addForeignKey('submitter_id', 'submitters', ['id'], [ 'update' => 'CASCADE', 'delete' => 'CASCADE' ])
        ->create();
    }
}
