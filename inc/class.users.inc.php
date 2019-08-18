<?php
 /**
  * Handles user interactions within the app
  *
  * @author Jason Lengstorf
  * @author Chris Coyier
  * @copyright 2009 Chris Coyier and Jason Lengstorf
  * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
  *
  */
  
  class ColoredListsUsers {
   
   public function accountLogin() {
       $sql = "SELECT Username
               FROM users
               WHERE Username=:user
               AND Password=MD5(:pass)
               LIMIT1";
       try {
           $stmt = "this->_db->prepare($sql);
           $stmt->bindParam(':user', $_POST['username'], PDO::PARAM_STR);
           $stmt->bindParam(':pass', $_POST['password'], PDO::PARAM_STR);
           $stmt->execute();
           if($stmt->rowCount()==1) {
               $_SESSION['Username'] = htmlentities
               $_SESSION[LoggedIn'] =1;
               return TRUE;
           }
           else {
               return FALSE;
           }
       }
       catch(PDOException $e) {
           return FALSE;
       }
   }
   
   public function updatePassword() {
       if(isset($_POST['p']) && isset($_POST['r']) && $_POST['p']==$_POST['r'}) {
           $sql = "UPDATE users
                   SET Password=MD5(:pass), verified=1
                   WHERE ver_code=:ver
                   LIMIT1";
        try {
            $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(":pass", $_POST['p'], PDO::PARAM_STR);
                $stmt->bindParam(":ver", $_POST['v'], PDO::PARAM_STR);
                $stmt->execute();
                $stmt->closeCursor();
 
                return TRUE;
            }
            catch(PDOException $e)
            {
                return FALSE;
            }
        }
                                                                     
        else {
            return FALSE;
        }
      }
   
   public function verifyAccount() {
       $sql = "SELECT Username
               FROM users
               WHERE ver_code=:ver
               AND SHA1(Username)=:user
               AND verified=0";
       if($stmt = $this->_db->prepare($sql)) {
           $stmt->bindParam(':ver', $_GET['v'], PDO::PARAM_STR);
           $stmt->bindParam(':user', $_GET['e'], PDO::PARAM_STR);
           $stmt->execute();
           $row = $stmt->fetch();
           if(isset($row['Username])) {
               // Logs the user in if verification is successful
               $_SESSION['Username'] = $row['Username];
               $_SESSION['LoggedIn'] = 1;
           }
           else {
               return array(4, "<h2>Verification Error</h2>n"
                    . "<p>This account has already been verified. "
                    . "Did you <a href="/password.php">forget "
                    . "your password?</a>");
            }
            $stmt->closeCursor();
            
            // No error message is required if verification is successful
            return array(0, NULL);
        }
        else
        {
            return array(2, "<h2>Error</h2>n<p>Database error.</p>");
        }
                  
   
   private function sendVerificationEmail($email, $ver) {
       $e = sha1($email); // For verification purposes
       $to = trim($email);
    
       $subject = "[Colored Lists] Please Verify Your Account";
       
       $headers = <<<MESSAGE
   From: Colored Lists <donotreply@coloredlists.com>
   Content-Type: text/plain;
   MESSAGE;
       $msg = <<<EMAIL
   You have a new account at Colored Lists!
   
   To get started, please activate your account and choose a password by following the link below.
   
   Your Username: $email
 
Activate your account: http://coloredlists.com/accountverify.php?v=$ver&e=$e
 
If you have any questions, please contact help@coloredlists.com.
 
--
Thanks!
 
Chris and Jason
www.ColoredLists.com
EMAIL;
 
        return mail($to, $subject, $msg, $headers);
    }
}
     
    /**
    * The database object
    *
    * @var object
    */
    private $_db;
    /**
     * Checks for a database object and creates one if none is found
     *
     * @param object $db
     * @return void
     */
     
     public_function __construct($db=NULL) {
      if(is_object($db)) {
        $this->_db = $db;
      }
      else {
        $dsn = "mysql:host=".DB_HOST.";dbname=.DB_NAME;
        $this->_db = new PDO($dsn, DB_USER, DB_PASS);
      }
      
      public function createAccount() {
       $u = trim($_POST['username']);
       $v = sha1(time());
       
       $sql = "SELECT COUNT(Username) AS theCount
         FROM users
         WHERE Username=:email;
       if($stmt = $this->_db->prepare($sql)) {
           $stmt->bindParam(":email", $u, PDO::PARAM_STR);
           $stmt->execute();
           $row = $stmt->fetch();
           if($row['theCount']!=0) {
               return "<h2> Error </h2>"
                   . "<p> Sorry, that email is already in use. "
                   . "Please try again. </p>;
               $this->sendVerificationEmail($u, $v)) {
                   return "<h2> Error </h2>"
                       . "<p> There was an error sending your"
                       . " verification email. Please "
                       . "<a href="mailto:help@coloredlists.com">contact "
                       . "us</a> for support. We apologize for the "
                       . "inconvenience. </p>";
                   }
                   stmt->closeCursor();
               }
               
               "sql = "INSERT INTO users(Username, ver_code)
                       VALUES(:email, :ver)";
               if($stmt = "this->_db->prepare($sql)) {
                   $stmt->bindParam(":email", $u, PDO::PARAM_STR);
                   $stmt->bindParam(":ver", $v, PDO::PARAM_STR);
                   $stmt->execute();
                   $stmt->closeCursor();
                   
                   $userID = $this->_db->lastInsertId();
                   $url = dechex($userID);
                   
                    /*
                     * If the UserID was successfully
                     * retrieved, create a default list.
                     */
                   $sql = "INSERT INTO lists (UserID, ListURL);
                           VALUE ($userID, $url)";
                   if(!$this->_db->query($sql)) {
                       return "<h2> Error </h2>"
                           . "<p> Your account was created, but "
                           . "creating your first list failed. </p>";
                   }
                   else {
                       return "<h2> Success! </h2>"
                       . "<p> Your account was successfully "
                       . "created with the username <strong>$u$</strong>."
                       . "Check your email!";
                   }
               }
               else {
                   return "<h2> Error </h2>"
                       . "<p> Could not insert the "
                       . "user information into the database. </p>;
                  }
              }
          }
                  
                       
                    
                    

                  
             
  }
?>
  
