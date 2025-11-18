<?php

namespace App\Factory;

class PdoFactory
{
    public function createConnection(string $dsn): \PDO
    {
        return new \PDO($dsn);
    }
}

