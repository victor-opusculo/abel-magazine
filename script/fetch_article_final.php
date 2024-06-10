<?php

use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Magazines\Article;

require_once __DIR__ . '/../vendor/autoload.php';

$i18n = I18n::init('pt_BR');
$articleId = $_GET['id'] ?? null;
$conn = Connection::create();

if (!Connection::isId($articleId))
    die("ID inválido!");

$exception = '';
try
{
    $article = (new Article([ 'id' => $articleId ]))->getSingle($conn);

    if (!$article->is_approved->unwrapOr(false))
        throw new Exception("Artigo não aprovado para publicação!");

    $filePath = $article->finalPublicationFile();

    if (!$filePath)
        throw new Exception("Arquivo final de artigo faltante!");

    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

    header('Content-Disposition: filename="' . "{$article->title->unwrap()}.{$fileExtension}" . '"');
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