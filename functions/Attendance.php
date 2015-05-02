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
            return $q2->rowCount();
        }catch (Exception $e){
            echo $e->getMessage();
            return false;
        }

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

   public function getAllLecturesForClass($classId = 1,$subjectId = 3){
        echo "Getting lectures wait... <br>";
        $q = $this->db->prepare("SELECT id,teacherId,time FROM lectures WHERE classId = ? AND subjectId = ?");
        $q->execute(array($classId,$subjectId));
        $lectureList = $q->fetchAll(PDO::FETCH_OBJ);
        return $lectureList;
   }






}