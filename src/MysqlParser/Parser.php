<?php

declare(strict_types=1);

namespace MgCosta\MysqlParser;

use MgCosta\MysqlParser\Exceptions\ParserException;
use MgCosta\MysqlParser\Contracts\ParserBuildable;
use MgCosta\MysqlParser\Contracts\MysqlParsable;
use MgCosta\MysqlParser\Contracts\Processable;
use MgCosta\MysqlParser\Processor\CloudSpanner;
use RuntimeException;

class Parser implements MysqlParsable, ParserBuildable
{
    /**
     * The name of the table to parse
     *
     * @var string
     */
    protected $tableName;

    /**
     * The described table with columns from MySQL
     *
     * @var array
     */
    protected $describedTable;

    /**
     * The described table keys from MySQL
     *
     * @var array
     */
    protected $describedKeys;

    /**
     * The string with mysql schema to parse
     *
     * @var string
     */
    protected $schema;

    /**
     * The grammatical library to parse the ddl from Mysql
     *
     * @var Processable|CloudSpanner
     */
    protected $processor;

    public function __construct(Processable $processor = null)
    {
        $this->processor = $processor ?? new CloudSpanner();
    }

    public function setTableName(string $tableName): Parser
    {
        $this->tableName = strtolower($tableName);
        return $this;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setDescribedTable(array $table): Parser
    {
        $requiredKeys = ['Field', 'Type', 'Null', 'Key', 'Default', 'Extra'];
        $table = $this->parseAndValidateArrayKeys($table, $requiredKeys, 'described table');

        $this->describedTable = $table;
        return $this;
    }

    public function getDescribedTable(): array
    {
        return $this->describedTable;
    }

    public function setKeys(array $keys): Parser
    {
        $requiredKeys = [
            'TABLE_NAME', 'COLUMN_NAME', 'CONSTRAINT_NAME',
            'REFERENCED_TABLE_NAME', 'REFERENCED_COLUMN_NAME'
        ];
        $keys = $this->parseAndValidateArrayKeys($keys, $requiredKeys, 'described keys');

        $this->describedKeys = $keys;
        return $this;
    }

    public function getDescribedKeys(): array
    {
        return $this->describedKeys;
    }

    /**
     * @return array
     * @throws ParserException
     */
    public function toDDL(): array
    {
        if (!empty($this->describedTable) && !empty($this->describedKeys)) {
            return $this->processor->parseDescribedSchema($this);
        }

        throw new ParserException("You must define a described table/keys to parse");
    }

    private function parseAndValidateArrayKeys(array $data, array $keys, string $origin): array
    {
        foreach ($data as $key => $column) {
            if (is_object($column)) {
                $column = (array)$column;
                $data[$key] = $column;
            }
            if (array_diff_key($column, array_flip($keys))) {
                throw new RuntimeException("There's invalid column keys for the " . $origin);
            }
        }
        return $data;
    }
}
