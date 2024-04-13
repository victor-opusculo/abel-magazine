<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Media;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Media\Media;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\FormDataBody;
use VictorOpusculo\PComp\Rpc\HttpGetMethod;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;

require_once __DIR__ . '/../../../../lib/Middlewares/AdminLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct()
    {
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\adminLoginCheck';
    }

    #[FormDataBody]
    public function create(array $post, array $files) : array
    {
        $conn = Connection::get();
        try
        {
            $media = (new Media);
            $media->fillPropertiesFromFormInput($post, $files);
            $result = $media->save($conn);

            if ($result['newId'])
            {
                LogEngine::writeLog("Mídia criada! ID: $result[newId]");
                return [ 'success' => I18n::get('functions.mediaCreatedSuccess'), 'newId' => $result['newId'] ];
            }
            else
            {
                LogEngine::writeLog("Erro ao criar mídia!");
                return [ 'error' => I18n::get('functions.mediaCreatedError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }

    #[HttpGetMethod]
    public function getMultiple(array $get) : array
    {
        $conn = Connection::get();
        $getter = new Media();
        $count = $getter->getCount($conn, $get['q'] ?? '');
        $medias = $getter->getMultiple($conn, $get['q'] ?? '', $get['order_by'] ?? '', $get['page_num'] ?? 1, $get['num_results_on_page'] ?? 20);

        return [ 'data' => $medias, 'allCount' => $count ];
    }
}