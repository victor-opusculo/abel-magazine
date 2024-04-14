<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Magazines;

use Exception;
use mysqli;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Option;

/**
 * @property Option<int> id
 * @property Option<int> edition_id
 * @property Option<int> submitter_id
 * @property Option<string> title
 * @property Option<string> resume
 * @property Option<string> keywords
 * @property Option<string> language
 * @property Option<string> status
 * @property Option<int> is_approved
 * @property Option<string> not_idded_file_extension
 * @property Option<string> idded_file_extension
 * @property Option<string> submission_datetime
 * 
 */
class Article extends DataEntity
{
    public function __construct(?array $initialValues = null)
    {
        $this->properties = (object)
        [
            'id' => new DataProperty('id', fn() => null, DataProperty::MYSQL_INT),
            'edition_id' => new DataProperty('edition_id', fn() => null, DataProperty::MYSQL_INT),
            'submitter_id' => new DataProperty('submitter_id', fn() => null, DataProperty::MYSQL_INT),
            'title' => new DataProperty('title', fn() => null, DataProperty::MYSQL_STRING),
            'resume' => new DataProperty('resume', fn() => null, DataProperty::MYSQL_STRING),
            'keywords' => new DataProperty('keywords', fn() => null, DataProperty::MYSQL_STRING),
            'language' => new DataProperty('language', fn() => null, DataProperty::MYSQL_STRING),
            'status' => new DataProperty('status', fn() => null, DataProperty::MYSQL_STRING),
            'is_approved' => new DataProperty('is_approved', fn() => null, DataProperty::MYSQL_INT),
            'not_idded_file_extension' => new DataProperty(null, fn() => null, DataProperty::MYSQL_STRING),
            'idded_file_extension' => new DataProperty(null, fn() => null, DataProperty::MYSQL_STRING),
            'submission_datetime' => new DataProperty(null, fn() => gmdate('Y-m-d H:i:s'), DataProperty::MYSQL_STRING)
        ];

        parent::__construct($initialValues);
    }
    
    protected string $databaseTable = 'articles';
    protected string $formFieldPrefixName = 'articles';
    protected array $primaryKeys = ['id'];

    private string $fileInputNameNotIdded = 'file_article_nid';
    private string $fileInputNameIdded = 'file_article_id';

    public function beforeDatabaseInsert(mysqli $conn): int
    {
        $edition = (new Edition([ 'id' => $this->edition_id->unwrapOr(0) ]))->getSingle($conn);
        if ($edition->is_open_for_submissions->unwrapOr(false))
        {
            Upload\NotIddedArticleUpload::checkForUploadError($this->postFiles, $this->fileInputNameNotIdded);
            $extension = Upload\NotIddedArticleUpload::getExtension($this->postFiles, $this->fileInputNameNotIdded);
            $this->not_idded_file_extension = Option::some($extension);
            $this->status = Option::some(ArticleStatus::EvaluationInProgress->value);
            $this->submission_datetime = Option::some(gmdate('Y-m-d H:i:s'));
        }
        else
            throw new Exception(I18n::get('exceptions.editionNotOpenForSubmissions'));

        return 0;
    }

    public function afterDatabaseInsert(mysqli $conn, $insertResult)
    {
        Upload\NotIddedArticleUpload::uploadArticleFile($insertResult['newId'], $this->postFiles, $this->fileInputNameNotIdded);
        return $insertResult;
    }

    public function beforeDatabaseUpdate(mysqli $conn): int
    {
        if (isset($this->postFiles) && $this->postFiles[$this->fileInputNameIdded] && ($this->status->unwrapOr('') === ArticleStatus::Approved->value))
        {
            Upload\IddedArticleUpload::checkForUploadError($this->postFiles, $this->fileInputNameIdded);
            $extension = Upload\IddedArticleUpload::getExtension($this->postFiles, $this->fileInputNameIdded);
            $this->idded_file_extension = Option::some($extension);
            $this->status = Option::some(ArticleStatus::ApprovedWithIddedFile->value);
        }

        return 0;
    }

    public function afterDatabaseUpdate(mysqli $conn, $updateResult)
    {
        if (isset($this->postFiles) && $this->postFiles[$this->fileInputNameIdded] && ($this->status->unwrapOr('') === ArticleStatus::Approved->value))
            Upload\IddedArticleUpload::uploadArticleFile($this->id->unwrap(), $this->postFiles, $this->fileInputNameIdded);

        return $updateResult;
    }

    public function afterDatabaseDelete(mysqli $conn, $deleteResult)
    {
        Upload\NotIddedArticleUpload::cleanArticleFolder($this->id->unwrap());
        Upload\NotIddedArticleUpload::checkForEmptyArticleDir($this->id->unwrap());
        return $deleteResult;
    }
}