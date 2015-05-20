<?php
class Auth{
    protected $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    public function passwordHash($str){
        return $str;
        return md5($str);
    }

    public function isLogged(){
        if(isset($_SESSION['login'])){
            return $_SESSION['login'];
        }
        return false;
    }
    public function isTeaching($teacherId,$classId,$subjectId){
        $q = $this->db->prepare("SELECT id FROM subjectteachers WHERE classId= ? AND subjectId = ? AND teacherId = ?");
        $q->execute(array($classId,$subjectId,$teacherId));
        if($q->rowCount()) return true;
        return false;
    }

    public function getBranch($classId){
        $q = $this->db->prepare("SELECT branch FROM classes WHERE id = ?");
        $q->execute(array($classId));
        $branch = $q->fetch(PDO::FETCH_OBJ);
        if($branch) return $branch->branch;
        return null;
    }


    public function isAllowed($classId,$subjectId){
        $login = $this->isLogged();
        if(empty($login)) return false;
        if($login['type']=="superuser"){

        } else if($login['type'] == "HOD"){
            if($this->getBranch($classId)==$login['branch'])
                return true;
            return false;
        } else if($login['type'] == "teacher"){
            return $this->isTeaching($login['id'],$classId,$subjectId);
        }
        return false;
    }

    public function loginHOD($username,$password){
       $q = $this->db->prepare("SELECT id,name,branch FROM hods WHERE username = ? and password = ? LIMIT 1");
       $q->execute(array($username,$password));
       if($q->rowCount()){
           $result = $q->fetch(PDO::FETCH_ASSOC);
           $login = array();
           $login['type'] = "HOD";
           $login['id'] = $result['id'];
           $login['name'] = $result['name'];
           $login['branch'] = $result['branch'];
           $_SESSION['login'] = $login;
           return $login;
       } else
           return false;
    }
    public function loginTeacher($username,$password){
        $q = $this->db->prepare("SELECT id,name FROM teachers WHERE username = ? and password = ? LIMIT 1");
        $q->execute(array($username,$password));
        if($q->rowCount()){
            $result = $q->fetch(PDO::FETCH_ASSOC);
            $login = array();
            $login['type'] = "teacher";
            $login['id'] = $result['id'];
            $login['name'] = $result['name'];
            $_SESSION['login'] = $login;
            return $login;
        } else
            return false;
    }
    public function loginStudent($username,$password){
        $q = $this->db->prepare("SELECT id,name,classId,rollno FROM students WHERE rturoll = ? and password = ? LIMIT 1");
        $q->execute(array($username,$password));
        if($q->rowCount()){
            $result = $q->fetch(PDO::FETCH_ASSOC);
            $login = array();
            $login['type'] = "student";
            $login['id'] = $result['id'];
            $login['name'] = $result['name'];
            $login['classId'] = $result['classId'];
            $login['rollno'] = $result['rollno'];
            $_SESSION['login'] = $login;
            return $login;
        } else
            return false;
    }
    public function logout(){
        $_SESSION['login'] = null;
        session_destroy();
    }

    public function login($username,$password,$type,$remember = false){
        $password = $this->passwordHash($password);
        if($type=='hod')return $this->loginHOD($username,$password);
        else if($type=='teacher')return $this->loginTeacher($username,$password);
        else if ($type=='student')return $this->loginStudent($username,$password);
        else return false;

        return;
            if($remember == true){
                $cookie_time = 60*60*24*7;
                setcookie('username',$username,time()+$cookie_time);
                setcookie('password',$password,time()+$cookie_time);
            }
        return false;
    }
}