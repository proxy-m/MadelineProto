<?php

/**
 * Update feeder loop.
 *
 * This file is part of MadelineProto.
 * MadelineProto is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * MadelineProto is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU General Public License along with MadelineProto.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Daniil Gentili <daniil@daniil.it>
 * @copyright 2016-2020 Daniil Gentili <daniil@daniil.it>
 * @license   https://opensource.org/licenses/AGPL-3.0 AGPLv3
 *
 * @link https://docs.madelineproto.xyz MadelineProto documentation
 */
namespace danog\MadelineProto\Loop\Generic;

use danog\Loop\Generic\PeriodicLoop as GenericPeriodicLoop;
use danog\MadelineProto\Loop\InternalLoop;
use danog\MadelineProto\MTProto;
/**
 * {@inheritDoc}
 *
 * @internal For internal use
 */
class PeriodicLoopInternal extends GenericPeriodicLoop
{
    use InternalLoop {
        __construct as private init;
    }
    /**
     * Constructor.
     *
     * @param MTProto  $API      API instance
     * @param callable $callable Method
     * @param string   $name     Loop name
     * @param int|null $interval Interval
     */
    public function __construct(MTProto $API, callable $callable, string $name, ?int $interval)
    {
        $this->init($API);
        parent::__construct($callable, $name, $interval);
    }
}