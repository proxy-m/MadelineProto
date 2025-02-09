<?php

declare (strict_types=1);
namespace PhabelVendor\PhpParser\Node\Expr;

use PhabelVendor\PhpParser\Node;
use PhabelVendor\PhpParser\Node\Expr;
class FuncCall extends CallLike
{
    /** @var Node\Name|Expr Function name */
    public $name;
    /** @var array<Node\Arg|Node\VariadicPlaceholder> Arguments */
    public $args;
    /**
     * Constructs a function call node.
     *
     * @param (Node\Name | Expr) $name Function name
     * @param array<(Node\Arg | Node\VariadicPlaceholder)> $args Arguments
     * @param array $attributes Additional attributes
     */
    public function __construct($name, array $args = array(), array $attributes = array())
    {
        $this->attributes = $attributes;
        $this->name = $name;
        $this->args = $args;
    }
    /**
     *
     */
    public function getSubNodeNames() : array
    {
        return ['name', 'args'];
    }
    /**
     *
     */
    public function getType() : string
    {
        return 'Expr_FuncCall';
    }
    /**
     *
     */
    public function getRawArgs() : array
    {
        return $this->args;
    }
}