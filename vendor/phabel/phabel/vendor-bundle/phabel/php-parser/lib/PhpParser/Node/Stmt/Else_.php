<?php

declare (strict_types=1);
namespace PhabelVendor\PhpParser\Node\Stmt;

use PhabelVendor\PhpParser\Node;
class Else_ extends Node\Stmt
{
    /** @var Node\Stmt[] Statements */
    public $stmts;
    /**
     * Constructs an else node.
     *
     * @param Node\Stmt[] $stmts Statements
     * @param array $attributes Additional attributes
     */
    public function __construct(array $stmts = array(), array $attributes = array())
    {
        $this->attributes = $attributes;
        $this->stmts = $stmts;
    }
    /**
     *
     */
    public function getSubNodeNames() : array
    {
        return ['stmts'];
    }
    /**
     *
     */
    public function getType() : string
    {
        return 'Stmt_Else';
    }
}