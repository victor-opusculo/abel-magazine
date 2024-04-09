<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Media;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Media\Media;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\FormDataBody;
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
                return [ 'success' => I18n::get('functions.mediaCreatedSuccess'), 'newId' => $result['newId'] ];
            else
                return [ 'error' => I18n::get('functions.mediaCreatedError') ];
        }
        catch (Exception $e)
        {
            return [ 'error' => $e->getMessage() ];
        }
    }
}