<?php
namespace VictorOpusculo\AbelMagazine\App;

use VictorOpusculo\PComp\Rpc\BaseFunctionsClass;

final class HomeFunctions extends BaseFunctionsClass
{
    public function test(array $data) : array
    {
        return [ 'success' => 'Êxito!' ];
    }

    public function getAll(array $data) : array
    {
        $qty = $data['qty'] ?? 10;
        return array_map(fn($i) => mt_rand(1, 1000), array_fill(0, $qty, null));
    } 
} 