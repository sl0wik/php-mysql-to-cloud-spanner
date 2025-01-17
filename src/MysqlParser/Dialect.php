<?php

declare(strict_types=1);

namespace MgCosta\MysqlParser;

use MgCosta\MysqlParser\Contracts\DialectGenerator;

class Dialect implements DialectGenerator
{
    public function generateTableDetails(string $tableName): string
    {
        return 'DESCRIBE ' . $tableName;
    }

    public function generateTableKeysDetails(string $tableName): string
    {
        return "SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '" . $tableName . "'";
    }
}
