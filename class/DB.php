<?php

class DB
{

    public $mysqli;
    public $lastId;
    protected static $instance;

    protected function __construct()
    {
        $db = require __DIR__ . '/../config_db.php';
        $mysqli = new mysqli($db['server'], $db['user'], $db['pass'], $db['name']);
        if (mysqli_connect_errno()) {
            printf("Подключение к серверу MySQL невозможно. Код ошибки: %s\n", mysqli_connect_error());
            exit;
        }
        $mysqli->query("SET NAMES utf8");
        $this->mysqli = $mysqli;

    }

    protected function  __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * стандартный синглтон
     * @return Db
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    public function simple_query($query)
    {
        //echo $query;
        return $this->mysqli->query($query);
    }

    public function queryInsert($table_name, $arrValue)
    {
        $query = 'INSERT INTO `' . $table_name . '` SET ';
        foreach ($arrValue as $key => $val) {
            $query .= '`' . $key . '`=\'' . $val . '\',';
        }
        $query = substr($query, 0, -1);
        $this->mysqli->query($query);
        $this->lastId = mysqli_insert_id($this->mysqli);
    }

    public function querySelect($table_name, $arrValue, $where)
    {
        $query = 'SELECT ';
        if (!is_array($arrValue)) {
            $query .= ' * ';
        } else {
            foreach ($arrValue as $val) {
                $query .= '`' . $val . '`,';
            }
            $query = substr($query, 0, -1);
        }
        $query .= ' FROM `' . $table_name . '` WHERE ' . $where;
        return $this->mysqli->query($query);
    }

    public function queryUpdate($table_name, $arrValue, $where)
    {
        unset($arrValue['id']);
        $query = 'UPDATE `' . $table_name . '` SET ';
        foreach ($arrValue as $key => $value) {
            $query .= '`' . $key . '`=\'' . $value . '\',';
        }
        $query = substr($query, 0, -1);
        $query .= ' WHERE ' . $where;
        $this->mysqli->query($query);
    }

    public function queryRow($table_name, $arrValue, $where){
        $query = 'SELECT ';
        if (!is_array($arrValue)) {
            $query .= ' * ';
        } else {
            foreach ($arrValue as $val) {
                $query .= '`' . $val . '`,';
            }
            $query = substr($query, 0, -1);
        }
        $query .= ' FROM `' . $table_name . '` WHERE ' . $where;
        return $this->mysqli->query($query)->fetch_assoc();
    }
}