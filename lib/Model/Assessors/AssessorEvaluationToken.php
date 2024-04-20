<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Assessors;

use DateTime;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Option;

/**
 * @property Option id
 * @property Option token
 * @property Option article_id
 * @property Option assessor_name
 * @property Option assessor_email
 */
class AssessorEvaluationToken extends DataEntity
{
    public function __construct(?array $initialValues = null)
    {
        $this->properties = (object)
        [
            'id' => new DataProperty(null, fn() => null, DataProperty::MYSQL_INT),
            'token' => new DataProperty(null, fn() => null, DataProperty::MYSQL_STRING),
            'article_id' => new DataProperty('article_id', fn() => null, DataProperty::MYSQL_INT),
            'assessor_name' => new DataProperty('assessor_name', fn() => '', DataProperty::MYSQL_STRING),
            'assessor_email' => new DataProperty('assessor_email', fn() => '', DataProperty::MYSQL_STRING)
        ];
        parent::__construct($initialValues);
    }

    protected string $databaseTable = 'assessors_evaluation_tokens';
    protected string $formFieldPrefixName = 'assessors_evaluation_tokens';
    protected array $primaryKeys = ['id'];

    public function generateToken() : self
    {
        $this->token = Option::some(Uuid::uuid7(new DateTime('now', new DateTimeZone('UTC')))->toString());
        return $this;
    }
}