<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PagesTable extends AbstractMigration
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
        $pages = $this->table('pages');
        $pages
        ->addColumn('title', 'string', [ 'limit' => 140, 'null' => false ])
        ->addColumn('content', 'text')
        ->addColumn('html_enabled', 'boolean', [ 'default' => 0 ])
        ->addColumn('is_published', 'boolean', [ 'default' => 1 ])
        ->addIndex([ 'title', 'content' ], [ 'type' => 'fulltext' ])
        ->create();

        $settings = $this->table('settings');
        $settings
        ->changeColumn('value', 'text', [ 'null' => true ])
        ->update();

        $settings->insert(
            [
                'name' => 'SUBMISSION_RULES_PAGE_ID',
                'value' => null
            ]
        )
        ->saveData();
    }

    public function down(): void
    {
        $pages = $this->table('pages');
        $pages->drop()->save();

        $settings = $this->table('settings');
        $settings
        ->changeColumn('value', 'text', [ 'null' => false ])
        ->update();

        $this->execute("DELETE from `settings` WHERE `name` = 'SUBMISSION_RULES_PAGE_ID'");
    }
}
