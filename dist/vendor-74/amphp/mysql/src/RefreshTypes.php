<?php

namespace Amp\Mysql;

final class RefreshTypes
{
    const REFRESH_GRANT = 1;
    const REFRESH_LOG = 2;
    const REFRESH_TABLES = 4;
    const REFRESH_HOSTS = 8;
    const REFRESH_STATUS = 16;
    const REFRESH_THREADS = 32;
    const REFRESH_SLAVE = 64;
    const REFRESH_MASTER = 128;
}