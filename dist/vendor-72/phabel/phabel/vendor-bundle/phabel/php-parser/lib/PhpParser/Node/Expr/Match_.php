<?php

declare (strict_types=1);
namespace PhabelVendor\PhpParser\Node\Expr;

use PhabelVendor\PhpParser\Node;
use PhabelVendor\PhpParser\Node\MatchArm;
class Match_ extends Node\Expr
{
    /** @var Node\Expr */
    public $cond;
    /** @var MatchArm[] */
    public $arms;
    /**
     * @param MatchArm[] $arms
     */
    public function __construct(Node\Expr $cond, array $arms = array(), array $attributes = array())
    {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->arms = $arms;
    }
    /**
     *
     */
    public function getSubNodeNames() : array
    {
        return ['cond', 'arms'];
    }
    /**
     *
     */
    public function getType() : string
    {
        return 'Expr_Match';
    }
}