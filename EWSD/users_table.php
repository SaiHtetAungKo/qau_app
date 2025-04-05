<?php
    include_once('connection.php');
    
    class UsersTable
    {
        private $db;
        public function __construct(Connect $connection)
        {
            $this->db =  $connection->getConnection();
        }
       
        public function checkEmailandPassword($email, $password)
        {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE user_email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_array(MYSQLI_ASSOC);
            
            if ($data && password_verify($password, $data['user_password']) ) {
                return $data;
            }
            else{
                return false;
            }    
        }

    
        public function checkUserOldPassword($userID, $oldPsw) 
        {
            $selectOldPsw = "SELECT user_password FROM users WHERE user_id = ?";
            $stmt = mysqli_prepare($this->db, $selectOldPsw);
            mysqli_stmt_bind_param($stmt, "i", $userID);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $dataOldPsw = mysqli_fetch_array($result, MYSQLI_ASSOC);
            mysqli_stmt_close($stmt);
        
            // check that whether old psw and new psw are same
            if ($dataOldPsw && password_verify($oldPsw, $dataOldPsw['user_password'])) {
                return true;
            } else {
                return false;
            }
        }

        
        public function checkUserNewPassword($newPsw, $confirmPsw)
        {
            if ($newPsw === $confirmPsw) {
                return true;
            } 
            else {
                return false;
            }
        }

        public function updateNewPassword($userID, $hashNewPassword)
        {
            $updatePsw = "UPDATE users SET user_password = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($this->db, $updatePsw);
            mysqli_stmt_bind_param($stmt, "si", $hashNewPassword, $userID);
            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $result;                         
        }
  
    }
?>



