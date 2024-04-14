<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Settings;

use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;

/**
 * @property Option<string> name
 * @property Option<string> value
 */
class LgpdTermVersion extends DataEntity
{
    public function __construct($initialValues = null)
    {
        $this->properties = (object)
        [
            'name' => new DataProperty(null, fn() => 'DEFAULT_LGPD_TERM_VERSION', DataProperty::MYSQL_STRING),
            'value' => new DataProperty(null, fn() => '', DataProperty::MYSQL_STRING)
        ];
        parent::__construct($initialValues);
    }

    protected string $databaseTable = 'settings';
    protected string $formFieldPrefixName = 'lgpdTermVersion';
    protected array $primaryKeys = ['name'];

}