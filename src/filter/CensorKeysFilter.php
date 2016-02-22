<?php

namespace jsoner\filter;


class CensorKeysFilter implements Filter
{
    public static function filter($array, $params)
    {
        $dummy = array_pop($params);
        foreach ($params as $key) {
            if(array_key_exists($key, $array)) {
                $array[$key] = $dummy;
            }
        }
        return $array;
    }
}
