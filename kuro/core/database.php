<?php
defined('BASE_PATH') or exit('No direct script access allowed');

class Database
{
    private $pdo;
    protected mixed $statement;
    private string $dbHost = "localhost";
    private string $dbUser = "root";
    private string $dbPass = "";
    private string $dbName = "kuro";

    protected function query($sql)
    {
        $this->statement = $this->connect()->query($sql);
    }

    protected function connect()
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        try {
            $dsn = 'mysql:host='.$this->dbHost.';dbname='.$this->dbName;
            $this->pdo = new PDO($dsn, $this->dbUser, $this->dbPass, [PDO::MYSQL_ATTR_INIT_COMMAND =>"SET NAMES utf8;SET time_zone = 'Europe/Copenhagen'"]);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            return $this->pdo;
        } catch (PDOException $e) {
            print ("Error!: ".$e->getMessage()."<br/>");
            die();
        } finally {
            //$this->pdo = NULL;
        }
    }

    protected function prepare($sql)
    {
        $this->statement = $this->connect()->prepare($sql);
    }

    protected function fetch(int $mode = PDO::FETCH_DEFAULT)
    {
        try {
            return $this->statement->fetch($mode);
        } catch (Throwable $error) {
            print_r("Error: " . $error->getMessage());
        } finally {
            $this->pdo = NULL;
        }
    }

    protected function fetchAll()
    {
        try {
            return $this->statement->fetchAll();
        } catch (Throwable $error) {
            print_r("Error: " . $error->getMessage());
        } finally {
            $this->pdo = NULL;
        }
    }

    protected function close()
    {
        $this->pdo = NULL;
    }

    protected function commit()
    {
        $this->connect()->commit();
        $this->pdo = NULL;
    }

    protected function rollBack()
    {
        $this->connect()->rollBack();
        $this->pdo = NULL;
    }
}