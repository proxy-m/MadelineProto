<?php

/**
 * Callback module.
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
namespace danog\MadelineProto\Wrappers;

use Amp\Promise;
use danog\MadelineProto\Tools;
/**
 * Manages clicking buttons.
 */
trait Button
{
    /**
     * Click on button.
     *
     * @internal
     *
     * @param bool $donotwait
     * @param string $method
     * @param array $parameters
     *
     * @return Promise|true
     */
    public function clickInternal(bool $donotwait, string $method, array $parameters)
    {
        $internal = $donotwait ? 'methodCallAsyncWrite' : 'methodCallAsyncRead';
        $result = $this->{$internal}($method, $parameters);
        if ($donotwait) {
            Tools::callFork($result);
        }
        return $donotwait ? true : $result;
    }
}