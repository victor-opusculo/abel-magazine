<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitialAdministrators extends AbstractMigration
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
        $admins = $this->table('administrators');
        $admins->insert(
            [
                [ 
                    'email' => 'victor.ventura@uol.com.br', 
                    'full_name' => 'Victor Opusculo Oliveira Ventura de Almeida',
                    'password_hash' => password_hash('12345678', PASSWORD_DEFAULT), 
                    'timezone' => 'America/Sao_Paulo' 
                ],
                [ 
                    'email' => 'abel@portalabel.org.br', 
                    'full_name' => 'Associação Brasileira das Escolas do Legislativo e de Contas',
                    'password_hash' => password_hash('12345678', PASSWORD_DEFAULT), 
                    'timezone' => 'America/Sao_Paulo' 
                ],
            ])
        ->saveData();
    }

    public function down(): void
    {
        $admins = $this->table('administrators');
        $admins->truncate();
    }
}
