<?php

declare (strict_types=1);
namespace PhabelVendor\PhpParser\Node\Stmt;

use PhabelVendor\PhpParser\Node;
/**
 * Represents statements of type "expr;"
 */
class Expression extends Node\Stmt
{
    /** @var Node\Expr Expression */
    public $expr;
    /**
     * Constructs an expression statement.
     *
     * @param Node\Expr $expr Expression
     * @param array $attributes Additional attributes
     */
    public function __construct(Node\Expr $expr, array $attributes = array())
    {
        $this->attributes = $attributes;
        $this->expr = $expr;
    }
    /**
     *
     */
    public function getSubNodeNames() : array
    {
        return ['expr'];
    }
    /**
     *
     */
    public function getType() : string
    {
        return 'Stmt_Expression';
    }
}