<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Upload;

use Exception;

class ArticleUploadException extends Exception
{
    public function __construct(string $message, public string $fileName, public int $articleId)
    {
        parent::__construct($message);
    }
}