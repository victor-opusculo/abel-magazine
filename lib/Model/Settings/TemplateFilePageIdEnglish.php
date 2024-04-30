<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Settings;

use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Option;

/**
 * @property Option<string> name
 * @property Option<string> value
 */
class TemplateFilePageIdEnglish extends DataEntity
{
    public function __construct($initialValues = null)
    {
        $this->properties = (object)
        [
            'name' => new DataProperty(null, fn() => 'ARTICLE_TEMPLATE_PAGE_ID_EN', DataProperty::MYSQL_STRING),
            'value' => new DataProperty(null, fn() => '', DataProperty::MYSQL_STRING)
        ];
        parent::__construct($initialValues);

        $this->name = Option::some('ARTICLE_TEMPLATE_PAGE_ID_EN');
    }

    protected string $databaseTable = 'settings';
    protected string $formFieldPrefixName = 'templatePageIdEnglish';
    protected array $primaryKeys = ['name'];
    protected array $setPrimaryKeysValue = ['name'];
}