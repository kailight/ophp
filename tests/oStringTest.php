<?php

namespace o;

class oStringTest extends \PHPUnit_Framework_TestCase {


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
     * @dataProvider providerReplace
     */
    public function testReplace($original,$search,$replace,$result)
    {
        $string = new oString($original);
        $this->assertEquals((string) $result, $string->replace($search,$replace));
    }

    public function providerReplace()
    {
        return array (
            array ('string','st', '', 'ring'),
            array ('Über', 'Ü', '', 'ber'),
            array ('Über Öwnage', 'Über ', '', 'Öwnage'),
        );
    }



    /**
     * @dataProvider providerLen
     */
    public function testLen($original,$result)
    {
        $string = new oString($original);
        $this->assertEquals((string) $result, $string->len());
    }

    public function providerLen()
    {
        return array (
            array ('foo',3),
            array ('foobar', 6),
            array ('',0),
        );
    }



    /**
     * @dataProvider providerTrim
     */
    public function testTrim($original,$charlist,$result)
    {
        $string = new oString($original);
        $this->assertEquals((string) $result, $string->trim($charlist));
    }

    public function providerTrim()
    {
        return array (
            array ('foo ',null,'foo'),
            array ("\t foobar \n", null, 'foobar'),
            array ('*foobar*','*','foobar'),
        );
    }


    /**
     * @dataProvider providerHumanize
     */
    public function testHumanize($original,$result)
    {
        $string = new oString($original);
        $this->assertEquals((string) $result, $string->humanize());
    }

    public function providerHumanize()
    {

        return array (
            array ('foo','foo'),
            array ("foo_bar",'foo bar'),
            array ('some_weird_string','some weird string'),
        );

    }
    
    
    
    
    /**
     * @dataProvider providerGlorify
     */
    public function testGlorify($original,$result)
    {
        $string = new oString($original);
        $this->assertEquals((string) $result, $string->Glorify());
    }

    public function providerGlorify()
    {

        return array (
            array ('foo','Foo'),
            array ("foo_bar",'Foo Bar'),
            array ('some_weird_string','Some Weird String'),
        );

    }

}