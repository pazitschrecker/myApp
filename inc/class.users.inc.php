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
  
