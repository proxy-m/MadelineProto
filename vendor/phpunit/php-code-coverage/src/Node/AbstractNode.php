<?php

declare (strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Node;

use const DIRECTORY_SEPARATOR;
use function array_merge;
use function str_replace;
use function substr;
use Countable;
use SebastianBergmann\CodeCoverage\Util\Percentage;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
abstract class AbstractNode implements Countable
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $pathAsString;
    /**
     * @var array
     */
    private $pathAsArray;
    /**
     * @var AbstractNode
     */
    private $parent;
    /**
     * @var string
     */
    private $id;
    public function __construct(string $name, self $parent = NULL)
    {
        if (substr($name, -1) === DIRECTORY_SEPARATOR) {
            $name = substr($name, 0, -1);
        }
        $this->name = $name;
        $this->parent = $parent;
    }
    public function name() : string
    {
        return $this->name;
    }
    public function id() : string
    {
        if ($this->id === null) {
            $parent = $this->parent();
            if ($parent === null) {
                $this->id = 'index';
            } else {
                $parentId = $parent->id();
                if ($parentId === 'index') {
                    $this->id = str_replace(':', '_', $this->name);
                } else {
                    $this->id = $parentId . '/' . $this->name;
                }
            }
        }
        return $this->id;
    }
    public function pathAsString() : string
    {
        if ($this->pathAsString === null) {
            if ($this->parent === null) {
                $this->pathAsString = $this->name;
            } else {
                $this->pathAsString = $this->parent->pathAsString() . DIRECTORY_SEPARATOR . $this->name;
            }
        }
        return $this->pathAsString;
    }
    public function pathAsArray() : array
    {
        if ($this->pathAsArray === null) {
            if ($this->parent === null) {
                $this->pathAsArray = [];
            } else {
                $this->pathAsArray = $this->parent->pathAsArray();
            }
            $this->pathAsArray[] = $this;
        }
        return $this->pathAsArray;
    }
    public function parent() : ?self
    {
        return $this->parent;
    }
    public function percentageOfTestedClasses() : Percentage
    {
        return Percentage::fromFractionAndTotal($this->numberOfTestedClasses(), $this->numberOfClasses());
    }
    public function percentageOfTestedTraits() : Percentage
    {
        return Percentage::fromFractionAndTotal($this->numberOfTestedTraits(), $this->numberOfTraits());
    }
    public function percentageOfTestedClassesAndTraits() : Percentage
    {
        return Percentage::fromFractionAndTotal($this->numberOfTestedClassesAndTraits(), $this->numberOfClassesAndTraits());
    }
    public function percentageOfTestedFunctions() : Percentage
    {
        return Percentage::fromFractionAndTotal($this->numberOfTestedFunctions(), $this->numberOfFunctions());
    }
    public function percentageOfTestedMethods() : Percentage
    {
        return Percentage::fromFractionAndTotal($this->numberOfTestedMethods(), $this->numberOfMethods());
    }
    public function percentageOfTestedFunctionsAndMethods() : Percentage
    {
        return Percentage::fromFractionAndTotal($this->numberOfTestedFunctionsAndMethods(), $this->numberOfFunctionsAndMethods());
    }
    public function percentageOfExecutedLines() : Percentage
    {
        return Percentage::fromFractionAndTotal($this->numberOfExecutedLines(), $this->numberOfExecutableLines());
    }
    public function percentageOfExecutedBranches() : Percentage
    {
        return Percentage::fromFractionAndTotal($this->numberOfExecutedBranches(), $this->numberOfExecutableBranches());
    }
    public function percentageOfExecutedPaths() : Percentage
    {
        return Percentage::fromFractionAndTotal($this->numberOfExecutedPaths(), $this->numberOfExecutablePaths());
    }
    public function numberOfClassesAndTraits() : int
    {
        return $this->numberOfClasses() + $this->numberOfTraits();
    }
    public function numberOfTestedClassesAndTraits() : int
    {
        return $this->numberOfTestedClasses() + $this->numberOfTestedTraits();
    }
    public function classesAndTraits() : array
    {
        return array_merge($this->classes(), $this->traits());
    }
    public function numberOfFunctionsAndMethods() : int
    {
        return $this->numberOfFunctions() + $this->numberOfMethods();
    }
    public function numberOfTestedFunctionsAndMethods() : int
    {
        return $this->numberOfTestedFunctions() + $this->numberOfTestedMethods();
    }
    public abstract function classes() : array;
    public abstract function traits() : array;
    public abstract function functions() : array;
    /**
     * @psalm-return array{linesOfCode: int, commentLinesOfCode: int, nonCommentLinesOfCode: int}
     */
    public abstract function linesOfCode() : array;
    public abstract function numberOfExecutableLines() : int;
    public abstract function numberOfExecutedLines() : int;
    public abstract function numberOfExecutableBranches() : int;
    public abstract function numberOfExecutedBranches() : int;
    public abstract function numberOfExecutablePaths() : int;
    public abstract function numberOfExecutedPaths() : int;
    public abstract function numberOfClasses() : int;
    public abstract function numberOfTestedClasses() : int;
    public abstract function numberOfTraits() : int;
    public abstract function numberOfTestedTraits() : int;
    public abstract function numberOfMethods() : int;
    public abstract function numberOfTestedMethods() : int;
    public abstract function numberOfFunctions() : int;
    public abstract function numberOfTestedFunctions() : int;
}