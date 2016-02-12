<?php

/**
 * Hooks for the JSONer extension.
 *
 * @ingroup Extensions
 */
class JSONerHooks
{
    public static function onParserSetup( &$parser )
    {
        $parser->setFunctionHook('what', 'JSONerHooks::what');

        return true; // Always return true, in order not to stop MW's hook processing!
    }

    public static function what( &$parser )
    {
        global $wgJSONerBaseUrl;
        $val = "Function 'what' ($wgJSONerBaseUrl) called.";
        return $val;
    }
}
