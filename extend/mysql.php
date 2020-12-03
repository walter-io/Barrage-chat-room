<?php

namespace extend;

class Mysql
{
    private $host;
    private $user;
    private $db;
    private $pass;
    private $port;
    private static $instance;

    public function __construct()
    {
        $conn = mysqli_connect($this->host, $this->user, $this->pass, $this->db, $this->port);
        $conn->set_charset('utf8');
        self::$instance = $conn;
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public function query($sql)
    {
        $result = mysqli_query(self::$instance, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

}