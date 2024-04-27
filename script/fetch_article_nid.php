<?php

use VictorOpusculo\AbelMagazine\Lib\Helpers\System;
use VictorOpusculo\AbelMagazine\Lib\Helpers\UserTypes;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Assessors\AssessorEvaluationToken;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;

require_once __DIR__ . '/../vendor/autoload.php';

$i18n = I18n::init('pt_BR');
$articleId = $_GET['id'] ?? null;
$conn = Connection::create();

if (!Connection::isId($articleId))
    die("ID inválido!");

$exception = '';
$token = $_GET['review_token'] ?? null;
try
{
    $article = (new Article([ 'id' => $articleId ]))->getSingle($conn);

    session_name('abel_magazine_admin_user');
    session_start();

    if (($_SESSION['user_type'] ?? '') !== UserTypes::administrator)
    {
        session_unset();
        session_destroy();
        session_name('abel_magazine_author_user');
        session_start();

        if (($_SESSION['user_type'] ?? '') !== UserTypes::author)
        {
            if ($token)
                $token = (new AssessorEvaluationToken([ 'token' => $token ]))->getSingleFromToken($conn);
            else
                throw new Exception("Artigo não localizado!");
        }
        else if ($article->submitter_id->unwrapOr(0) != ($_SESSION['user_id'] ?? INF))
        {
            throw new Exception("Artigo não localizado!");
        }
    }

    $filePath = System::baseDir() . '/' . $article->notIddedFilePathFromBaseDir();

    header('Content-Disposition: filename="' . "{$article->title->unwrap()}.{$article->not_idded_file_extension->unwrap()}" . '"');
    header("Content-Type: " . mime_content_type($filePath));
    header("Content-Length: " . filesize($filePath));

    readfile($filePath);
}
catch (Exception $e)
{
    $exception = $e->getMessage();
}
finally
{
    $conn->close();
}

if ($exception) die($exception);