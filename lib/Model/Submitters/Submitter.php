<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Submitters;

use mysqli;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataObjectProperty;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Exceptions\DatabaseEntityNotFound;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\MyOrm\SqlSelector;

/**
 * @property Option<int> id
 * @property Option<string> email
 * @property Option<string> full_name
 * @property Option<object{
 *      telephone: Option<string|null>
 * }> other_infos
 * @property Option<string> password_hash
 * @property Option<string> registration_datetime
 * @property Option<string> timezone
 * @property Option<int> lgpd_term_version
 * @property Option<string> lgpd_term
 * @property Option<int> lgpd_term_active
 */
class Submitter extends DataEntity
{
    public function __construct(?array $initialValues = null)
    {
        $this->properties = (object)
        [
            'id' => new DataProperty(null, fn() => null, DataProperty::MYSQL_INT),
            'email' => new DataProperty('email', fn() => null, DataProperty::MYSQL_STRING, true),
            'full_name' => new DataProperty('fullname', fn() => null, DataProperty::MYSQL_STRING, true),
            'other_infos' => new DataObjectProperty((object)
            [
                'telephone' => new DataProperty('telephone', fn() => null)
            ], true),
            'password_hash' => new DataProperty(null, fn() => null, DataProperty::MYSQL_STRING),
            'registration_datetime' => new DataProperty(null, fn() => gmdate('Y-m-d H:i:s'), DataProperty::MYSQL_STRING),
            'timezone' => new DataProperty('timezone', fn() => 'America/Sao_Paulo', DataProperty::MYSQL_STRING),
            'lgpd_term_version' => new DataProperty('lgpdtermversion', fn() => null, DataProperty::MYSQL_INT),
            'lgpd_term' => new DataProperty('lgpd_term', fn() => null, DataProperty::MYSQL_STRING),
            'lgpd_term_active' => new DataProperty(null, fn() => 1, DataProperty::MYSQL_INT)
        ];

        parent::__construct($initialValues);
    }

    protected string $databaseTable = 'submitters';
    protected string $formFieldPrefixName = 'submitters';
    protected array $primaryKeys = ['id'];

    public function getSingleFromEmail(mysqli $conn) : self
    {
        $selector = $this->getGetSingleSqlSelector()
        ->clearValues()
        ->clearWhereClauses()
        ->addWhereClause("{$this->getWhereQueryColumnName('email')} = ?")
        ->addValue('s', $this->email->unwrapOr(''));

        $dr = $selector->run($conn, SqlSelector::RETURN_SINGLE_ASSOC);
        if (isset($dr))
            return $this->newInstanceFromDataRowFromDatabase($dr);
        else
            throw new DatabaseEntityNotFound(I18n::get('exceptions.emailNotFound'), $this->databaseTable);
    }

    public function existsEmailWithAnotherId(mysqli $conn) : bool
    {
        $selector = (new SqlSelector)
        ->setTable($this->databaseTable)
        ->addSelectColumn("COUNT(*)")
        ->addWhereClause("{$this->getWhereQueryColumnName('email')} = ?")
        ->addWhereClause("AND {$this->getWhereQueryColumnName('id')} != ?")
        ->addValue('s', $this->email->unwrapOr(null))
        ->addValue('s', $this->id->unwrapOr(null));

        $count = (int)$selector->run($conn, SqlSelector::RETURN_FIRST_COLUMN_VALUE);
        return $count > 0;
    }

    public function hashPassword(string $newPassword) : self
    {
        $this->password_hash = Option::some(password_hash($newPassword, PASSWORD_DEFAULT));
        return $this;
    }

    public function verifyPassword(string $givenPassword) : bool
    {
        return password_verify($givenPassword, $this->password_hash->unwrapOr('***'));
    }

    public function revokeLgpdTerm() : self
    {
        $this->lgpd_term_active = Option::some(0);
        return $this;
    }

    public function beforeDatabaseInsert(mysqli $conn): int
    {
        $this->registration_datetime = Option::some(gmdate('Y-m-d H:i:s'));
        $this->lgpd_term_active = Option::some(1);
        return 0;
    }
}