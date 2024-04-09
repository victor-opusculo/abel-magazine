<?php
namespace VictorOpusculo\AbelMagazine\App\Admin\Panel\Media\MediaId;

use Exception;
use VictorOpusculo\AbelMagazine\Lib\Internationalization\I18n;
use VictorOpusculo\AbelMagazine\Lib\Model\Database\Connection;
use VictorOpusculo\AbelMagazine\Lib\Model\Media\Media;
use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;
use VictorOpusculo\PComp\Rpc\FormDataBody;
use VictorOpusculo\PComp\Rpc\IgnoreMethod;

require_once __DIR__ . '/../../../../../lib/Middlewares/AdminLoginCheck.php';

final class Functions extends BaseFunctionsClass
{
    #[IgnoreMethod]
    public function __construct(?array $params = [])
    {
        parent::__construct($params);
        $this->middlewares[] = '\VictorOpusculo\AbelMagazine\Lib\Middlewares\adminLoginCheck';
    }

    protected $mediaId;

    #[FormDataBody]
    public function edit(array $post, array $files) : array
    {
        $conn = Connection::get();
        try
        {
            $media = (new Media([ 'id' => $this->mediaId ]))->getSingle($conn);
            $media->fillPropertiesFromFormInput($post, $files);
            $result = $media->save($conn);

            if ($result['affectedRows'] > 0)
                return [ 'success' => I18n::get('functions.mediaEditedSuccess') ];
            else
                return [ 'info' => I18n::get('functions.noDataChanged') ];
        }
        catch (Exception $e)
        {
            return [ 'error' => $e->getMessage() ];
        }
    }

    public function del(array $data) : array
    {
        $conn = Connection::get();
        try
        {
            $media = (new Media([ 'id' => $this->mediaId ]))->getSingle($conn);
            $result = $media->delete($conn);

            if ($result['affectedRows'] > 0)
                return [ 'success' => I18n::get('functions.mediaDeletedSuccess') ];
            else
                return [ 'error' => I18n::get('functions.mediaDeleteError') ];
        }
        catch (Exception $e)
        {
            return [ 'error' => $e->getMessage() ];
        }
    }
}