<?php
/**
 * This file is part of the TelegramBot package.
 *
 * Mehrdad Khoddami aka MKH <khoddami.me@gmail.com>
 *
 * @license  https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace MehrdadKhoddami\PDODatabase;


use PDO;
use PDOException;
use \MehrdadKhoddami\PDODatabase\MysqlCredentialsNotProvidedException;
use \MehrdadKhoddami\PDODatabase\MysqlCanNotConnectException;
use \MehrdadKhoddami\PDODatabase\MysqlExternalConnectionNotProvidedException;


class DB
{
    /**
     * MySQL credentials
     *
     * @var array
     */
    static protected $mysql_credentials = [];

    /**
     * PDO object
     *
     * @var PDO
     */
    static protected $pdo;

    /**
     * Table prefix
     *
     * @var string
     */
    static protected $table_prefix;

    /**
     * Initialize
     *
     * @param array                         $credentials  Database connection details
     * @param string                        $table_prefix Table prefix
     * @param string                        $encoding     Database character encoding
     *
     * @return PDO PDO database object
     * @throws \MehrdadKhoddami\PDODatabase\MysqlCredentialsNotProvidedException
     * @throws \MehrdadKhoddami\PDODatabase\MysqlCanNotConnectException
     */
    public static function initialize(
        array $credentials,
        $table_prefix = null,
        $encoding = 'utf8mb4'
    ) {
        if (empty($credentials)) {
            throw new MysqlCredentialsNotProvidedException('MySQL credentials not provided!');
        }

        $dsn     = 'mysql:host=' . $credentials['host'] . ';dbname=' . $credentials['database'];
        $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $encoding];
        try {
            $pdo = new PDO($dsn, $credentials['user'], $credentials['password'], $options);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } catch (PDOException $e) {
            throw new MysqlCanNotConnectException($e->getMessage());
        }

        self::$pdo               = $pdo;
        self::$mysql_credentials = $credentials;
        self::$table_prefix      = $table_prefix;

        return self::$pdo;
    }

    /**
     * External Initialize
     *
     * Let you use the class with an external already existing Pdo Mysql connection.
     *
     * @param PDO                           $external_pdo_connection PDO database object
     * @param string                        $table_prefix            Table prefix
     *
     * @return PDO PDO database object
     * @throws \MehrdadKhoddami\PDODatabase\MysqlExternalConnectionNotProvidedException
     */
    public static function externalInitialize(
        $external_pdo_connection,
        $table_prefix = null
    ) {
        if ($external_pdo_connection === null) {
            throw new MysqlExternalConnectionNotProvidedException('MySQL external connection not provided!');
        }

        self::$pdo               = $external_pdo_connection;
        self::$mysql_credentials = [];
        self::$table_prefix      = $table_prefix;

        return self::$pdo;
    }

    /**
     * Check if database connection has been created
     *
     * @return bool
     */
    public static function isDbConnected()
    {
        return self::$pdo !== null;
    }

    /**
     * Get the PDO object of the connected database
     *
     * @return \PDO
     */
    public static function getPdo()
    {
        return self::$pdo;
    }

    /**
     * Convert from unix timestamp to timestamp
     *
     * @param int $time Unix timestamp (if null, current timestamp is used)
     *
     * @return string
     */
    protected static function getTimestamp($time = null)
    {
        if ($time === null) {
            $time = time();
        }

        return date('Y-m-d H:i:s', $time);
    }
}
