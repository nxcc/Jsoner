<?php
namespace jsoner\filter;

use jsoner\Parser;

class SelectSubtreeFilterTest extends \PHPUnit_Framework_TestCase
{
    public function inOutDataProvider()
    {
        return [
            ['menu', '{"menu":{"id":"file","value":"File","popup":{"menuitem":[{"value":"New","onclick":"CreateNewDoc()"},{"value":"Open","onclick":"OpenDoc()"},{"value":"Close","onclick":"CloseDoc()"}]}}}', '{"id":"file","value":"File","popup":{"menuitem":[{"value":"New","onclick":"CreateNewDoc()"},{"value":"Open","onclick":"OpenDoc()"},{"value":"Close","onclick":"CloseDoc()"}]}}'],
            ['menuitem', '{"menuitem":[{"value":"New","onclick":"CreateNewDoc()"},{"value":"Open","onclick":"OpenDoc()"},{"value":"Close","onclick":"CloseDoc()"}]}', '[{"value":"New","onclick":"CreateNewDoc()"},{"value":"Open","onclick":"OpenDoc()"},{"value":"Close","onclick":"CloseDoc()"}]'],
            ['small', '{"small":{"0":"a","1":"b"}}', '{"0":"a","1":"b"}'],

            # If the data from the parser contains a list of objects, the filter should do nothing
            ['0', '[{"key": "value"},{"key": "value"}]', '[{"key": "value"},{"key": "value"}]'],

            # Empty data
            ['', '[]', '[]'],
            ['', '{}', '{}'],
        ];
    }

    /**
     * @dataProvider inOutDataProvider
     */
    public function testFilter($subtree, $unfiltered, $filtered)
    {
        $unfiltered_array = Parser::jsonDecode($unfiltered);
        $filtered_array = Parser::jsonDecode($filtered);

        $a = SelectSubtreeFilter::doFilter($unfiltered_array, $subtree);

        $this->assertTrue($filtered_array === $a,
            "SelectSubtreeFilter did not select subtree!"
        );
    }
}
