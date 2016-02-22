<?php

namespace jsoner\filter;


interface Filter
{
    public static function filter($array, $params);
}
