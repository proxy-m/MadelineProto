<?php

declare (strict_types=1);
namespace PhabelVendor\PHPStan\PhpDocParser\Ast\ConstExpr;

use PhabelVendor\PHPStan\PhpDocParser\Ast\NodeAttributes;
use function implode;
class ConstExprArrayNode implements ConstExprNode
{
    use NodeAttributes;
    /** @var ConstExprArrayItemNode[] */
    public $items;
    /**
     * @param ConstExprArrayItemNode[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }
    /**
     *
     */
    public function __toString() : string
    {
        return '[' . implode(', ', $this->items) . ']';
    }
}