<?php
class Attendance {
    protected $db;
    protected $rollStart,$rollEnd;
    protected $absenteeCount;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    public function logInsertRequest($json){
        echo $json."<br>";
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
        return $rolls;
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

        if($summary['className'] && $summary['subjectName']);else return null;
        $summary['teacherName'] = "To be Coded !!";
        //return $summary;
        $query = $this->db->prepare("SELECT rollStart,rollEnd,s.name as subjectName,t.name as teacherName, c.name as className
          FROM subjectteachers st
          JOIN (SELECT * FROM classes WHERE id = ?) c ON st.classId = c.id
          JOIN (SELECT * FROM subjects WHERE id = ?) s ON st.subjectId = s.id
          JOIN teachers t ON st.teacherId = t.id
          ");
        $query->execute(array($classId,$subjectId));
        if($query->rowCount()){
            $sum = $query->fetch(PDO::FETCH_ASSOC);
        }

        return $sum;
        return $summary;
    }

    public function getLecturesCounts($classId){
        $q = $this->db->prepare("SELECT teacherId,lcount,t.name as teacherName FROM subjectteachers st
            LEFT JOIN (SELECT COUNT(*) as lcount,subjectId FROM lectures
			WHERE classId = ? GROUP BY subjectId ) l ON l.subjectId = st.subjectId
        JOIN teachers t ON t.id = teacherId
        WHERE classId = ? ORDER BY st.subjectId");
        $q->execute(array($classId,$classId));
        $arr = $q->fetchAll(PDO::FETCH_ASSOC);
        return $arr;
    }
    public function getAllClasses($sem){
        $q = $this->db->prepare("SELECT id as classId,name FROM classes WHERE sem = ?");
        $q->execute(array($sem));
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubjectNames($classId){
        $q = $this->db->prepare("SELECT id,name FROM subjects WHERE id IN (SELECT subjectId FROM subjectteachers WHERE classId=?)");
        $q->execute(array($classId));
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function studentReport($rollno,$classId){
        $q = $this->db->prepare("SELECT L.id as lectureId,L.subjectId as subjectId,TIME_FORMAT(L.time,'%h:%i %p') as time,
        DATE(L.time) as date,(CASE WHEN A.lectureId IS NULL THEN 1 ELSE 0 END) as status
        FROM lectures L
        LEFT JOIN (SELECT lectureId FROM absentees WHERE rollno = ?) A ON L.id = A.lectureId
        WHERE classId = ? ORDER BY 'date' DESC");
        $q->execute(array($rollno,$classId));
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function teacherReport($teacherId){
        $q = $this->db->prepare('SELECT L.id,L.classId,L.subjectId,DATE_FORMAT(time,"%Y-%m-%d %h:%i %p") as time,L.teacherId as takenBy,
         st.teacherId as takenOf,t.name as teacherName,c.name as className, s.name as subjectName,
         DAYOFWEEK(time) as day
         FROM lectures L RIGHT JOIN subjectteachers st ON st.classId = L.classId AND st.subjectId = L.subjectId
         LEFT JOIN teachers t ON (t.id = L.teacherId OR t.id = st.teacherId) AND t.id != ?
         LEFT JOIN classes c ON c.id = L.classId
         LEFT JOIN subjects s ON s.id = L.subjectId
         WHERE st.teacherId = ? OR L.teacherId = ?
         ORDER BY time DESC');
        $q->execute(array($teacherId,$teacherId,$teacherId));
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTeacherInfo($teacherId){
        $q = $this->db->prepare("SELECT id,name,remarks FROM teachers WHERE id =?");
        $q->execute(array($teacherId));
        return $q->fetch(PDO::FETCH_ASSOC);
    }
    public function getStudentInfo($classId,$rollno){
        $q = $this->db->prepare("SELECT rollno,s.name as name,classId,c.name as className,remarks FROM students s
         JOIN (SELECT id,name FROM classes WHERE id = ?) c ON s.classId = c.id
         WHERE rollno =?");
        $q->execute(array($classId,$rollno));
        return $q->fetch(PDO::FETCH_ASSOC);
    }

    public function getTeacherClasses($teacherId){
        $q = $this->db->prepare("SELECT classId,subjectId,c.name as className,s.name as subjectName FROM subjectteachers st
        LEFT JOIN classes c ON c.id = st.classId
        LEFT JOIN subjects s ON s.id = st.subjectId
        WHERE teacherId = ?
        ");
        $q->execute(array($teacherId));
        return $q->fetchAll(PDO::FETCH_ASSOC);

    }

}