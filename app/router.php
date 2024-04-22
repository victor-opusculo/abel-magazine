<?php
namespace VictorOpusculo\AbelMagazine\App;

return 
[
    '/' => \VictorOpusculo\AbelMagazine\App\HomePage::class,
    '__layout' => \VictorOpusculo\AbelMagazine\App\BaseLayout::class,
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
                            '/delete' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\EditionId\Delete::class,
                            '/articles' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\MagazineId\Editions\EditionId\Articles::class
                        ]
                    ]
                ]
            ],
            '/articles' => fn() =>
            [
                '/[articleId]' => fn() =>
                [
                    '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\View::class,
                    '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\Functions::class,
                    '/edit' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\Edit::class,
                    '/delete' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\Delete::class,
                    '/evaluation_tokens' => fn() =>
                    [
                        '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\EvaluationTokens\Home::class,
                        '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\EvaluationTokens\Functions::class,
                        '/create' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\EvaluationTokens\Create::class,
                        '/[tokenId]' => fn() =>
                        [
                            '/delete' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\EvaluationTokens\TokenId\Delete::class,
                            '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\EvaluationTokens\TokenId\Functions::class
                        ]
                    ]
                ]
            ],
            '/opinions' => fn() =>
            [
                '/[opinionId]' => fn() =>
                [
                    '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Opinions\OpinionId\View::class,
                    '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Opinions\OpinionId\Functions::class,
                    '/delete' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Opinions\OpinionId\Delete::class
                ]
            ]
        ],
        '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Functions::class
    ],
    '/magazine' => fn() =>
    [
        '/[magazineStrId]' => fn() =>
        [
            '/' => \VictorOpusculo\AbelMagazine\App\Magazine\MagazineStrId\Home::class,
            '/edition' => fn() =>
            [
                '/[editionId]' => \VictorOpusculo\AbelMagazine\App\Magazine\MagazineStrId\Edition\EditionId::class
            ]
        ]
    ],
    '/submitter' => fn() =>
    [
        '/login' => \VictorOpusculo\AbelMagazine\App\Submitter\Login::class,
        '/register' => \VictorOpusculo\AbelMagazine\App\Submitter\Register::class,
        '__functions' => \VictorOpusculo\AbelMagazine\App\Submitter\Functions::class,
        '/panel' => fn() =>
        [
            '/' => \VictorOpusculo\AbelMagazine\App\Submitter\Panel\Home::class,
            '__layout' => \VictorOpusculo\AbelMagazine\App\Submitter\Panel\Layout::class,
            '__functions' => \VictorOpusculo\AbelMagazine\App\Submitter\Panel\Functions::class,
            '/edit_profile' => \VictorOpusculo\AbelMagazine\App\Submitter\Panel\EditProfile::class,
            '/articles' => fn() =>
            [
                '/' => \VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\Home::class,
                '__functions' => \VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\Functions::class,
                '/create' => \VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\Create::class,
                '/[articleId]' => fn() =>
                [
                    '/' => \VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\ArticleId\View::class,
                    '__functions' => \VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\ArticleId\Functions::class,
                    '/edit' => \VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\ArticleId\Edit::class
                ]
            ]
        ]
    ],
    '/reviewer' => fn() =>
    [
        '/review' => fn() =>
        [
            '/[tokenStr]' => fn() =>
            [
                '/' => \VictorOpusculo\AbelMagazine\App\Reviewer\Review\TokenStr\Home::class,
                '__functions' => \VictorOpusculo\AbelMagazine\App\Reviewer\Review\TokenStr\Functions::class
            ]
        ]
    ]
];