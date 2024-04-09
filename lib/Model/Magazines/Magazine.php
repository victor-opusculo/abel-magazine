<?php
namespace VictorOpusculo\AbelMagazine\Lib\Model\Magazines;

use VictorOpusculo\MyOrm\DataEntity;
use VictorOpusculo\MyOrm\DataProperty;

class Magazine extends DataEntity
{
    public function __construct(?array $initialValues = null)
    {
        $this->properties = (object)
        [
            'id' => new DataProperty('id', fn() => null, DataProperty::MYSQL_INT),
            'name' => new DataProperty('name', fn() => '', DataProperty::MYSQL_STRING),
            'description' => new DataProperty('description', fn() => '', DataProperty::MYSQL_STRING),
            'cover_image_media_id' => new DataProperty('cover_image_media_id', fn() => null, DataProperty::MYSQL_INT),
            'string_identifier' => new DataProperty('string_identifier', fn() => null, DataProperty::MYSQL_STRING)
        ];

        parent::__construct($initialValues);
    }

    protected string $databaseTable = 'magazines';
    protected string $formFieldPrefixName = 'magazines';
    protected array $primaryKeys = ['id'];
}