<?php

use Phalcon\Db as Db;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;


class ModelBase extends Model
{

    /**
     * @var FileLogger
     */
    protected static $logger;

    /**
     * 初始化日志
     * @param $file_logger  FileLogger
     */
    public static function initLogger($file_logger)
    {
        self::$logger = $file_logger;
    }

    /**
     * 普通日志
     * @param string $where
     * @param integer $line
     * @param mixed $msg
     *
     * @return void
     */
    public static function logInfo($where, $line, $msg)
    {
        self::$logger->log(Logger::INFO, "{$where}@{$line} {$msg}");
    }

    /**
     * 错误日志
     * @param string $where
     * @param integer $line
     * @param mixed $msg
     *
     * @return void
     */
    public static function logError($where, $line, $msg)
    {
        self::$logger->log(Logger::ERROR, "{$where}@{$line} {$msg}");
    }

    /**
     * 调试日志
     * @param string $where
     * @param integer $line
     * @param mixed $msg
     *
     * @return void
     */
    public static function logDebug($where, $line, $msg)
    {
        self::$logger->log(Logger::DEBUG, "{$where}@{$line} {$msg}");
    }

    /**
     * 调试日志
     * @param string $where
     * @param integer $line
     * @param mixed $msg
     *
     * @return void
     */
    public static function debug($where, $line, $msg)
    {
        self::logDebug($where, $line, $msg);
    }

    /**
     * 参看
     * http://php.net/manual/en/pdostatement.bindparam.php
     * */
    protected static $_instance;

    /**
     * 单一实例的简写
     * @return ModelBase
     */
    public static function getInst()
    {
        return self::getInstance();
    }

    public static function getInstance()
    {
        $today = date("Y-m-d");
        $log_file = APP_PATH . "/app/logs/db_{$today}.log";
        $logger = new FileLogger($log_file);

        if (null == self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 查询带返回
     *
     * @param string $sql
     * @param null|array $a_param
     * @return Resultset
     */
    public function dbQuery($sql, $a_param = null)
    {
        $me = self::getInstance();
        return new Resultset(null, $me, $me->getReadConnection()->query($sql, $a_param));
    }

    /**
     * 查询不带返回
     *
     * @param string $sql
     * @param null|array $a_param
     * @return void
     */

    public function dbExecute($sql, $a_param = null)
    {

        $me = self::getInstance();
        $me->getReadConnection()->execute($sql, $a_param);
    }

    /**
     * 查询带返回
     *
     * @param string $sql
     * @param null|array $a_param
     * @return array
     */
    public function dbQuery2($sql, $a_param = null)
    {
        $me = self::getInstance();
        return
            $me->getReadConnection()->fetchAll(
                $sql,
                Db::FETCH_ASSOC,
                $a_param
            );
    }


    /**
     * 信息
     *
     * @var array
     */
    protected $_info = null;


    /**
     * 填充数据
     *
     * @param array $a_data
     * @return  boolean
     */
    public function init($a_data)
    {

        $this->_info = $a_data;
        return true;
    }


    /**
     * 获取信息数组
     *
     * @return array
     */
    public function getInfo()
    {
        return $this->_info;
    }


    /**
     * 获取id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_info['id'];
    }


    /**
     * Helper method so we can re-try a write.
     *
     * @param string $name
     *   The config name.
     * @param string $data
     *   The config data, already dumped to a string.
     * @return bool
     * @throws \Exception
     */
    protected function doWrite($name, $data)
    {
        $type = $this->connection->getType();
        switch ($type) {
            case 'mysql':
                $sql = 'INSERT INTO config ( collection, name, data ) '
                    . ' VALUES (:collection, :name, :data) '
                    . ' ON DUPLICATE KEY UPDATE '
                    . ' data = VALUES(data);';
                break;
            case 'pgsql':
                $sql = 'INSERT INTO config (collection, name, data) '
                    . 'VALUES (:collection, :name, :data) '
                    . 'ON CONFLICT (collection, name) '
                    . 'DO UPDATE SET data = EXCLUDED.data;';
                break;
            case 'sqlite':
                $sql = 'INSERT OR REPLACE INTO config (collection, name, data) '
                    . 'VALUES (:collection, :name, :data);';
                break;
            default:
                throw new \Exception('Only support Mysql, Postgres and SQLite');
                break;
        }

        return (bool)$this->connection->execute(
            $sql,
            [
                'collection' => $this->collection,
                'name' => $name,
                'data' => $this->encode($data)
            ],
            [
                Db\Column::BIND_PARAM_STR,
                Db\Column::BIND_PARAM_STR,
                Db\Column::BIND_PARAM_BLOB
            ]
        );
    }


}