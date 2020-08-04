<?php

return new class implements \Psr\Container\ContainerInterface
{
    protected $data;

    public function get($key)
    {
        return $this->data[$key];
    }

    public function has($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
    }
};