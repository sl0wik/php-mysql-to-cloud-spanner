<?php

namespace MgCosta\MysqlParser\Contracts;

use MgCosta\MysqlParser\Parser;

interface ParserBuildable
{
    public function setTableName(string $tableName): Parser;
    public function setDescribedTable(array $table): Parser;
    public function setKeys(array $keys): Parser;
    public function getDescribedTable(): array;
    public function getDescribedKeys(): array;
    public function getTableName(): string;
}
