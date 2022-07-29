<?php

declare (strict_types=1);
namespace PhabelVendor\PhpParser\Node\Expr;

use PhabelVendor\PhpParser\Node\Expr;
class UnaryMinus extends Expr
{
    /** @var Expr Expression */
    public $expr;
    /**
     * Constructs a unary minus node.
     *
     * @param Expr $expr Expression
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $expr, array $attributes = array())
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
        return 'Expr_UnaryMinus';
    }
}