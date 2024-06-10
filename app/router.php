<?php
namespace VictorOpusculo\AbelMagazine\App;

return 
[
    '/' => \VictorOpusculo\AbelMagazine\App\HomePage::class,
    '__layout' => \VictorOpusculo\AbelMagazine\App\BaseLayout::class,
    '/contact' => \VictorOpusculo\AbelMagazine\App\Contact::class,
    '/base' => fn() =>
    [
        '__functions' => \VictorOpusculo\AbelMagazine\App\Base\Functions::class,
    ],
    '/admin' => fn() =>
    [
        '/' => \VictorOpusculo\AbelMagazine\App\Admin\Login::class,
        '/login' => \VictorOpusculo\AbelMagazine\App\Admin\Login::class,
        '/panel' => fn() =>
        [
            '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Home::class,
            '__layout' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Layout::class,
            '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Functions::class,
            '/edit_profile' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\EditProfile::class,
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
                '/set_default_magazine' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Magazines\SetDefaultMagazineId::class,
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
                '/change_notification_email' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ChangeNotificationEmail::class,
                '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\Functions::class,
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
                            '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Articles\ArticleId\EvaluationTokens\TokenId\View::class,
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
            ],
            '/pages' => fn() =>
            [
                '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\Home::class,
                '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\Functions::class,
                '/create' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\Create::class,
                '/set_submission_rules_page' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\SetSubRulesPageId::class,
                '/set_submission_template_page' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\SetSubTemplatePageId::class,
                '/set_homepage_pre_text_page' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\SetHomepagePreTextPageId::class,
                '/set_editorial_team_page' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\SetEditorialTeamPageId::class,
                '/[pageId]' => fn() =>
                [
                    '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\PageId\View::class,
                    '__functions' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\PageId\Functions::class,
                    '/edit' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\PageId\Edit::class,
                    '/delete' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Pages\PageId\Delete::class
                ]
            ],
            '/submitters' => fn() =>
            [
                '/[submitterId]' => fn() =>
                [
                    '/' => \VictorOpusculo\AbelMagazine\App\Admin\Panel\Submitters\SubmitterId\View::class
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
            ],
            '/article' => fn() =>
            [
                '/[articleId]' => \VictorOpusculo\AbelMagazine\App\Magazine\MagazineStrId\Article\ArticleId::class
            ]
        ]
    ],
    '/submitter' => fn() =>
    [
        '/login' => \VictorOpusculo\AbelMagazine\App\Submitter\Login::class,
        '/register' => \VictorOpusculo\AbelMagazine\App\Submitter\Register::class,
        '/recover_password' => \VictorOpusculo\AbelMagazine\App\Submitter\RecoverPassword::class,
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
                    '/edit' => \VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\ArticleId\Edit::class,
                    '/reviews' => \VictorOpusculo\AbelMagazine\App\Submitter\Panel\Articles\ArticleId\Reviews::class
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
    ],
    '/page' => fn() =>
    [
        '/[pageId]' => \VictorOpusculo\AbelMagazine\App\Page\PageId::class
    ],
    '/info' => fn() =>
    [
        '/submission_rules' => \VictorOpusculo\AbelMagazine\App\Info\SubmissionRules::class,
        '/submission_template' => \VictorOpusculo\AbelMagazine\App\Info\ArticleTemplate::class,
        '/editorial_team' => \VictorOpusculo\AbelMagazine\App\Info\EditorialTeam::class
    ]
];