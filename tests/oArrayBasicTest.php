<?php

namespace o;

class oArrayBasicTest extends \PHPUnit_Framework_TestCase {


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
     * @dataProvider provider__Construct
     */
    public function test__Construct($original,$result)
    {
        $array = new oArray($original);
        $this->assertEquals( $result, $array->currentArray );
    }

    public function provider__Construct()
    {
        return array (
            array (null, array()),
            array (array('foo','bar'), array('foo','bar')),
            array ('test', array('test')),
        );
    }


    /**
     * @dataProvider providerCount
     */
    public function testCount($original,$result)
    {
        $array = new oArray($original);
        $this->assertEquals( $result, $array->count() );
    }

    public function providerCount()
    {
        return array (
            array (array(1,2), 2),
            array (array(0,1,2), 3),
            array (array("foo","bar"),2),
        );
    }



    /**
     * @dataProvider providerKeyExists
     */
    public function testKeyExists($original,$key,$result)
    {
        $array = new oArray($original);
        $this->assertEquals( $result, $array->keyExists($key) );
    }

    public function providerKeyExists()
    {
        return array (
            array (array(1,2), 0, true),
            array (array(0,1,2), 5, false),
            array (array("foo","bar"), 0, true),
        );
    }



    /**
     * @dataProvider providerKeys
     */
    public function testKeys($original,$result)
    {
        $array = new oArray($original);
        $this->assertEquals( $result, $array->keys() );
    }


    public function providerKeys()
    {
        return array (
            array (array(1,2), array(0,1)),
            array (array(0,1=>1), array(0,1)),
            array (array("foo","bar"), array(0,1)),
        );
    }



    /**
     * @dataProvider providerGet_basic
     */
    public function testGet_basic($original,$key,$result)
    {
        $array = new oArray($original);
        $this->assertEquals( $result, $array->get($key) );
    }



    public function providerGet_basic()
    {
        return array (
            array ( array(0=>'foo',1=>'bar'),0,'foo' ),
            array ( array('foo'=>'bar','bar'=>'foo'), 'foo', 'bar' ),
            array ( array(0=>'foo',1=>'bar','foobar'=>'foobar'), 2, 'foobar' ),
        );
    }



    /**
     * @dataProvider providerImplode
     */
    public function testImplode($original,$glue,$result)
    {
        $array = new oArray($original);
        $this->assertEquals( (string) $result, $array->implode($glue) );
    }


    public function providerImplode()
    {
        return array (
            array (array(0,2=>1), ',',"0,1"),
            array (array("foo","bar"), '',"foobar"),
            array (array("apples","oranges"), " and ", "apples and oranges"),
        );
    }





    /**
     * @dataProvider providerReverse
     */
    public function testReverse($original,$result)
    {
        $array = new oArray($original);
        $this->assertEquals( $result, $array->reverse()->get() );
    }


    public function providerReverse()
    {
        return array (
            array (array(0,1), array(1,0)),
            array (array("foo",1), array(1,"foo")),
            array (array("foo",1), array(1,"foo")),
        );
    }



}