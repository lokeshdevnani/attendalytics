<?php
class Attendance {
    protected $db;

    public function __construct($db){
        $this->db = $db;
    }
    public function display(){
        echo "Hello attendance";
    }


}