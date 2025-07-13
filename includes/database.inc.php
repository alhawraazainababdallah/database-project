<?php

class Database
{
    private $dbServer = "DESKTOP-D2ER5Q2";
    private $dbName = "VehicleRenting";
    private $conn;
    private $error;
    public function __construct() {
        $this->connect();
    }
    public function getLastError()
    {
        return $this->error;
    }
    public function clearLastError()
    {
        $this->error = "";
    }

    //Register Query
    public function query($sql) {
        try
        {
            $params = array();
            $options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
            $query = sqlsrv_query($this->conn, $sql, $params, $options);
            if($query == false)
            {
                echo $sql."<br/>";
                $this->error = sqlsrv_errors();
                print_r($this->error);
                // die;//($this->error);
            }
        }
        catch(Exception $e)
        {
            $this->error = sqlsrv_errors();
            die($this->error);
        }
        return $query;
    }

    public function fetch_assoc($query)
    {
        return sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);
    }
    private function connect()
    {
        $connInfo = array("Database"=>$this->dbName);
        $this->conn = sqlsrv_connect($this->dbServer, $connInfo);
        if($this->conn == false)
        {
            $this->error = sqlsrv_errors();
            die($this->error);
        }
    }
    public function num_rows($query)
    {
        return sqlsrv_num_rows($query);
    }
    public function freeQuery($query)
    {
        sqlsrv_free_stmt($query);
    }
    private function disconnect()
    {
        sqlsrv_close($this->conn);
        $this->conn = null;
    }
    public function __destruct()
    {
        $this->disconnect();
    }
}

$db = new Database();
