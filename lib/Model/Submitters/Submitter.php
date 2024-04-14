<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Submitters;

use mysqli;
use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataObjectProperty;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Option;

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
            'id' => new DataProperty('id', fn() => null, DataProperty::MYSQL_INT),
            'email' => new DataProperty('email', fn() => null, DataProperty::MYSQL_STRING, true),
            'full_name' => new DataProperty('full_name', fn() => null, DataProperty::MYSQL_STRING, true),
            'other_infos' => new DataObjectProperty((object)
            [
                'telephone' => new DataProperty('telephone', fn() => null)
            ], true),
            'password_hash' => new DataProperty(null, fn() => null, DataProperty::MYSQL_STRING),
            'registration_datetime' => new DataProperty(null, fn() => gmdate('Y-m-d H:i:s'), DataProperty::MYSQL_STRING),
            'timezone' => new DataProperty('timezone', fn() => 'America/Sao_Paulo', DataProperty::MYSQL_STRING),
            'lgpd_term_version' => new DataProperty('lgpd_term_version', fn() => null, DataProperty::MYSQL_INT),
            'lgpd_term' => new DataProperty('lgpd_term', fn() => null, DataProperty::MYSQL_STRING),
            'lgpd_term_active' => new DataProperty(null, fn() => 1, DataProperty::MYSQL_INT)
        ];

        parent::__construct($initialValues);
    }

    protected string $databaseTable = 'submitters';
    protected string $formFieldPrefixName = 'submitters';
    protected array $primaryKeys = ['id'];

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
        return 0;
    }
}