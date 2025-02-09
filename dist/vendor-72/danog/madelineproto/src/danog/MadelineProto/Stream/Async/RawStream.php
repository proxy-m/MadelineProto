<?php

/**
 * Raw stream helper trait.
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
namespace danog\MadelineProto\Stream\Async;

use Amp\Promise;
/**
 * Raw stream helper trait.
 *
 * Wraps the asynchronous generator methods with asynchronous promise-based methods
 *
 * @author Daniil Gentili <daniil@daniil.it>
 */
trait RawStream
{
    /**
     *
     */
    public function read() : Promise
    {
        return \danog\MadelineProto\Tools::call($this->readGenerator());
    }
    /**
     *
     */
    public function write(string $data) : Promise
    {
        return \danog\MadelineProto\Tools::call($this->writeGenerator($data));
    }
    /**
     *
     */
    public function end(string $finalData = '') : Promise
    {
        if (\method_exists($this, 'endGenerator')) {
            return \danog\MadelineProto\Tools::call($this->endGenerator($finalData));
        }
        $promise = $this->write($finalData);
        $promise->onResolve(function () {
            return $this->disconnect();
        });
        return $promise;
    }
}