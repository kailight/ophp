<?php

namespace o;

class oArrayAdvancedTest extends \PHPUnit_Framework_TestCase {


    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new oString();
    }

    protected function tearDown()
    {
        $this->fixture = NULL;
    }



    /**
     * @dataProvider providerForeach_basic
     */
    public function testForeach_basic($original,$expected_result)
    {
        $array = new oArray($original);
        $result = '';
        foreach ($array as $k=>$v) {
            $result .= $v.',';
        }
        $result = trim($result,',');
        $this->assertEquals( $expected_result, $result );
    }


    public function providerForeach_basic()
    {
        return array (
            array (array(0,1,2), '0,1,2'),
            array (array("foo","bar","foobar"), "foo,bar,foobar"),
            array (array("apples","oranges"), "apples,oranges"),
        );
    }






}