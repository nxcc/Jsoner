<?php

namespace jsoner\filter;


class RemoveKeysFilter implements Filter
{

    public static function filter($array, $params)
    {
        foreach ($params as $key) {
            unset($array[$key]);
        }
        return $array;
    }
}
