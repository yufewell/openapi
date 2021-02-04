<?php


namespace mysql;

use PDO;
use lib\DB;
use lib\Log;

abstract class Base
{
    protected static $instance   = null;    // 类的实例
    protected static $connection = null;    // 数据库连接实例
    protected static $table      = '';
    protected static $db_prefix  = '';
    protected $fields            = '*';
    protected $condition         = '';
    protected $order             = '';
    protected $limit             = '';
    protected $sql               = '';

    /**
     * 构造方法
     * 获取配置, 连接数据库, 并保存实例
     * 设置表前缀
     */
    public function __construct() {
        self::$connection = self::connect();
    }

    /**
     * 返回数据库连接对象
     *
     * @param $config
     * @return null|PDO
     */
    public static function connect($config = []) {
        if (empty($config)) {
            $config = get_config();

            $config = $config['mysql'];
        }

        if (self::$connection !== null) {
            return self::$connection;
        }

        $username = $config['username'];
        $password = $config['password'];
        $host = $config['host'];
        $db = $config['database'];
        $port = $config['port'];

        self::$connection = new PDO("mysql:dbname=$db;host=$host;port=$port", $username, $password,
            [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'"]);

        if (!self::$connection) {
            Log::write('数据库连接失败', $config);
        }
        return self::$connection;
    }

    /**
     * 设置table, 获取实例
     *
     * @param string $table
     * @return DB|null
     */
    public static function table($table = '') {
        self::$table = self::$db_prefix . strtolower($table);

        if (self::$instance === null)
            self::$instance = new static();

        return self::$instance;
    }

    /**
     * 获取instance
     *
     * @return DB|null
     */
    public static function getInstance() {
        if (self::$instance === null)
            self::$instance = new static();

        return self::$instance;
    }

    /**
     * 执行sql语句, 并返回结果
     *
     * @param $sql
     * @return bool
     */
    private function query($sql){
        if (defined('APP_DEBUG') && APP_DEBUG === true) {
            Log::write($sql, [], 'SQL');
        }

        $stmt = self::$connection->prepare($sql);
        $rst = $stmt->execute();

        if (!$rst) {
            Log::warning('query error, SQL:'.$sql, $stmt->errorInfo());
        }
        return $rst;
    }

    /**
     * 查询数据
     *
     * @param $sql
     * @return array|bool
     */
    public function getAll($sql) {
        $stmt = self::$connection->prepare($sql);
        $rst = $stmt->execute();
        if (!$rst) {
            Log::warning('getAll error, SQL:'.$sql, $stmt->errorInfo());
            return $rst;
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 查询一条数据
     *
     * @param $sql
     * @return bool|mixed
     */
    public function getOne($sql) {
        $stmt = self::$connection->prepare($sql);
        $rst = $stmt->execute();
        if (!$rst) {
            Log::warning('getOne error, SQL:'.$sql, $stmt->errorInfo());
            return $rst;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 设置查询字段
     *
     * @param $fields
     * @return null|static
     */
    public function field($fields) {
        $this->fields = $fields;
        return self::$instance;
    }

    /**
     * 拼接sql
     *
     */
    private function _make_select_sql() {
        $this->sql = 'SELECT ' . $this->fields . ' FROM ' . '`' . self::$table . '`';
        if (!empty($this->condition)) {
            $this->sql .= ' WHERE ' . $this->condition . ' ';
        }
        if (!empty($this->order)) {
            $this->sql .= ' ORDER BY ' . $this->order . ' ';
        }
        if (!empty($this->limit)) {
            $this->sql .= ' LIMIT ' . $this->limit . ' ';
        }
        $this->condition = $this->order = $this->limit = '';
        $this->fields = '*';
//         Log::write('SQL: '.$this->sql);
    }

    /**
     * 查询构造方法
     */
    public function select() {
        $this->_make_select_sql();
        $data = $this->getAll($this->sql);
        if (empty($data)) {
            $data = [];
        }

        return $data;
    }

    /**
     * 获取单条记录
     *
     * @return bool|mixed
     */
    public function find() {
        $this->_make_select_sql();
        $data = $this->getOne($this->sql);
        if (empty($data)) {
            $data = [];
        }

        return $data;
    }

    /**
     * 设置where条件
     *
     * @param $condition
     * @return null|static
     */
    public function where($condition) {
        if (is_array($condition) && ! empty($condition)) {
            $whereStr = '';
            foreach ($condition as $k => $item) {
                $k = addslashes($k);

                if (is_array($item)) {
                    if (count($item) === 3) {
                        $item[0] = addslashes($item[0]);
                        $item[1] = addslashes($item[1]);

                        if (is_array($item[2])) {
                            $item[2] = "'".implode("','", $item[2])."'";
                            $whereStr .= "`{$item[0]}` {$item[1]} ({$item[2]}) AND ";

                        } else {
                            $item[2] = addslashes($item[2]);
                            $whereStr .= "`{$item[0]}` {$item[1]} '{$item[2]}' AND ";
                        }

                    } elseif (count($item) === 2) {
                        $item[0] = addslashes($item[0]);
                        $item[1] = addslashes($item[1]);

                        $whereStr .= "`{$k}` {$item[0]} '{$item[1]}' AND ";
                    }
                } else {

                    $item = addslashes($item);
                    $whereStr .= "`{$k}` = '{$item}' AND ";
                }
            }

            $this->condition = rtrim($whereStr, ' AND ');

        } elseif (is_string($condition)) {
            $this->condition = $condition;
        }

        return self::$instance;
    }

    /**
     * 设置order条件
     *
     * @param $order
     * @return null|static
     */
    public function order($order) {
        $this->order = $order;
        return self::$instance;
    }

    /**
     * 设置limit条件
     *
     * @param $start
     * @param $pagesize
     * @return null|static
     */
    public function limit($start, $pagesize = 0) {
        if (empty($pagesize)) {
            $this->limit = $start;
        } else {
            $this->limit = intval($start) . ',' . intval($pagesize);
        }

        return self::$instance;
    }

    /**
     * 插入数据
     *
     * @param $data
     * @return bool|string
     */
    public function insert($data) {
        $sql = 'INSERT INTO `'. self::$table .'` SET ';
        foreach ($data as $key => $value) {
            $key = addslashes(trim($key));
            $value = addslashes(trim($value));
            $sql .= "`$key` = '$value', ";
        }
        $sql = trim($sql, ', ');
        if (!$this->query($sql)) {
            return false;
        }
        return self::$connection->lastInsertId();
    }

    /**
     * 更新数据
     *
     * @param $data
     * @return bool
     */
    public function update($data) {
        $sql = 'UPDATE `'. self::$table .'` SET ';
        foreach ($data as $key => $value) {
            $key = addslashes(trim($key));
            $value = addslashes(trim($value));

            $sql .= "`$key` = '$value', ";
        }

        $sql = trim($sql, ', ');
        if ($this->condition) {
            $sql .= ' WHERE ' . $this->condition;
        }

        return $this->query($sql);
    }

    /**
     * insertOrUpdate
     *
     * @param $where
     * @param $data
     * @return bool|string
     */
    public function insertOrUpdate($where, $data) {
        if ($this->where($where)->find()) {
            return $this->where($where)->update($data);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * 统计加和
     *
     * @param $field
     * @return bool
     */
    public function sum($field) {
        $fieldAlias = self::$table . '_' . $field;

        $this->fields = "SUM(`{$field}`) as {$fieldAlias}";

        $data = $this->find();
        if (empty($data)) {
            return false;
        }

        return $data[$fieldAlias];
    }

    /**
     * 统计
     *
     * @param string $field
     * @return bool
     */
    public function count($field = '*') {
        $this->fields = "COUNT({$field}) as total";

        $data = $this->find();
        if (empty($data)) {
            return false;
        }

        return $data['total'];
    }

    /**
     * 获取uuid
     *
     * @return bool|mixed
     */
    public function uuid() {
        $sql = 'select uuid() as uuid';
        $data = $this->getOne($sql);
        if (!$data) {
            // add_error_log('获取uuid失败');
            return false;
        }
        return str_replace('-', '', $data['uuid']);
    }

    /**
     * beginTrans
     *
     * @return bool
     */
    public static function beginTrans() {
        if (self::$connection === null)
            self::$connection = self::getConnection();

        return self::$connection->beginTransaction();
    }

    /**
     * rollBack
     *
     * @return bool
     */
    public static function rollBack() {
        if (self::$connection === null)
            self::$connection = self::getConnection();

        return self::$connection->rollBack();
    }

    /**
     * commit
     *
     * @return bool
     */
    public static function commit() {
        if (self::$connection === null)
            self::$connection = self::getConnection();

        return self::$connection->commit();
    }
}