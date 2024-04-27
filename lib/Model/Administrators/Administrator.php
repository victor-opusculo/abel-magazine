<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Administrators;

use Exception;
use mysqli;
use VictorOpusculo\AbelMagazine\Lib\Model\Exceptions\EmailNotFound;
use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\MyOrm\SqlSelector;

/** @property \VictorOpusculo\MyOrm\Option id
 * @property \VictorOpusculo\MyOrm\Option email
 * @property \VictorOpusculo\MyOrm\Option full_name
 * @property \VictorOpusculo\MyOrm\Option password_hash
 * @property \VictorOpusculo\MyOrm\Option timezone
 */
class Administrator extends DataEntity
{
    public function __construct(?array $initialValues = null)
    {
        $this->properties = (object)
        [
            'id' => new DataProperty('id', fn() => null, DataProperty::MYSQL_INT),
            'email' => new DataProperty('email', fn() => null, DataProperty::MYSQL_STRING),
            'full_name' => new DataProperty('full_name', fn() => null, DataProperty::MYSQL_STRING),
            'password_hash' => new DataProperty(null, fn() => null, DataProperty::MYSQL_STRING),
            'timezone' => new DataProperty('timezone', fn() => 'America/Sao_Paulo', DataProperty::MYSQL_STRING)
        ];

        parent::__construct($initialValues);
    }

    protected string $databaseTable = 'administrators';
    protected string $formFieldPrefixName = 'administrators';
    protected array $primaryKeys = ['id'];

    public function verifyPasswords(string $givenPassword) : bool
    {
        if ($this->password_hash->unwrapOr(false))
            return password_verify($givenPassword, $this->password_hash->unwrap());
        else
            throw new Exception("Hash de senha não carregado!");
    }

    public function hashPassword(string $newPassword) : void
    {
        if (mb_strlen($newPassword) >= 5)
            $this->password_hash = Option::some(password_hash($newPassword, PASSWORD_DEFAULT));
        else
            throw new Exception("Senha nova muito curta! Deve haver pelo menos 5 caracteres.");
    }

    public function getSingleByEmail(mysqli $conn) : self
    {
        $selector = $this->getGetSingleSqlSelector()
        ->clearWhereClauses()
        ->clearValues()
        ->addWhereClause("{$this->databaseTable}.email = ?")
        ->addValue('s', $this->email->unwrapOr('n@a'));

        $dr = $selector->run($conn, SqlSelector::RETURN_SINGLE_ASSOC);
        if (isset($dr))
            return $this->newInstanceFromDataRowFromDatabase($dr);
        else
            throw new EmailNotFound($this->email->unwrapOr('n@a'));
    }

}