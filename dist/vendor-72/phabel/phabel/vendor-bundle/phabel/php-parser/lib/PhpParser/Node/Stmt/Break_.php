<?php

declare (strict_types=1);
namespace PhabelVendor\PhpParser\Node\Stmt;

use PhabelVendor\PhpParser\Node;
class Break_ extends Node\Stmt
{
    /** @var null|Node\Expr Number of loops to break */
    public $num;
    /**
     * Constructs a break node.
     *
     * @param (null | Node\Expr) $num Number of loops to break
     * @param array $attributes Additional attributes
     */
    public function __construct(Node\Expr $num = NULL, array $attributes = array())
    {
        $this->attributes = $attributes;
        $this->num = $num;
    }
    /**
     *
     */
    public function getSubNodeNames() : array
    {
        return ['num'];
    }
    /**
     *
     */
    public function getType() : string
    {
        return 'Stmt_Break';
    }
}