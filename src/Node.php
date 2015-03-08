<?php

/**
 * Understands a graph node
 */
class Node
{
    private $links;

    public function __construct(array $links = array())
    {
        $this->links = $links;
    }

    public function addLink(Node $other, $cost)
    {
        $this->links[] = new Link($other, $cost);
        return $this;
    }

    public function canReach(Node $destination)
    {
        return count($this->paths($destination)) > 0;
    }

    public function hopCount(Node $destination)
    {
        return $this->path($destination, Path::leastHops())->hopCount();
    }

    public function cost(Node $destination)
    {
        return $this->path($destination, Path::leastCost())->cost();
    }

    public function path(Node $destination, callable $strategy)
    {
        $paths = $this->paths($destination);

        if (empty($paths)) {
            throw new RuntimeException('Destination not reachable!');
        }

        return array_reduce($paths, $strategy, $paths[0]);
    }

    public function paths(Node $destination, array $visitedNodes = array())
    {
        if ($this === $destination) {
            return array(new Path());
        }

        if (in_array($this, $visitedNodes)) {
            return array();
        }

        $visitedNodes[] = $this;

        return array_reduce(
            $this->links,
            function ($paths, $link) use ($destination, $visitedNodes) {
                return array_merge(
                    $paths,
                    $link->_paths($destination, $visitedNodes)
                );
            },
            array()
        );
    }
}
