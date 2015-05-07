<?php
class Attendance {
    protected $db;
    protected $rollStart,$rollEnd;
    protected $absenteeCount;

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
            return $q2->rowCount();
        }catch (Exception $e){
            echo $e->getMessage();
            return false;
        }

    }

    public function getRollRange($classId){
        $p = $this->db->prepare("SELECT rollStart,rollEnd FROM classes WHERE id=? ");
        $p->execute(array($classId));
        $rolls = $p->fetchObject();
        if(!$rolls) return false;
        $this->rollStart = $rolls->rollStart;
        $this->rollEnd = $rolls->rollEnd;
    }

    public function getRollStart(){
        return $this->rollStart;
    }

    public function getRollEnd(){
        return $this->rollEnd;
    }

    public function getByLectureId($lectureId = 29){
        $p = $this->db->prepare("SELECT rollStart,rollEnd FROM classes WHERE id IN (SELECT classId FROM lectures WHERE id=? )");
        $p->execute(array($lectureId));
        $rolls = $p->fetchObject();
        if(!$rolls) return false;
        $rollStart = $rolls->rollStart;
        $rollEnd = $rolls->rollEnd;

        $q = $this->db->prepare("SELECT rollno FROM absentees WHERE lectureId = ? ORDER BY rollno");
        $q->execute(array($lectureId));
        $arr = $q->fetchAll(PDO::FETCH_COLUMN,"rollno");
        echo "<pre>",print_r($arr),"</pre>";

        $k=0;
        $arr_length = count($arr);
        for($i=$rollStart;$i<=$rollEnd;$i++){
            if($arr_length>$k && $arr[$k]==$i){
                echo "<b>$i</b> ";
                $k++;
            }else{
                echo "$i ";
            }
            echo "<br />";
        }
    }

    public function getAbsenteeCount(){
        return $this->absenteeCount;
    }

    public function getByLectureId2($lectureId = 29){

        $q = $this->db->prepare("SELECT rollno FROM absentees WHERE lectureId = ? ORDER BY rollno");
        $q->execute(array($lectureId));
        $arr = $q->fetchAll(PDO::FETCH_COLUMN,"rollno");

        $k=0;
        $arr_length = count($arr);
        $this->absenteeCount = $arr_length;

        $res = array();
        for($i=$this->rollStart;$i<=$this->rollEnd;$i++){
            if($arr_length>$k && $arr[$k]==$i){
                $res[]='A';
                $k++;
            }else{
                $res[]='P';
            }
        }
        return $res;
    }

   public function getAllLecturesForClass($classId = 1,$subjectId = 3){
        $q = $this->db->prepare("SELECT id,teacherId,time FROM lectures WHERE classId = ? AND subjectId = ? ORDER BY time");
        $q->execute(array($classId,$subjectId));
        $lectureList = $q->fetchAll(PDO::FETCH_OBJ);
        return $lectureList;
   }

    public function getStudentNames($classId){
        $q = $this->db->prepare("SELECT name FROM students WHERE classId = ?");
        $q->execute(array($classId));
        $lectureList = $q->fetchAll(PDO::FETCH_COLUMN,"name");
        return $lectureList;
    }

    public function getSummary($classId,$subjectId){
        $summary = array();
        $q = $this->db->prepare("SELECT name FROM classes WHERE id = ?");
        $q->execute(array($classId));
        $summary['className'] = $q->fetch(PDO::FETCH_NUM,0)[0];

        $q = $this->db->prepare("SELECT name FROM subjects WHERE id = ?");
        $q->execute(array($subjectId));
        $summary['subjectName'] = $q->fetch(PDO::FETCH_NUM,0)[0];

        $summary['teacherName'] = "To be Coded !!";
        return $summary;
    }


}