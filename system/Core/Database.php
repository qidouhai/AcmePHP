<?php

namespace NC\Core;

use PDO;
use PDOException;
use App\Config\Database as DbConfig;

class Database
{
    private static $instance;
    private $conn;
    private $SQL;

    public function __construct()
    {
        try {
            $config = [
                'host' => DbConfig::$host,
                'username' => DbConfig::$username,
                'passswd' => DbConfig::$passswd,
                'dbname' => DbConfig::$dbname,
                'charset' => DbConfig::$charset
            ];
            $this->conn = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']}",
                $config['username'],
                $config['passswd'],
                [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'' . strtolower($config['charset']) . '\'']
            );
        } catch (PDOException $e) {
            die('数据库连接失败：' . $e->getMessage());
        }
    }

    /**
     * 查询
     * @param string $table 表名
     * @param string|array $columns 返回字段
     * @param array $where 条件
     * @param array $join 表连接
     */
    public function select(string $table, $columns = '*', array $where = [], array $join = [])
    {
        $columnsSQL = $this->columnsContext($columns);
        $whereArr = !empty($where) ? $this->whereContext($table, $where) : ['', []];
        $whereSQL = $whereArr[0];
        $whereParams = $whereArr[1];
        $joinSQL = !empty($join) ? $this->joinContext($table, $join) : '';
        $this->SQL = "SELECT {$columnsSQL} FROM `$table`{$joinSQL}{$whereSQL}";
        $sth = $this->conn->prepare($this->SQL);
        $sth->execute($whereParams);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 插入
     * @param string $table 表名
     * @param array $data 插入数据
     */
    public function insert(string $table, array $data)
    {
        $insertArr = $this->insertContext($data);
        $insertSQL = $insertArr[0];
        $insertParams = $insertArr[1];
        $this->SQL = "INSERT INTO `{$table}` {$insertSQL}";
        $sth = $this->conn->prepare($this->SQL);
        $sth->execute($insertParams);
        return $sth;
    }

    /**
     * 删除
     * @param string $table 表名
     * @param array $where 条件
     */
    public function delect(string $table, array $where = [])
    {
        $whereArr = !empty($where) ? $this->whereContext($table, $where) : ['', []];
        $whereSQL = $whereArr[0];
        $whereParams = $whereArr[1];
        $this->SQL = "DELETE FROM `$table`{$whereSQL}";
        $sth = $this->conn->prepare($this->SQL);
        $sth->execute($whereParams);
        return $sth;
    }

    /**
     * 更新
     * @param string $table 表名
     * @param array $data 更新数据
     * @param array $where 条件
     */
    public function update(string $table, array $data, array $where = [])
    {
        $updateArr = $this->updateContext($data);
        $updateSQL = $updateArr[0];
        $updateParams = $updateArr[1];
        $whereArr = !empty($where) ? $this->whereContext($table, $where) : ['', []];
        $whereSQL = $whereArr[0];
        $whereParams = $whereArr[1];
        $this->SQL = "UPDATE `{$table}` SET {$updateSQL}{$whereSQL}";
        $sth = $this->conn->prepare($this->SQL);
        $sth->execute(array_merge($updateParams, $whereParams));
        return $sth;
    }

    /**
     * 返回最后执行 SQL
     */
    public function last()
    {
        return $this->SQL;
    }

    private function updateContext(array $data)
    {
        return ['`' . implode('` = ?, `', array_keys($data)) . '` = ?', array_values($data)];
    }

    private function columnsContext($columns)
    {
        if (is_array($columns)) {
            array_walk($columns, [$this, 'columnsPending']);
            return implode(', ', $columns);
        } else {
            return $this->columnsPending($columns);
        }
    }

    private function columnsPending(string &$column)
    {
        preg_match('/[^\(]+/', $column, $matches);
        $columnStr = $this->keyFormat($matches[0]);
        preg_match('/(?<=\()[^\)]+/', $column, $matches);
        $asColumn = isset($matches[0]) ? 'AS ' . "`{$matches[0]}`" : '';
        $column = "{$columnStr} {$asColumn}";
        return $column;
    }

    private function whereContext(string $table, array $where)
    {
        $params = [];
        $whereSQL = $this->whereEach($table, $where, 'AND', $params);
        return [" WHERE {$whereSQL}", $params];
    }

    private function whereEach(string $table, array $whereArr, string $separator, array &$params)
    {
        $whereSQL = '';
        foreach ($whereArr as $k => $v) {
            if ($k === 'AND' || $k === 'OR') {
                end($whereArr);
                if ($k === key($whereArr)) {
                    $whereSQL .= '(' . $this->whereEach($table, $v, $k, $params) . ')';
                } else {
                    $whereSQL .= '(' . $this->whereEach($table, $v, $k, $params) . ") {$separator} ";
                }
            } else {
                preg_match('/(?<=\[)[^\]]+/', $k, $matches);
                $operator = empty($matches) ? '=' : $matches[0];
                $k = empty($matches) ? $k : $matches[0];
                preg_match('/^.*(?=\[)/', $k, $matches);
                $k = $this->keyFormat(!strpos($k, '.') ? "{$table}.{$k}" : $k);
                $whereSQL .= "{$k} {$operator} ? {$separator} ";
                array_push($params, $v);
            }
        }
        return rtrim($whereSQL, " {$separator} ");
    }

    private function keyFormat(string $key)
    {
        return '`' . implode('`.`', explode('.', $key)) . '`';
    }

    private function joinContext(string $table, array $join)
    {
        $joinSQLArr = [];
        foreach ($join as $k => $v) {
            preg_match('/(?<=\[)[^\]]+/', $k, $matches);
            $joinType = $this->joinType(empty($matches) ? '>' : $matches[0]);
            preg_match('/(?<=\]).*/', $k, $matches);
            $joinTable = empty($matches) ? $k : $matches[0];
            $joinON = '';
            if (is_array($v)) {
                $joinONArr = [];
                foreach ($v as $k1 => $v1) {
                    $k1 = $this->keyFormat(!strpos($k1, '.') ? "{$table}.{$k1}" : $k1);
                    $v1 = $this->keyFormat(!strpos($v1, '.') ? "{$joinTable}.{$v1}" : $v1);
                    array_push($joinONArr, "{$k1} = {$v1}");
                }
                $joinON = 'ON ' . implode(' AND ', $joinONArr);
            } else if (is_string($v)) {
                $joinON = "USING(`{$v}`)";
            }
            array_push($joinSQLArr, "{$joinType} `{$joinTable}` {$joinON}");
        }
        return ' ' . implode(' ', $joinSQLArr);
    }

    private function insertContext(array $data)
    {
        $columns = '';
        $values = '';
        $params = [];
        $dataLen = count($data);
        if ($dataLen === count($data, 1)) {
            $columns = '(`' . implode('`, `', array_keys($data)) . '`)';
            $valuesArr = array_pad([], $dataLen, '?');
            $values = '(' . implode(', ', $valuesArr) . ')';
            $params = array_values($data);
        } else {
            $columns = '(`' . implode('`, `', array_keys($data[0])) . '`)';
            $valuesArr = [];
            foreach (array_pad([], $dataLen, array_pad([], count($data[0]), '?')) as $k => $v) {
                array_push($valuesArr, '(' . implode(', ', $v) . ')');
                $params = array_merge($params, array_values($data[$k]));
            }
            $values = implode(', ', $valuesArr);
        }
        return ["{$columns} VALUES {$values}", $params];
    }

    private function joinType(string $operator)
    {
        switch ($operator) {
            case '>':
                return 'LEFT JOIN';
                break;
            case '<':
                return 'RIGHT JOIN';
                break;
            case '><':
                return 'INNER JOIN';
                break;
            case '<>':
                return 'FULL OUTER JOIN';
                break;
            default:
                return $operator;
                break;
        }
    }

    public static function getInstance()
    {
        if (!(self::$instance instanceof Database)) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
}
