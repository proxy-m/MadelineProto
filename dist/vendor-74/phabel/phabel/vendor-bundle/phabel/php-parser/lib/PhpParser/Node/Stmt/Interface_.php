<?php

declare (strict_types=1);
namespace PhabelVendor\PhpParser\Node\Stmt;

use PhabelVendor\PhpParser\Node;
class Interface_ extends ClassLike
{
    /** @var Node\Name[] Extended interfaces */
    public $extends;
    /**
    * Constructs a class node.
    *
    * @param (string | Node\Identifier) $name Name
    * @param array $subNodes Array of the following optional subnodes:
    'extends'    => array(): Name of extended interfaces
    'stmts'      => array(): Statements
    'attrGroups' => array(): PHP attribute groups
    * @param array $attributes Additional attributes
    */
    public function __construct($name, array $subNodes = array(), array $attributes = array())
    {
        $this->attributes = $attributes;
        $this->name = \is_string($name) ? new Node\Identifier($name) : $name;
        $this->extends = $subNodes['extends'] ?? [];
        $this->stmts = $subNodes['stmts'] ?? [];
        $this->attrGroups = $subNodes['attrGroups'] ?? [];
    }
    /**
     *
     */
    public function getSubNodeNames() : array
    {
        return ['attrGroups', 'name', 'extends', 'stmts'];
    }
    /**
     *
     */
    public function getType() : string
    {
        return 'Stmt_Interface';
    }
}