<?php

namespace Amp\Sql;

use Amp\Promise;
interface Statement extends TransientResource
{
    /**
     * @param mixed[] $params
     *
     * @return Promise<(CommandResult | ResultSet)>
     */
    public function execute(array $params = array()) : Promise;
    /**
     * @return string The SQL string used to prepare the statement.
     */
    public function getQuery() : string;
}