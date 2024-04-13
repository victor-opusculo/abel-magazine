<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Magazines;

use mysqli;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Exceptions\DatabaseEntityNotFound;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\MyOrm\SqlSelector;

/**
 * @property Option id
 * @property Option magazine_id
 * @property Option ref_date
 * @property Option edition_label
 * @property Option title
 * @property Option description
 * @property Option is_published
 * @property Option is_open_for_submissions
 * @property Option deleted_at
 */
class Edition extends DataEntity
{
    public function __construct(?array $initialValues = null)
    {
        $this->properties = (object)
        [
            'id' => new DataProperty('id', fn() => null, DataProperty::MYSQL_INT),
            'magazine_id' => new DataProperty('magazine_id', fn() => null, DataProperty::MYSQL_INT),
            'ref_date' => new DataProperty('ref_date', fn() => null, DataProperty::MYSQL_STRING),
            'edition_label' => new DataProperty('edition_label', fn() => null, DataProperty::MYSQL_STRING),
            'title' => new DataProperty('title', fn() => 'Sem nome', DataProperty::MYSQL_STRING),
            'description' => new DataProperty('description', fn() => '', DataProperty::MYSQL_STRING),
            'is_published' => new DataProperty('is_published', fn() => 1, DataProperty::MYSQL_INT),
            'is_open_for_submissions' => new DataProperty('is_open_for_submissions', fn() => 0, DataProperty::MYSQL_INT),
            'deleted_at' => new DataProperty('deleted_at', fn() => null, DataProperty::MYSQL_STRING)
        ];

        $this->properties->is_published->valueTransformer = [\VictorOpusculo\AbelMagazine\Lib\Helpers\Data::class, 'booleanTransformer'];
        $this->properties->is_published->is_open_for_submissions = [\VictorOpusculo\AbelMagazine\Lib\Helpers\Data::class, 'booleanTransformer'];

        parent::__construct($initialValues);
    }

    protected string $databaseTable = 'editions';
    protected string $formFieldPrefixName = 'editions';
    protected array $primaryKeys = ['id'];

    public bool $includeSoftDeleted = false;


    public function getSingle(mysqli $conn): static
    {
        $selector = $this->getGetSingleSqlSelector();

        if (!$this->includeSoftDeleted)
            $selector->addWhereClause("AND {$this->getWhereQueryColumnName('deleted_at')} IS NULL ");

        $dr = $selector->run($conn, SqlSelector::RETURN_SINGLE_ASSOC);

        if (isset($dr))
            return $this->newInstanceFromDataRowFromDatabase($dr);
        else
            throw new DatabaseEntityNotFound(I18n::get('exceptions.editionNotFound'), $this->databaseTable);
    }

    public function getCount(mysqli $conn, string $searchKeywords) : int
    {
        $selector = (new SqlSelector)
        ->addSelectColumn("COUNT(*)")
        ->setTable($this->databaseTable);

        if (mb_strlen($searchKeywords) > 3)
            $selector
            ->addWhereClause("MATCH (title, description, edition_label) AGAINST (?)")
            ->addValue('s', $searchKeywords);

        if (!$this->includeSoftDeleted)
        {
            $selector = $selector->hasWhereClauses()
                ?   $selector->addWhereClause("AND {$this->getWhereQueryColumnName('deleted_at')} IS NULL ")
                :   $selector->addWhereClause("{$this->getWhereQueryColumnName('deleted_at')} IS NULL ");
        }

        $count = (int)$selector->run($conn, SqlSelector::RETURN_FIRST_COLUMN_VALUE);
        return $count;
    }

    /**
     * @return Edition[]
     */
    public function getMultiple(mysqli $conn, string $searchKeywords, string $orderBy, int $pageNum, int $numResultsOnPage) : array
    {
        $selector = $this->getGetSingleSqlSelector()
        ->clearValues()
        ->clearWhereClauses();

        if (mb_strlen($searchKeywords) > 3)
            $selector
            ->addWhereClause("MATCH (title, description, edition_label) AGAINST (?)")
            ->addValue('s', $searchKeywords);

        if (!$this->includeSoftDeleted)
        {
            $selector = $selector->hasWhereClauses()
                ?   $selector->addWhereClause("AND {$this->getWhereQueryColumnName('deleted_at')} IS NULL ")
                :   $selector->addWhereClause("{$this->getWhereQueryColumnName('deleted_at')} IS NULL ");
        }

        $selector->setOrderBy(match($orderBy)
        {
            'title' => 'title ASC',
            'ref_date' => 'ref_date DESC',
            'id' => 'id DESC',
            default => 'ref_date DESC'
        });

        $calcPage = ($pageNum - 1) * $numResultsOnPage;
        $selector
        ->setLimit('?, ?')
        ->addValues('ii', [ $calcPage, $numResultsOnPage ]);

        $drs = $selector->run($conn, SqlSelector::RETURN_ALL_ASSOC);
        return array_map([ $this, 'newInstanceFromDataRowFromDatabase' ], $drs);
    }

    public function getCountFromMagazine(mysqli $conn, string $searchKeywords) : int
    {
        $selector = (new SqlSelector)
        ->addSelectColumn("COUNT(*)")
        ->setTable($this->databaseTable)
        ->addWhereClause("{$this->getWhereQueryColumnName('magazine_id')} = ?")
        ->addValue('i', $this->magazine_id->unwrapOr(0));

        if (mb_strlen($searchKeywords) > 3)
            $selector
            ->addWhereClause("AND MATCH (title, description, edition_label) AGAINST (?)")
            ->addValue('s', $searchKeywords);

        if (!$this->includeSoftDeleted)
            $selector->addWhereClause("AND {$this->getWhereQueryColumnName('deleted_at')} IS NULL ");

        $count = (int)$selector->run($conn, SqlSelector::RETURN_FIRST_COLUMN_VALUE);
        return $count;
    }

    /**
     * @return Edition[]
     */
    public function getMultipleFromMagazine(mysqli $conn, string $searchKeywords, string $orderBy, int $pageNum, int $numResultsOnPage) : array
    {
        $selector = $this->getGetSingleSqlSelector()
        ->clearValues()
        ->clearWhereClauses()
        ->addWhereClause("{$this->getWhereQueryColumnName('magazine_id')} = ?")
        ->addValue('i', $this->magazine_id->unwrapOr(0));

        if (mb_strlen($searchKeywords) > 3)
            $selector
            ->addWhereClause("AND MATCH (title, description, edition_label) AGAINST (?)")
            ->addValue('s', $searchKeywords);

        if (!$this->includeSoftDeleted)
            $selector->addWhereClause("AND {$this->getWhereQueryColumnName('deleted_at')} IS NULL ");

        $selector->setOrderBy(match($orderBy)
        {
            'title' => 'title ASC',
            'ref_date' => 'ref_date DESC',
            'id' => 'id DESC',
            default => 'ref_date DESC'
        });

        $calcPage = ($pageNum - 1) * $numResultsOnPage;
        $selector
        ->setLimit('?, ?')
        ->addValues('ii', [ $calcPage, $numResultsOnPage ]);

        $drs = $selector->run($conn, SqlSelector::RETURN_ALL_ASSOC);
        return array_map([ $this, 'newInstanceFromDataRowFromDatabase' ], $drs);
    }
}