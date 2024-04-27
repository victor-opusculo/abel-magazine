<?php

namespace VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Upload;

use VictorOpusculo\AbelMagazine\Lib\Helpers\System;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\FileUploadUtils;

final class IddedArticleUpload
{
    private function __construct() { }

    public const UPLOAD_DIR = 'uploads/articles/{articleId}/';
    public const ALLOWED_TYPES = ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    public const MAX_SIZE = 10485760 /* 10MB */;

    /**
     * Processa o upload de arquivo de artigos.
     * @param int $articleId ID do artigo
     * @param array $filePostData Array $_FILES
     * @param string $fileInputElementName Nome do elemento do tipo file do formulário de upload
     * @return bool
     * @throws ArticleUploadException
     * */
    public static function uploadArticleFile(int $articleId, array $filePostData, string $fileInputElementName) : bool
    {
            $fullDir = str_replace("{articleId}", (string)$articleId, System::systemBaseDir() . '/' . self::UPLOAD_DIR);
            $fileName = basename($filePostData[$fileInputElementName]["name"]);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uploadFile = $fullDir . "idded.$fileExtension";
        
            FileUploadUtils::checkForUploadError($filePostData[$fileInputElementName], self::MAX_SIZE, [self::class, 'throwException'], [ $fileName, $articleId ], self::ALLOWED_TYPES);

            if (!is_dir($fullDir))
                mkdir($fullDir, 0777, true);
                    
            if (!file_exists($uploadFile))
            {
                if (move_uploaded_file($filePostData[$fileInputElementName]["tmp_name"], $uploadFile))
                    return true;
                else
                    self::throwException(I18n::get('exceptions.errorMovingFileAfterUpload'), $fileName, $articleId);
            }
            else
                self::throwException(I18n::get("exceptions.fileAlreadyExistsOnServer"), $fileName, $articleId);
            
            return false;		
    }

    public static function getExtension(array $filePostData, string $fileInputElementName ) : string
    {
        $fileName = basename($filePostData[$fileInputElementName]["name"]);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        return $fileExtension;
    }

    public static function checkForUploadError(array $filePostData, string $fileInputElementName) : void
    {
        $fileName = basename($filePostData[$fileInputElementName]["name"]);
        FileUploadUtils::checkForUploadError($filePostData[$fileInputElementName], self::MAX_SIZE, [self::class, 'throwException'], [ $fileName, -1 ], self::ALLOWED_TYPES);
    }

    public static function deleteArticleFile(int $articleId) : bool
    {
        $fullDir = str_replace("{articleId}", (string)$articleId, System::systemBaseDir() . '/' . self::UPLOAD_DIR);
        $locationFilePath = $fullDir . "idded.*";
        
        $files = glob($locationFilePath);
        foreach ($files as $file)
            if (file_exists($file))
            {
                if(unlink($file))
                    return true;
                else
                    self::throwException(I18n::get('exceptions.errorDeletingArticleFile'), basename($file), $articleId);
            }

        return false;
    }


    public static function cleanArticleFolder(int $articleId)
    {
        $fullDir = str_replace("{articleId}", (string)$articleId, System::systemBaseDir() . '/' . self::UPLOAD_DIR);
        
        if (is_dir($fullDir))
        {
            $files = glob($fullDir . "*"); // get all file names
            
            foreach($files as $file)
            {
                if(is_file($file)) 
                    unlink($file); // delete file
            }
        }
    }

    public static function checkForEmptyArticleDir(int $articleId)
    {        
        $fullDir = str_replace("{articleId}", (string)$articleId, System::systemBaseDir() . '/' . self::UPLOAD_DIR);

        if (is_dir($fullDir))
            if (FileUploadUtils::isDirEmpty($fullDir))
                rmdir($fullDir);
    }

    public static function throwException(string $message, string $fileName, int $mediaId)
    {
        throw new ArticleUploadException($message, $fileName, $mediaId); 
    }
}
