<?php

return new class implements \Psr\Container\ContainerInterface
{
    protected $data_test = [
        'config' => [
            'router' => [
                'routes' => [
                    'test1' => [
                        'type' => 'String',
                        'options' => [
                            'route' => '/testing-url-1'
                        ]
                    ],
                    'test2' => [
                        'type' => 'String',
                        'options' => [
                            'route' => '/testing-url-2'
                        ]
                    ]
                ]
            ]
        ]
    ];

    public function get($key)
    {
        return $this->data_test[$key];
    }

    public function has($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data_test[$key];
        }
    }
};