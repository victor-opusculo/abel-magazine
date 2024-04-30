<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Settings;

use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;
use VictorOpusculo\MyOrm\Option;

/**
 * @property Option<string> name
 * @property Option<string> value
 */
class HomePagePreTextPageId extends DataEntity
{
    public function __construct($initialValues = null)
    {
        $this->properties = (object)
        [
            'name' => new DataProperty(null, fn() => 'HOMEPAGE_PRE_TEXT_PAGE_ID', DataProperty::MYSQL_STRING),
            'value' => new DataProperty(null, fn() => null, DataProperty::MYSQL_STRING)
        ];
        parent::__construct($initialValues);

        $this->name = Option::some('HOMEPAGE_PRE_TEXT_PAGE_ID');
    }

    protected string $databaseTable = 'settings';
    protected string $formFieldPrefixName = 'homepagePreText';
    protected array $primaryKeys = ['name'];
    protected array $setPrimaryKeysValue = ['name'];
}