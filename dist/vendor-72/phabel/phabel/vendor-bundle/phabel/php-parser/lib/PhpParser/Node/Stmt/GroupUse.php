<?php

declare (strict_types=1);
namespace PhabelVendor\PhpParser\Node\Stmt;

use PhabelVendor\PhpParser\Node\Name;
use PhabelVendor\PhpParser\Node\Stmt;
class GroupUse extends Stmt
{
    /** @var int Type of group use */
    public $type;
    /** @var Name Prefix for uses */
    public $prefix;
    /** @var UseUse[] Uses */
    public $uses;
    /**
     * Constructs a group use node.
     *
     * @param Name $prefix Prefix for uses
     * @param UseUse[] $uses Uses
     * @param int $type Type of group use
     * @param array $attributes Additional attributes
     */
    public function __construct(Name $prefix, array $uses, int $type = 1, array $attributes = array())
    {
        $this->attributes = $attributes;
        $this->type = $type;
        $this->prefix = $prefix;
        $this->uses = $uses;
    }
    /**
     *
     */
    public function getSubNodeNames() : array
    {
        return ['type', 'prefix', 'uses'];
    }
    /**
     *
     */
    public function getType() : string
    {
        return 'Stmt_GroupUse';
    }
}