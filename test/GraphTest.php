<?php

class GraphTest extends PHPUnit_Framework_TestCase
{
    public static function nodes()
    {
        $nodes = array(
            'A' => new Node(),
            'B' => new Node(),
            'C' => new Node(),
            'D' => new Node(),
            'E' => new Node(),
            'F' => new Node(),
            'G' => new Node()
        );

        $nodes['B']
            ->addLink($nodes['A'], 6)
            ->addLink($nodes['C'], 7)
            ->addLink($nodes['F'], 5);

        $nodes['C']
            ->addLink($nodes['D'], 1)
            ->addLink($nodes['D'], 2)
            ->addLink($nodes['E'], 8);

        $nodes['D']->addLink($nodes['E'], 3);

        $nodes['E']->addLink($nodes['B'], 4);

        return $nodes;
    }

    public function testCanReach()
    {
        $nodes = GraphTest::nodes();

        $this->assertTrue($nodes['A']->canReach($nodes['A']));
        $this->assertTrue($nodes['B']->canReach($nodes['B']));
        $this->assertTrue($nodes['B']->canReach($nodes['A']));
        $this->assertTrue($nodes['B']->canReach($nodes['F']));
        $this->assertTrue($nodes['B']->canReach($nodes['E']));

        $this->assertFalse($nodes['A']->canReach($nodes['B']));
        $this->assertFalse($nodes['B']->canReach($nodes['G']));
        $this->assertFalse($nodes['G']->canReach($nodes['B']));
    }

    public function testHopCount()
    {
        $nodes = GraphTest::nodes();

        $this->assertEquals(0, $nodes['A']->hopCount($nodes['A']));
        $this->assertEquals(1, $nodes['B']->hopCount($nodes['A']));
        $this->assertEquals(3, $nodes['C']->hopCount($nodes['F']));
    }

    public function testCost()
    {
        $nodes = GraphTest::nodes();

        $this->assertEquals(0, $nodes['A']->cost($nodes['A']));
        $this->assertEquals(6, $nodes['B']->cost($nodes['A']));
        $this->assertEquals(13, $nodes['C']->cost($nodes['F']));
    }

    public function testPath()
    {
        $nodes = GraphTest::nodes();

        $this->assertPath($nodes['A'], $nodes['A'], Path::leastCost(), 0, 0);
        $this->assertPath($nodes['B'], $nodes['A'], Path::leastCost(), 1, 6);
        $this->assertPath($nodes['B'], $nodes['F'], Path::leastCost(), 1, 5);
        $this->assertPath($nodes['D'], $nodes['B'], Path::leastCost(), 2, 7);
        $this->assertPath($nodes['C'], $nodes['F'], Path::leastCost(), 4, 13);

        $this->assertPath($nodes['B'], $nodes['E'], Path::leastHops(), 2, 15);
    }

    public function testPaths()
    {
        $nodes = GraphTest::nodes();

        $this->assertCount(1, $nodes['A']->paths($nodes['A']));
        $this->assertCount(1, $nodes['B']->paths($nodes['A']));
        $this->assertCount(3, $nodes['C']->paths($nodes['F']));
        $this->assertCount(0, $nodes['A']->paths($nodes['B']));
        $this->assertCount(0, $nodes['B']->paths($nodes['G']));
        $this->assertCount(0, $nodes['G']->paths($nodes['B']));
    }

    /**
     * @expectedException RuntimeException
     *
     * @dataProvider destinationNotReachableProvider
     */
    public function testDestinationNotReachable($from, $to)
    {
        $nodes = GraphTest::nodes();

        call_user_func_array($from, $to);
    }

    public function destinationNotReachableProvider()
    {
        $nodes = GraphTest::nodes();

        return array(
            array(array($nodes['A'], 'hopCount'), array($nodes['B'])),
            array(array($nodes['B'], 'hopCount'), array($nodes['G'])),
            array(array($nodes['G'], 'hopCount'), array($nodes['B'])),
            array(array($nodes['A'], 'cost'), array($nodes['B'])),
            array(array($nodes['B'], 'cost'), array($nodes['G'])),
            array(array($nodes['G'], 'cost'), array($nodes['B'])),
            array(
                array($nodes['A'], 'path'),
                array($nodes['B'], Path::leastCost())
            ),
            array(
                array($nodes['B'], 'path'),
                array($nodes['G'], Path::leastHops())
            ),
            array(
                array($nodes['G'], 'path'),
                array($nodes['B'], Path::leastCost())
            )
        );

        return array(
            array('A', 'B', 'hopCount'),
            array('B', 'G', 'hopCount'),
            array('G', 'B', 'hopCount'),
            array('A', 'B', 'cost'),
            array('B', 'G', 'cost'),
            array('G', 'B', 'cost'),
            array('A', 'B', 'path'),
            array('B', 'G', 'path'),
            array('G', 'B', 'path')
        );
    }

    protected function assertPath(
        $source,
        $destination,
        $strategy,
        $expectedHops,
        $expectedCost
    ) {
        $path = $source->path($destination, $strategy);

        $this->assertEquals($expectedHops, $path->hopCount());
        $this->assertEquals($expectedCost, $path->cost());
    }
}
