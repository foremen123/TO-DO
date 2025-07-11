<?php

namespace Tests\DataProvider;

class RouterDataProvider
{
    static public function attributesRoutes(): array
    {
        $controller = new class
        {
            #[\app\Attributes\Get('/get')]
            public function indexGet(): bool
            {
                return true;
            }

            #[\app\Attributes\Post('/post')]
            public function indexPost(): bool
            {
                return true;
            }
        };

        return [
            'basic attribute routes' => [
                $controller,
                [
                    'get' => ['/get' => [$controller, 'indexGet']],
                    'post' => ['/post' => [$controller, 'indexPost']],
                ]
            ]
        ];
    }

    static public function ExceptionsRoutes(): array
    {
        $controller = new class
        {
            #[\app\Attributes\Get('/get')]
            public function index(): bool
            {
                return true;
            }
        };

        return
            [
                'Not found route' =>
                    [
                        '/error', 'get'
                    ],

                'Found route, but method not allowed' =>
                    [
                        '/get', 'post'
                    ],

                'Not exist method' =>
                    [
                        '/get', 'get', ['class' => $controller::class, 'method' => 'error']
                    ],

                'Not exist class' =>
                    [
                        '/get', 'get', ['class' => 'error', 'method' => 'index']
                    ],
            ];
    }

    static public function succeedRoute(): array
    {
        $controller = new class {
            #[\app\Attributes\Get('/get')]
            public function index(): bool
            {
                return true;
            }
        };

        return [
            'basic route' =>
                [
                    '/get',
                    'get',
                    [
                        'class' => $controller::class,
                        'method' => 'index',
                    ],
                ],
        ];
    }
}