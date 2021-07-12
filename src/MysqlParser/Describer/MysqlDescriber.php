<?php

declare(strict_types=1);

namespace MgCosta\MysqlParser\Describer;

use MgCosta\MysqlParser\Contracts\DescribeExtractor;

class MysqlDescriber implements DescribeExtractor
{
    public function getTableDetails(string $tableName): string
    {
        return 'DESCRIBE ' . $tableName;
    }

    public function getTableKeysDetails(string $tableName): string
    {
        return "SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '" . $tableName . "'";
    }
}