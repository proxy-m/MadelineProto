<?php

declare (strict_types=1);
namespace PhabelVendor\PhpParser\Node\Expr;

use PhabelVendor\PhpParser\Node\Expr;
class Yield_ extends Expr
{
    /** @var null|Expr Key expression */
    public $key;
    /** @var null|Expr Value expression */
    public $value;
    /**
     * Constructs a yield expression node.
     *
     * @param (null | Expr) $value Value expression
     * @param (null | Expr) $key Key expression
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $value = NULL, Expr $key = NULL, array $attributes = array())
    {
        $this->attributes = $attributes;
        $this->key = $key;
        $this->value = $value;
    }
    /**
     *
     */
    public function getSubNodeNames() : array
    {
        return ['key', 'value'];
    }
    /**
     *
     */
    public function getType() : string
    {
        return 'Expr_Yield';
    }
}