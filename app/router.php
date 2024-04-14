<?php
namespace VictorOpusculo\AbelMagazine\App;

return 
[
    '/' => \VictorOpusculo\AbelMagazine\App\HomePage::class,
    '__layout' => \VictorOpusculo\AbelMagazine\App\BaseLayout::class,
    '__functions' => \VictorOpusculo\AbelMagazine\App\HomeFunctions::class,
    '/admin' => fn() =>
    [
        '/login' => \VictorOpusculo\AbelMagazine\App\Admin\Login::class,
        '/panel' => fn() =>
        [
            '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Home::class,
            '__layout' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Layout::class,
            '/media' => fn() =>
            [
                '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Media\Home::class,
                '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Media\Functions::class,
                '/create' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Media\Create::class,
                '/[mediaId]' => fn() =>
                [
                    '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Media\MediaId\View::class,
                    '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Media\MediaId\Functions::class,
                    '/edit' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Media\MediaId\Edit::class,
                    '/delete' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Media\MediaId\Delete::class
                ]
            ],
            '/magazines' => fn() =>
            [
                '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\Home::class,
                '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\Functions::class,
                '/create' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\Create::class,
                '/[magazineId]' => fn() =>
                [
                    '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\View::class,
                    '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Functions::class,
                    '/edit' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Edit::class,
                    '/delete' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Delete::class,
                    '/editions' => fn() =>
                    [
                        '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\Functions::class,
                        '/create' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\Create::class,
                        '/[editionId]' => fn() => 
                        [
                            '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\EditionId\View::class,
                            '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\EditionId\Functions::class,
                            '/edit' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\EditionId\Edit::class,
                            '/delete' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\EditionId\Delete::class
                        ]
                    ]
                ]
            ]
        ],
        '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Functions::class
    ]
];