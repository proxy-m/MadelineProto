<?php

namespace Amp\Sql;

use Amp\Promise;
interface Link extends Executor
{
    /**
     * Starts a transaction on a single connection.
     *
     * @param int $isolation Transaction isolation level.
     *
     * @return Promise<Transaction>
     */
    public function beginTransaction(int $isolation = 1) : Promise;
}