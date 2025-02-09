<?php

declare (strict_types=1);
namespace PhabelVendor\PhpParser\Node\Expr;

use PhabelVendor\PhpParser\Node\Expr;
use PhabelVendor\PhpParser\Node\Name;
class ConstFetch extends Expr
{
    /** @var Name Constant name */
    public $name;
    /**
     * Constructs a const fetch node.
     *
     * @param Name $name Constant name
     * @param array $attributes Additional attributes
     */
    public function __construct(Name $name, array $attributes = array())
    {
        $this->attributes = $attributes;
        $this->name = $name;
    }
    /**
     *
     */
    public function getSubNodeNames() : array
    {
        return ['name'];
    }
    /**
     *
     */
    public function getType() : string
    {
        return 'Expr_ConstFetch';
    }
}