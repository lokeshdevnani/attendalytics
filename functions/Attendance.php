<?php
class Attendance {
    protected $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }
    public function upload($classId,$subjectId,$teacherId,$time,$absentees){
        try{
            $q1 = $this->db->prepare("INSERT INTO lectures (classId,subjectId,teacherId,time,uploadTime)
            VALUES(?,?,?,?,NOW()) ");
            $q1->execute(array($classId,$subjectId,$teacherId,$time));
            $lectureId = $this->db->lastInsertId();
            echo $lectureId;
            if(!$lectureId) return false;

            $sql = array();
            foreach($absentees as $absentee){
                $sql[] = "(". $lectureId . "," . $absentee . ")";
            }

            $sqlString = "INSERT INTO absentees (lectureId,rollno) VALUES ".implode(",",$sql);
            $q2 = $this->db->prepare($sqlString);
            $q2->execute();
            echo $q2->rowCount();
            
        }catch (Exception $e){
            echo $e->getMessage();
            return false;
        }

        /*
        $sql = array();
        foreach( $data as $row ) {
            $sql[] = '("'.mysql_real_escape_string($row['text']).'", '.$row['category_id'].')';
        }
        mysql_query('INSERT INTO table (text, category) VALUES '.implode(',', $sql));
        */
    }

    public function display(){
        echo "Hello attendance";
    }


}