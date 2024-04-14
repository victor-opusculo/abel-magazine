<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class LgpdTermFields extends AbstractMigration
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
    public function up(): void
    {
        $submitters = $this->table('submitters');
        $submitters
        ->addColumn('lgpd_term_version', 'integer', [ 'null' => false ])
        ->addColumn('lgpd_term', 'text', [ 'null' => false ])
        ->addColumn('lgpd_term_active', 'boolean', [ 'null' => false ])
        ->update();

        $settings = $this->table('settings', [ 'id' => false, 'primary_key' => 'name' ]);
        $settings
        ->addColumn('name', 'string', [ 'limit' => 100, 'null' => false ])
        ->addColumn('value', 'text', [ 'null' => false ])
        ->create();

        $settings->insert(
            [
                [ 'name' => 'DEFAULT_LGPD_TERM_VERSION', 'value' => '1' ],
                [ 'name' => 'DEFAULT_LGPD_TERM_TEXT', 'value' => file_get_contents(__DIR__ . '/premade_data/lgpdTerm1.html') ]
            ])
        ->saveData();
    }

    public function down(): void
    {
        $settings = $this->table('settings');
        $settings->drop()->save();

        $submitters = $this->table('submitters');
        $submitters
        ->removeColumn('lgpd_term_version')
        ->removeColumn('lgpd_term')
        ->removeColumn('lgpd_term_active')
        ->update();
    }
}
