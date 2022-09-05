<?php

class database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "bgl";
    private $result = array();
    private $mysqli = "";

    public function __construct(){
        $this->mysqli = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->mysqli->connect_error) {
            die("Connection failed: " . $this->mysqli->connect_error);
        }
    }

    public function create_db(){
        $sql = "CREATE DATABASE $this->dbname";
        $this->mysqli->query($sql);
    }

    public function create_table($sql){
        $this->mysqli->query($sql);
    }

    public function insert($table, $para=array()){
        $table_columns = implode(',', array_keys($para));
        $table_value = implode("','", $para);

        $sql = "INSERT INTO $table($table_columns) VALUES('$table_value')";

        $result = $this->mysqli->query($sql);
    }

    public function update($table, $para=array(), $id){
        $args = array();

        foreach ($para as $key => $value) {
            $args[] = "$key = '$value'"; 
        }

        $sql = "UPDATE  $table SET " . implode(',', $args);

        $sql .=" WHERE $id";

        $result = $this->mysqli->query($sql);
    }

    public function delete($table,$id){
        $sql = "DELETE FROM $table";
        $sql .=" WHERE $id ";
        $this->mysqli->query($sql);
    }

    public function select($table, $rows="*", $where = null, $order = null){
        $sql = "SELECT $rows FROM $table";
        if ($where != null) {
            $sql .=" WHERE $where";
        }

        if ($order != null) {
            $sql .=" ORDER BY $order";
        }

        return $result = $this->mysqli->query($sql);
    }

    public function select_custom($sql){
        return $result = $this->mysqli->query($sql);
    }

    public function drop_db(){
        $sql = "DROP DATABASE IF EXISTS $this->dbname";
        $result = $this->mysqli->query($sql);
    }

    public function __destruct(){
        $this->mysqli->close();
    }
}

?>