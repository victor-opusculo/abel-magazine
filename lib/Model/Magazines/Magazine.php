<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Magazines;

use mysqli;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Media\Media;
use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Exceptions\DatabaseEntityNotFound;
use VictorOpusculo\MyOrm\SqlSelector;
use VictorOpusculo\MyOrm\Option;

/**
 * @property Option id
 * @property Option name
 * @property Option description
 * @property Option cover_image_media_id
 * @property Option string_identifier
 * @property Option deleted_at
 */
class Magazine extends DataEntity
{
    public function __construct(?array $initialValues = null)
    {
        $this->properties = (object)
        [
            'id' => new DataProperty('id', fn() => null, DataProperty::MYSQL_INT),
            'name' => new DataProperty('name', fn() => '', DataProperty::MYSQL_STRING),
            'description' => new DataProperty('description', fn() => '', DataProperty::MYSQL_STRING),
            'cover_image_media_id' => new DataProperty('cover_image_media_id', fn() => null, DataProperty::MYSQL_INT),
            'string_identifier' => new DataProperty('string_identifier', fn() => null, DataProperty::MYSQL_STRING),
            'deleted_at' => new DataProperty(null, fn() => null, DataProperty::MYSQL_STRING)
        ];

        parent::__construct($initialValues);
    }

    protected string $databaseTable = 'magazines';
    protected string $formFieldPrefixName = 'magazines';
    protected array $primaryKeys = ['id'];

    public ?Media $coverImage = null;
    public bool $includeSoftDeleted = false;

    public function fetchCoverImage(mysqli $conn) : self
    {
        if ($this->cover_image_media_id->unwrapOr(null))
        {
            $media = (new Media([ 'id' => $this->cover_image_media_id->unwrap() ]))->getSingle($conn);
            $this->coverImage = $media;
        }

        return $this;
    }

    public function getSingle(mysqli $conn): static
    {
        $selector = $this->getGetSingleSqlSelector();

        if (!$this->includeSoftDeleted)
            $selector->addWhereClause("AND {$this->getWhereQueryColumnName('deleted_at')} IS NULL ");

        $dr = $selector->run($conn, SqlSelector::RETURN_SINGLE_ASSOC);

        if (isset($dr))
            return $this->newInstanceFromDataRowFromDatabase($dr);
        else
            throw new DatabaseEntityNotFound(I18n::get('exceptions.magazineNotFound'), $this->databaseTable);
    }

    public function getCount(mysqli $conn, string $searchKeywords, bool $includeSoftDeleted = false) : int
    {
        $selector = (new SqlSelector)
        ->addSelectColumn("COUNT(*)")
        ->setTable($this->databaseTable);

        if (mb_strlen($searchKeywords) > 3)
            $selector
            ->addWhereClause("MATCH (name, description, string_identifier) AGAINST (?)")
            ->addValue('s', $searchKeywords);

        if (!$includeSoftDeleted)
        {
            $selector = $selector->hasWhereClauses()
                ?   $selector->addWhereClause("AND {$this->getWhereQueryColumnName('deleted_at')} IS NULL ")
                :   $selector->addWhereClause("{$this->getWhereQueryColumnName('deleted_at')} IS NULL ");
        }

        $count = (int)$selector->run($conn, SqlSelector::RETURN_FIRST_COLUMN_VALUE);
        return $count;
    }

    /**
     * @return Magazine[]
     */
    public function getMultiple(mysqli $conn, string $searchKeywords, string $orderBy, int $pageNum, int $numResultsOnPage, bool $includeSoftDeleted = false) : array
    {
        $selector = $this->getGetSingleSqlSelector()
        ->clearValues()
        ->clearWhereClauses();

        if (mb_strlen($searchKeywords) > 3)
            $selector
            ->addWhereClause("MATCH (name, description, string_identifier) AGAINST (?)")
            ->addValue('s', $searchKeywords);

        if (!$includeSoftDeleted)
        {
            $selector = $selector->hasWhereClauses()
                ?   $selector->addWhereClause("AND {$this->getWhereQueryColumnName('deleted_at')} IS NULL ")
                :   $selector->addWhereClause("{$this->getWhereQueryColumnName('deleted_at')} IS NULL ");
        }

        $selector->setOrderBy(match($orderBy)
        {
            'name' => 'name ASC',
            'string_identifier' => 'string_identifier ASC',
            'id' => 'id DESC',
            default => 'id DESC'
        });

        $calcPage = ($pageNum - 1) * $numResultsOnPage;
        $selector
        ->setLimit('?, ?')
        ->addValues('ii', [ $calcPage, $numResultsOnPage ]);

        $drs = $selector->run($conn, SqlSelector::RETURN_ALL_ASSOC);
        return array_map([ $this, 'newInstanceFromDataRowFromDatabase' ], $drs);
    }

    public function getSingleByStringIdentifier(mysqli $conn) : self
    {
        $selector = $this->getGetSingleSqlSelector()
        ->clearValues()
        ->clearWhereClauses()
        ->addWhereClause("{$this->getWhereQueryColumnName('string_identifier')} = ?")
        ->addValue('s', $this->string_identifier->unwrapOr(null));

        $dr = $selector->run($conn, SqlSelector::RETURN_SINGLE_ASSOC);

        if (isset($dr))
            return $this->newInstanceFromDataRowFromDatabase($dr);
        else
            throw new \VictorOpusculo\MyOrm\Exceptions\DatabaseEntityNotFound(I18n::get('exceptions.magazineNotFound'), $this->databaseTable);
    }

    public function existsStringIdentifier(mysqli $conn) : bool
    {
        $selector = (new SqlSelector)
        ->addSelectColumn("COUNT(*)")
        ->setTable($this->databaseTable)
        ->addWhereClause("{$this->getWhereQueryColumnName('string_identifier')} = ?")
        ->addWhereClause(" AND {$this->getWhereQueryColumnName('id')} != ?")
        ->addValue('s', $this->string_identifier->unwrapOr(null))
        ->addValue('i', $this->id->unwrapOr(null));

        $count = (int)$selector->run($conn, SqlSelector::RETURN_FIRST_COLUMN_VALUE);
        return $count > 0;
    }

    public function softRecover(mysqli $conn) : array
    {
        $this->deleted_at = Option::some(null);
        return $this->save($conn);
    }

    public function softDelete(mysqli $conn) : array
    {
        $this->deleted_at = Option::some(gmdate('Y-m-d H:i:s'));
        return $this->save($conn);
    }

    /** @return array{int, Edition[]} */
    public function fetchMultipleEditions(mysqli $conn, string $searchKeywords, string $orderBy, int $pageNum, int $numResultsOnPage, bool $includeSoftDeleted = false) : array
    {
        $editionGetter = new Edition([ 'magazine_id' => $this->id->unwrapOr(0) ]);
        $editionGetter->includeSoftDeleted = $includeSoftDeleted;

        $count = $editionGetter->getCountFromMagazine($conn, $searchKeywords);
        $editions = $editionGetter->getMultipleFromMagazine($conn, $searchKeywords, $orderBy, $pageNum, $numResultsOnPage);
        return [ $count, $editions ];
    }
}