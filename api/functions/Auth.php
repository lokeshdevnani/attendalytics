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

    public function isLogged(){   // is login Session active
        if(isset($_SESSION['login'])){
            return $_SESSION['login'];
        }
        return false;
    }

    public function isOK(){  // Checks if either session or token is validated and User is logged in.
       $login = $this->isLogged();
       if($login)
           return $login;
       if(isset($_REQUEST['token']) && $login = $this->isTokenValid($_REQUEST['token'])){
           $_SESSION['login'] = $login;
           return $login;
       }
       return false;
    }

    public function generateToken($datastring){
        $token = str_shuffle(md5(str_shuffle($datastring)));
        $q = $this->db->prepare("INSERT INTO tokens (datastring,token) VALUES(?,?)");
        if($q->execute(array($datastring,$token)))
            return $token;
        return false;
    }


    public function isTokenValid($token){
        $q = $this->db->prepare("SELECT datastring FROM tokens WHERE token = ? LIMIT 1");
        $q->execute(array($token));
        if($q->rowCount()){
            // get the datastring and set the session with the data after json_decoding it.
            $datastring = $q->fetchColumn(0);
            $login = json_decode($datastring,true);
            $_SESSION['login']= $login;
            return $login;
        }
        return false;
    }

    public function isTeachingSubject($teacherId,$classId,$subjectId){
        $q = $this->db->prepare("SELECT id FROM subjectteachers WHERE classId= ? AND subjectId = ? AND teacherId = ?");
        $q->execute(array($classId,$subjectId,$teacherId));
        if($q->rowCount()) return true;
        return false;
    }

    public function isTeaching($teacherId,$classId){
        $q = $this->db->prepare("SELECT id FROM subjectteachers WHERE classId= ? AND teacherId = ?");
        $q->execute(array($classId,$teacherId));
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
           return true;
        } else if($login['type'] == "HOD"){
            if($this->getBranch($classId)==$login['branch'])
                return true;
            return false;
        } else if($login['type'] == "teacher"){
            return $this->isTeachingSubject($login['id'],$classId,$subjectId);
        }
        return false;
    }

    public function isAllowedStudentwise($classId,$rollno){
        $login = $this->isLogged();
        if(empty($login)) return false;
        if($login['type']=="superuser"){
          return true;
        } else if($login['type'] == "HOD"){
            if($this->getBranch($classId)==$login['branch'])
                return true;
            return false;
        } else if($login['type'] == "teacher"){
            return $this->isTeaching($login['id'],$classId);
        } else if($login['type'] == "student"){
            return ($login["classId"]==$classId && $login["rollno"]==$rollno);
        }
        return false;
    }

    public function isAllowedTeacherwise($teacherId){
        $login = $this->isLogged();
        if(empty($login)) return false;
        if($login['type']=="superuser"){
            return true;
        } else if($login['type'] == "HOD"){
                return true;
        } else if($login['type'] == "teacher"){
            return ($login['id']==$teacherId);
        }
        return false;
    }
    public function isAllowedClasses($branch){
        $login = $this->isLogged();
        if(empty($login)) return false;
        if($login['type']=="superuser"){
            return true;
        } else if($login['type']=="HOD"){
            return ($login['branch'] == $branch);
        }
        return false;
    }
    public function isAllowedToUpload(){
        $login = $this->isLogged();
        if(empty($login)) return false;
        if($login['type']=="superuser"){
            return true;
        } else if($login['type'] == "HOD"){
                return true;
        } else if($login['type'] == "teacher"){
            return true;
        }
        return false;
    }

    public function isAllowedList(){
      $login = $this->isLogged();
      if(empty($login)) return false;
      if($login['type']=="superuser"){
          return true;
      } else if($login['type']=="HOD"){
          return true;
      }  else if($login['type'] == "teacher"){
          return true;
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
    }
}
