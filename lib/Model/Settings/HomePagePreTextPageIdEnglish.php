<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Settings;

use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Option;

/**
 * @property Option<string> name
 * @property Option<string> value
 */
class HomePagePreTextPageIdEnglish extends DataEntity
{
    public function __construct($initialValues = null)
    {
        $this->properties = (object)
        [
            'name' => new DataProperty(null, fn() => 'HOMEPAGE_PRE_TEXT_PAGE_ID_EN', DataProperty::MYSQL_STRING),
            'value' => new DataProperty(null, fn() => null, DataProperty::MYSQL_STRING)
        ];
        parent::__construct($initialValues);

        $this->name = Option::some('HOMEPAGE_PRE_TEXT_PAGE_ID_EN');
    }

    protected string $databaseTable = 'settings';
    protected string $formFieldPrefixName = 'homepagePreTextEnglish';
    protected array $primaryKeys = ['name'];
    protected array $setPrimaryKeysValue = ['name'];
}