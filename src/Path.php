<?php

/**
 * Understands a path through Nodes
 */
class Path
{
    private $links = array();

    public function prepend(Link $link)
    {
        array_unshift($this->links, $link);
        return $this;
    }

    public function cost()
    {
        return Link::totalCost($this->links);
    }

    public function hopCount()
    {
        return count($this->links);
    }

    public static function leastCost() {
        return function ($left, $right) {
            return $left->cost() < $right->cost() ? $left : $right;
        };
    }

    public static function leastHops() {
        return function ($left, $right) {
            return $left->hopCount() < $right->hopCount() ? $left : $right;
        };
    }
}
