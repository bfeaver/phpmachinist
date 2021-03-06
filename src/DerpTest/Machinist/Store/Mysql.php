<?php
namespace DerpTest\Machinist\Store;
use DerpTest\Machinist\Store\SqlStore;

/**
 * MySQL Specific store support.
 */
class Mysql extends SqlStore
{
    /**
     * Dictionary of primary key values for tables
     * @var array
     */
    protected $key_dict;

    /**
     * Dictionary of columns for tables
     * @var array
     */
    protected $column_dict;

    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->key_dict = array();
        $this->column_dict = array();
    }

    public function primaryKey($table)
    {
        if (!isset($this->key_dict[$table])) {
            $stmt = $this->pdo()->query("SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'");
            $results = array();
            while ($row = $stmt->fetch(\PDO::FETCH_OBJ)) {
                $results[] = $row->Column_name;
            }

            if (count($results) < 1) {
                $results = $this->columns($table);
            } else if (is_array($results) && count($results) == 1) {
                $results = array_pop($results);
            }
            $this->key_dict[$table] = $results;
        }
        return $this->key_dict[$table];
    }

    protected function columns($table)
    {
        if (!isset($this->column_dict[$table])) {
            $stmt = $this->pdo()->query("DESCRIBE `$table`");
            $columns = array();
            while ($row = $stmt->fetch()) {
                $columns[] = $row['Field'];
            }
            $this->column_dict[$table] = $columns;
        }
        return $this->column_dict[$table];
    }

    public function quoteTable($table)
    {
        return '`' . $table . '`';
    }

    public function quoteColumn($column)
    {
        return '`' . $column . '`';
    }
}