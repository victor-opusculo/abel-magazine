<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Assessors;

use DateTime;
use DateTimeZone;
use mysqli;
use Ramsey\Uuid\Uuid;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Exceptions\DatabaseEntityNotFound;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\MyOrm\SqlSelector;

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

    public ?Article $article = null;

    public function generateToken() : self
    {
        $this->token = Option::some(Uuid::uuid7(new DateTime('now', new DateTimeZone('UTC')))->toString());
        return $this;
    }

    public function getAllFromArticle(mysqli $conn) : array
    {
        $selector = $this->getGetSingleSqlSelector()
        ->clearValues()
        ->clearWhereClauses()
        ->addWhereClause("{$this->getWhereQueryColumnName('article_id')} = ?")
        ->addValue('i', $this->article_id->unwrapOr(0));

        $drs = $selector->run($conn, SqlSelector::RETURN_ALL_ASSOC);
        return array_map([ $this, 'newInstanceFromDataRowFromDatabase'], $drs);
    }

    public function existsForArticle(mysqli $conn) : bool
    {
        $selector = (new SqlSelector)
        ->addSelectColumn("COUNT(*)")
        ->setTable($this->databaseTable)
        ->addWhereClause("{$this->getWhereQueryColumnName('article_id')} = ?")
        ->addValue('i', $this->article_id->unwrapOr(0));

        $count = (int)$selector->run($conn, SqlSelector::RETURN_FIRST_COLUMN_VALUE);
        return $count > 0;
    }

    public function getSingleFromToken(mysqli $conn) : self
    {
        $selector = $this->getGetSingleSqlSelector()
        ->clearValues()
        ->clearWhereClauses()
        ->addWhereClause("{$this->getWhereQueryColumnName('token')} = ?")
        ->addValue('s', $this->token->unwrapOr(null));

        $dr = $selector->run($conn, SqlSelector::RETURN_SINGLE_ASSOC);
        if (isset($dr))
            return $this->newInstanceFromDataRowFromDatabase($dr);
        else
            throw new DatabaseEntityNotFound(I18n::get('exceptions.tokenNotFound'), $this->databaseTable);
    }

    public function fetchArticle(mysqli $conn) : self
    {
        $this->article = (new Article([ 'id' => $this->article_id->unwrapOr(0) ]))->getSingle($conn);
        return $this;
    }

}