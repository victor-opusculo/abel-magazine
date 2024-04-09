<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialSchema extends AbstractMigration
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
        $administrators = $this->table('administrators');
        $administrators
        ->addColumn('email', 'string', [ 'limit' => 140, 'null' => false ])
        ->addColumn('full_name', 'string', [ 'limit' => 140, 'null' => true ])
        ->addColumn('password_hash', 'string', [ 'limit' => 280, 'null' => false ])
        ->addColumn('timezone', 'string', [ 'limit' => 100, 'null' => false ])
        ->addIndex('email', [ 'unique' => true ])
        ->create();

        $media = $this->table("media");
        $media
        ->addColumn('name', 'string', [ 'limit' => 140, 'null' => false ])
        ->addColumn('description', 'text')
        ->addColumn('file_extension', 'string', [ 'limit' => 140, 'null' => false ])
        ->addIndex([ 'name', 'description' ], [ 'type' => 'fulltext' ])
        ->create();

        $submitters = $this->table('submitters');
        $submitters
        ->addColumn('email', 'varbinary', [ 'limit' => 400, 'null' => false ])
        ->addColumn('full_name', 'varbinary', [ 'limit' => 400, 'null' => false ])
        ->addColumn('other_infos', 'varbinary', [ 'limit' => 3000, 'null' => false ])
        ->addColumn('password_hash', 'string', [ 'limit' => 280, 'null' => false ])
        ->addColumn('registration_datetime', 'datetime', [ 'null' => false ])
        ->addColumn('timezone', 'string', [ 'limit' => 100, 'null' => false ])
        ->addIndex('email', [ 'unique' => true, 'limit' => 50 ])
        ->create();

        $magazines = $this->table('magazines');
        $magazines
        ->addColumn('name', 'string', [ 'limit' => 140, 'null' => false ])
        ->addColumn('description', 'text', [ 'null' => true ])
        ->addColumn('cover_image_media_id', 'integer', [ 'signed' => false, 'null' => true ])
        ->addColumn('string_identifier', 'string', [ 'limit' => 140, 'null' => false ])
        ->addForeignKey('cover_image_media_id', 'media', [ 'id' ], [ 'delete' => 'SET_NULL', 'update' => 'CASCADE' ])
        ->addIndex([ 'name', 'description', 'string_identifier' ], [ 'type' => 'fulltext' ])
        ->addIndex('string_identifier', [ 'unique' => true ])
        ->create();

        $editions = $this->table('editions');
        $editions
        ->addColumn('magazine_id', 'integer', [ 'signed' => false, 'null' => false ])
        ->addColumn('ref_date', 'date', [ 'null' => false ])
        ->addColumn('edition_label', 'string', [ 'limit' => 140, 'null' => false ])
        ->addColumn('title', 'string', [ 'limit' => 140, 'null' => true ])
        ->addColumn('description', 'text', [ 'null' => true ])
        ->addColumn('is_published', 'boolean', [ 'null' => false ])
        ->addColumn('is_open_for_submissions', 'boolean', [ 'null' => false ])
        ->addForeignKey('magazine_id', 'magazines', ['id'], [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
        ->addIndex([ 'title', 'description', 'edition_label' ], [ 'type' => 'fulltext' ])
        ->create();

        $articles = $this->table('articles');
        $articles
        ->addColumn('edition_id', 'integer', [ 'signed' => false, 'null' => false ])
        ->addColumn('submitter_id', 'integer', [ 'signed' => false, 'null' => true ])
        ->addColumn('title', 'string', [ 'limit' => 140, 'null' => false ])
        ->addColumn('resume', 'text')
        ->addColumn('keywords', 'string', [ 'limit' => 140 ])
        ->addColumn('language', 'string', [ 'limit' => 50, 'null' => false ])
        ->addColumn('status', 'string', [ 'limit' => 140, 'null' => false ])
        ->addColumn('is_approved', 'boolean', [ 'null' => false ])
        ->addColumn('not_idded_file_extension', 'string', [ 'null' => false ])
        ->addColumn('idded_file_extension', 'string', [ 'null' => true ])
        ->addColumn('submission_datetime', 'datetime', [ 'null' => false ])
        ->addForeignKey('edition_id', 'editions', ['id'], [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
        ->addForeignKey('submitter_id', 'submitters', ['id'], [ 'delete' => 'SET_NULL', 'update' => 'CASCADE' ])
        ->addIndex([ 'title', 'resume', 'keywords' ], [ 'type' => 'fulltext' ])
        ->create();

        $assessors_evaluation_tokens = $this->table('assessors_evaluation_tokens');
        $assessors_evaluation_tokens
        ->addColumn('token', 'string', [ 'null' => false, 'limit' => 140 ])
        ->addColumn('article_id', 'integer', [ 'signed' => false, 'null' => true ])
        ->addColumn('assessor_name', 'string', [ 'null' => false, 'limit' => 140 ])
        ->addColumn('assessor_email', 'string', [ 'null' => false, 'limit' => 140 ])
        ->addForeignKey('article_id', 'articles', ['id'], [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
        ->create();

        $assessor_opinions = $this->table('assessors_opinions');
        $assessor_opinions
        ->addColumn('article_id', 'integer', [ 'null' => false, 'signed' => false ])
        ->addColumn('is_approved', 'boolean', [ 'null' => false ])
        ->addColumn('feedback_message', 'text')
        ->addColumn('datetime', 'datetime', [ 'null' => false ])
        ->addColumn('assessor_name', 'string', [ 'null' => false, 'limit' => 140 ])
        ->addColumn('assessor_email', 'string', [ 'null' => false, 'limit' => 140 ])
        ->addForeignKey('article_id', 'articles', ['id'], [ 'delete' => 'CASCADE', 'update' => 'CASCADE' ])
        ->create();
    }
}
