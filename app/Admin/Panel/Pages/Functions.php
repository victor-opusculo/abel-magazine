<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Helpers\LogEngine;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Pages\Page;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\SubmissionRulesPageId;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\SubmissionRulesPageIdEnglish;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\TemplateFilePageId;
use VictorOpusculo\AbelMagazine\Lib\Model\Settings\TemplateFilePageIdEnglish;
use VictorOpusculo\MyOrm\Option;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\HttpGetMethod;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;

require_once __DIR__ . '/../../../../lib/Middlewares/AdminLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct($params)
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\adminLoginCheck';
    }

    public function create(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            $page = (new Page)->fillPropertiesFromFormInput($data);
            $result = $page->save($conn);

            if ($result['newId'])
            {
                LogEngine::writeLog("Página criada! ID: $result[newId]");
                return [ 'success' => I18n::get('functions.pageCreateSuccess'), 'newId' => $result['newId'] ];
            }
            else
            {
                LogEngine::writeErrorLog('Ao criar página.');
                return [ 'error' => I18n::get('functions.pageCreateError') ];
            }
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }

    public function setSubmissionRulesId(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            [ 'page_id' => $newId, 'page_id_en' => $newIdEnglish, 'remove' => $remove ] = $data;
            $sett = new SubmissionRulesPageId();
            $settEn = new SubmissionRulesPageIdEnglish();

            if (!$remove)
            {
                $sett->value = Option::some($newId);
                $settEn->value = Option::some($newIdEnglish);
            }
            else
            {
                $sett->value = Option::some(null);
                $settEn->value = Option::some(null);
            }

            $result = $sett->save($conn);
            $result['affectedRows'] += $settEn->save($conn)['affectedRows'];
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Páginas de regras de submissões definidas! ID: {$newId}");
                return [ 'success' => I18n::get('functions.settingChangeSuccess') ];
            }
            else
            {
                return [ 'info' => I18n::get('functions.noDataChanged') ];
            }
            
        }
        catch (Exception $e)
        {
            LogEngine::writeErrorLog($e->getMessage());
            return [ 'error' => $e->getMessage() ];
        }
    }

    public function setArticleTemplatePageId(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            [ 'page_id' => $newId, 'page_id_en' => $newIdEnglish, 'remove' => $remove ] = $data;
            $sett = new TemplateFilePageId();
            $settEn = new TemplateFilePageIdEnglish();

            if (!$remove)
            {
                $sett->value = Option::some($newId);
                $settEn->value = Option::some($newIdEnglish);
            }
            else
            {
                $sett->value = Option::some(null);
                $settEn->value = Option::some(null);
            }

            $result = $sett->save($conn);
            $result['affectedRows'] += $settEn->save($conn)['affectedRows'];
            if ($result['affectedRows'] > 0)
            {
                LogEngine::writeLog("Páginas de template de artigo definidas! ID: {$newId}");
                return [ 'success' => I18n::get('functions.settingChangeSuccess') ];
            }
            else
            {
                return [ 'info' => I18n::get('functions.noDataChanged') ];
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
        $getter = new Page();
        $count = $getter->getCount($conn, $get['q'] ?? '');
        $pages = $getter->getMultiple($conn, $get['q'] ?? '', $get['order_by'] ?? '', $get['page_num'] ?? 1, $get['num_results_on_page'] ?? 20);

        return [ 'data' => $pages, 'allCount' => $count ];
    }
}