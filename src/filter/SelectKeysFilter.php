<?php

namespace jsoner\filter;


class SelectKeysFilter implements Filter
{
    public static function doFilter($array, $params)
    {
        $result = [];
        $alwaysInclude = ['id'];
        $select_these_keys = array_merge($alwaysInclude, $params);

        foreach ($array as $item) {
            $result[] = array_intersect_key($item, array_flip($select_these_keys));
        }
        return $result;
    }
}
