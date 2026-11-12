<?php
/**
 * Core Database Wrapper using PDO
 * Supports secure query preparation, binding, and error handling.
 */

class Database
{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $port = DB_PORT;

    private $dbh;
    private $stmt;
    private $error;

    public function __construct()
    {
        $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->dbname . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // Database error fallback handling
        }
    }

    public function isConnected()
    {
        return $this->dbh !== null;
    }

    public function getError()
    {
        return $this->error;
    }

    public function query($sql)
    {
        if (!$this->dbh) return $this;
        $this->stmt = $this->dbh->prepare($sql);
        return $this;
    }

    public function bind($param, $value, $type = null)
    {
        if (!$this->stmt) return $this;

        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
        return $this;
    }

    public function execute()
    {
        if (!$this->stmt) return false;
        try {
            return $this->stmt->execute();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function resultSet()
    {
        $this->execute();
        return $this->stmt ? $this->stmt->fetchAll() : [];
    }

    public function single()
    {
        $this->execute();
        return $this->stmt ? $this->stmt->fetch() : null;
    }

    public function rowCount()
    {
        return $this->stmt ? $this->stmt->rowCount() : 0;
    }

    public function lastInsertId()
    {
        return $this->dbh ? $this->dbh->lastInsertId() : null;
    }
}
