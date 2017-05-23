<?php
require_once(dirname(__FILE__) . '/../vendor/autoload.php');
require_once(dirname(__FILE__) . '/../src/lod.php');
require_once(dirname(__FILE__) . '/../src/rdf.php');
require_once(dirname(__FILE__) . '/../src/lodinstance.php');

use PHPUnit\Framework\TestCase;

final class LODInstanceTest extends TestCase
{
    private $testTriples;
    private $testUri = 'http://foo.bar/';

    function setUp()
    {
        $this->testTriples = array(
            new LODStatement($this->testUri, 'foaf:page', new LODResource('http://foo.bar/page1')),
            new LODStatement($this->testUri, 'foaf:page', new LODResource('http://foo.bar/page2')),
            new LODStatement($this->testUri, 'rdfs:seeAlso', new LODResource('http://foo.bar/page3')),
            new LODStatement($this->testUri, 'rdfs:label', new LODLiteral('Yoinch Chettner', array('lang' => 'en-gb')))
        );
    }

    function testMerge()
    {
        $instance = new LODInstance(new LOD(), $this->testUri);
        $instance->merge($this->testTriples);
        $this->assertEquals(4, count($instance->model));
    }

    function testFilter()
    {
        $instance = new LODInstance(new LOD(), $this->testUri, $this->testTriples);


        // basic filter
        $filteredInstance = $instance->filter('foaf:page');
        $this->assertEquals(2, count($filteredInstance->model));

        $expanded = Rdf::expandPrefix('foaf:page', Rdf::COMMON_PREFIXES);
        foreach($filteredInstance->model as $triple)
        {
            $this->assertEquals($expanded, $triple->predicate->value);
        }
    }

    function testIteration()
    {
        $instance = new LODInstance(new LOD(), $this->testUri, $this->testTriples);

        $expectedValues = array(
            'http://foo.bar/page1',
            'http://foo.bar/page2',
            'http://foo.bar/page3',
            'Yoinch Chettner'
        );

        $actualValues = array();
        foreach($instance as $triple)
        {
            $actualValues[] = $triple->object->value;
        }

        $this->assertEquals($expectedValues, $actualValues);
    }

    function testArrayAccess()
    {
        $instance = new LODInstance(new LOD(), $this->testUri, $this->testTriples);

        // single predicate via offsetGet
        $filteredInstance1 = $instance['foaf:page'];
        $this->assertEquals(2, count($filteredInstance1->model));
        $expanded = Rdf::expandPrefix('foaf:page', Rdf::COMMON_PREFIXES);
        foreach($filteredInstance1->model as $triple)
        {
            $this->assertEquals($expanded, $triple->predicate->value);
        }

        // multiple predicates via offsetGet
        $filteredInstance2 = $instance['foaf:page,rdfs:seeAlso'];
        $this->assertEquals(3, count($filteredInstance2->model));

        // key existence via offsetExists
        $this->assertEquals(FALSE, isset($instance['fo:po']));
        $this->assertEquals(TRUE, isset($instance['foaf:page']));
    }
}
?>