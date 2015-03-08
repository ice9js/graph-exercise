<?php

/**
 * Understands the link to a graph node
 */
class Link
{
    private $target;

    private $cost;

    public function __construct(Node $target, $cost)
    {
        $this->target = $target;
        $this->cost = $cost;
    }

    public function _paths($destination, array $visitedNodes)
    {
        return array_map(function ($path) {
            return $path->prepend($this);
        }, $this->target->paths($destination, $visitedNodes));
    }

    public static function totalCost($links)
    {
        return array_reduce($links, function ($total, $link) {
            return $total + $link->cost;
        }, 0);
    }
}
