<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Assessors;

use mysqli;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;
use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\MyOrm\SqlSelector;

/**
 * @property Option<int> id
 * @property Option<int> article_id
 * @property Option<int> is_approved
 * @property Option<string> feedback_message
 * @property Option<string> datetime
 * @property Option<string> assessor_name
 * @property Option<string> assessor_email
 */
class AssessorOpinion extends DataEntity
{
    public function __construct(?array $initialValues = null)
    {
        $this->properties = (object)
        [
            'id' => new DataProperty(null, fn() => null, DataProperty::MYSQL_INT),
            'article_id' => new DataProperty(null, fn() => null, DataProperty::MYSQL_INT),
            'is_approved' => new DataProperty('is_approved', fn() => null, DataProperty::MYSQL_INT),
            'feedback_message' => new DataProperty('feedback_message', fn() => '', DataProperty::MYSQL_STRING),
            'datetime' => new DataProperty(null, fn() => gmdate('Y-m-d H:i:s'), DataProperty::MYSQL_STRING),
            'assessor_name' => new DataProperty('assessor_name', fn() => null, DataProperty::MYSQL_STRING),
            'assessor_email' => new DataProperty('assessor_email', fn() => null, DataProperty::MYSQL_STRING)
        ];

        parent::__construct($initialValues);
    }

    protected string $databaseTable = 'assessors_opinions';
    protected string $formFieldPrefixName = 'assessors_opinions';
    protected array $primaryKeys = ['id'];

    public ?Article $article = null;

    /** @return AssessorOpinion[] */
    public function getAllFromArticle(mysqli $conn) : array
    {
        $selector = $this->getGetSingleSqlSelector()
        ->clearValues()
        ->clearWhereClauses()
        ->addWhereClause("{$this->getWhereQueryColumnName('article_id')} = ?")
        ->addValue('i', $this->article_id->unwrapOr(null));

        $drs = $selector->run($conn, SqlSelector::RETURN_ALL_ASSOC);
        return array_map([ $this, 'newInstanceFromDataRowFromDatabase' ], $drs);
    } 

    public function existsForArticle(mysqli $conn) : bool
    {
        $selector = (new SqlSelector)
        ->addSelectColumn('COUNT(*)')
        ->setTable($this->databaseTable)
        ->addWhereClause("{$this->getWhereQueryColumnName('article_id')} = ?")
        ->addValue('i', $this->article_id->unwrap());

        $count = (int)$selector->run($conn, SqlSelector::RETURN_FIRST_COLUMN_VALUE);
        return $count > 0;
    }

    public function isArticleApproved(mysqli $conn) : bool
    {
        $selector = (new SqlSelector)
        ->addSelectColumn("count(*)")
        ->setTable($this->databaseTable)
        ->addWhereClause("{$this->getWhereQueryColumnName('article_id')} = ?")
        ->addValue('i', $this->article_id->unwrap());

        $countAllOpinions = (int)$selector->run($conn, SqlSelector::RETURN_FIRST_COLUMN_VALUE);

        $selector = (new SqlSelector)
        ->addSelectColumn("count(*)")
        ->setTable($this->databaseTable)
        ->addWhereClause("{$this->getWhereQueryColumnName('article_id')} = ?")
        ->addWhereClause("AND {$this->getWhereQueryColumnName('is_approved')} = 1")
        ->addValue('i', $this->article_id->unwrap());

        $countApproved = (int)$selector->run($conn, SqlSelector::RETURN_FIRST_COLUMN_VALUE);

        return $countAllOpinions === $countApproved;
    }

    public function fetchArticle(mysqli $conn) : self
    {
        $this->article = (new Article([ 'id' => $this->article_id->unwrapOr(null) ]))->getSingle($conn);
        return $this;
    }

    public function beforeDatabaseInsert(mysqli $conn): int
    {
        $this->datetime = Option::some(gmdate('Y-m-d H:i:s'));
        return 0;
    }
}