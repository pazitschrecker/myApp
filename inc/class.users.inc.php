<?php

/**
* Handles list interactions within the app
*
* PHP version 5
*
* @author Jason Lengstorf
* @author Chris Coyier
* @copyright 2009 Chris Coyier and Jason Lengstorf
* @license   http://www.opensource.org/licenses/mit-license.html  MIT License
*
*/
class ColoredListsItems
{

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
  public function __construct($db=NULL) {
    if(is_object($db)) {
      $this->_db = $db;
    }
    else {
      $dsn = "mysql:host=",DB_HOST.";dbname=.DB_NAME;
      $this->_db = new PDO($dsn, DB_USER, DB_PASS);
    }
  }
}
?>

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
     * Adds a list item to the database
     *
     * @return mixed    ID of the new item on success, error message on failure
     */

     public function addListItem() {
       $list = $_POST['list'];
       $text = strip_tags(urldecode(trim($_POST[text])), WHITELIST);
       $pos = $_POST['pos'];

       $sql = "INSERT INTO list_items
       (ListID, ListText, ListItemPosition, ListItemColor)
       VALUES(:list, :text, :pos, 1)";

       try {
         $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':list', $list, PDO::PARAM_INT);
            $stmt->bindParam(':text', $text, PDO::PARAM_STR);
            $stmt->bindParam(':pos', $pos, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            return $this->_db->lastInsertId();
        }
        catch(PDOException $e) {
          return $e->getMessage();
        }
     }


    /**
     * Generates HTML markup for each list item
     *
     * @param array $row    an array of the current item's attributes
     * @param int $order    the position of the current list item
     * @return string       the formatted HTML string
     */
    private function formatListItems($row, $order) {
      $c = $this->getColorClass($row['ListItemColor']);
      if($row['ListItemDone']==1) {
        $d = 'img class="crossout" src="/assets/images/crossout.png"'
        . 'style="width: 100%; display: block;"/>';
      }
      else {
        $d = NULL;
      }
      // If not logged in, manually append the <span> tag to each items
      if(!isset($_SESSION['LoggedIn'])||$_SESSION['LoggedIn']!=1)]) {
        $ss = "<span> ";
        $se = "</span>";
      }
      else {
        $ss = NULL;
        $se = NULL;
      }

      return "tttt<li id="$row[ListItemID]" rel="$order" "
            . "class="$c" color="$row[ListItemColor]">$ss"
            . htmlentities(strip_tags($row['ListText'])).$d
            . "$se</li>n";
    }

    /**
     * Returns the CSS class that determines color for the list item
     *
     * @param int $color    the color code of an item
     * @return string       the corresponding CSS class for the color code
     */
    private function getColorClass($color)
    {
        switch($color)
        {
            case 1:
                return 'colorBlue';
            case 2:
                return 'colorYellow';
            case 3:
                return 'colorRed';
            default:
                return 'colorGreen';
        }
    }
}



  /**
  * Outputs all list items corresponding to a particular list ID
  *
  * @return void
  */

  public function loadListItemsByListId() {
    $sql = "SELECT ListText, ListItemID, ListItemColor, ListItemDone"
    FROM list_items
    WHERE ListID=(
      SELECT ListID
      FROM lists
      WHERE ListURL=:list
      )
      ORDER BY ListItemPosition";
      if($stmt = $this->_db->prepare($sql)) {
        $stmt->bindParam(':list', $_GET['list'], PDO::PARAM_STR);
        $stmt->execute();
        $order = 1;
        while($row = $stmt->fetch()) {
          echo $this->formatListItems($row, $order);
          $order;
        }
        $stmt->closeCursor();
      }
      else {
        echo "<li> Something went wrong. ", $db->error, "</li>";
      }
    }
  }

  /**
  * Loads all list items associated with a user ID
  *
  * This function both outputs <li> tags with list items and returns an
  * array with the list ID, list URL, and the order number for a new item.
  *
  * @return array    an array containing list ID, list URL, and next order
  */

  public function loadListItemsByUser() {
    $sql = "SELECT
    list_items.ListID, ListText, ListItemID, ListItemColor, ListItemDone, ListURL
    FROM list_items
    LEFT JOIN lists
    USING (ListID)
    WHERE list_items.ListID=(
      SELECT lists.ListID
      FROM users
      WHERE users.Username=:user
    )
  )
  ORDER BY ListItemPosition";
  if($stmt = $this->_db->prepare($sql)) {
    $stmt->bindParam(':user', $_SESSION['Username'], PDO::PARAM_STR);
    $stmt->execute();
    $order = 0;
    while($row = $stmt->fetch()) {
      $LID = $row['ListID'];
      $URL = $row['ListURL'];
      echo $this->formatListItems($row,   $order);
    }
    $stmt->closeCursor();

    // If there aren't any list items saved, no list ID is returned
    if(!isset($LID))
    {
      $sql = "SELECT ListID, ListURL
      FROM lists
      WHERE UserID = (
        SELECT UserID
        FROM users
        WHERE Username=:user
      )";
      if($stmt = $this->_db->prepare($sql))
      {
        $stmt->bindParam(':user', $_SESSION['Username'], PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        $LID = $row['ListID'];
        $URL = $row['ListURL'];
        $stmt->closeCursor();
      }
    }
  }
  else
  {
    echo "tttt<li> Something went wrong. ", $db->errorInfo, "</li>n";
  }

  return array($LID, $URL, $order);
}
}


/**
* Sends a link to a user that lets them reset their password
*
* @param string $email  the user's emaol address
* @param string $ver    the user's verification code
* @return boolean       TRUE on success and FALSE on failure
*/

private function sendResetEmail($email, $ver) {
  $e = sha1($email); // For verification purposes
  $to = trim($email);

  $subject = "[Colored Lists] Request to Reset Your Password";

  $headers = <<<MESSAGE
  From: Colored Lists <donotreply@coloredlists.com>
  Content-Type: text/plain;
  MESSAGE;

  $msg = <<<EMAIL
  We just heard you forgot your password! Bummer! To get going again,
  head over to the link below and choose a new password.

  Follow this link to reset your password:
  http://coloredlists.com/resetpassword.php?v=$ver&e=$e

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
* Resets a user's status to unverified and sends them an email
*
* @return mixed TRUE on success and a message on failure
*/

public function resetPassword() {
  $sql = "UPDATE users
  SET verified=0
  WHERE Username=:user
  LIMIT 1";
  try {
    $stmt = $this->_db->prepare($sql);
    $stmt->bindParam(":user", $_POST['username'], PDO::PARAM_STR);
    $stmt->execute();
    $stmt->closeCursor();
  }
  catch (PDOException $e) {
    return $e->getMessage();
  }

  // SEnd the reset email
  if(!$this->sendResetEmail($_POST['username'], $v)) {
    return "Sending the email failed!";
  }
  return TRUE;
}
}

public function deleteAccount() {
  if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn]==1) {
    // Delete list items
    $sql = "DELETE FROM list_items
    WHERE ListID=(
      SELECT ListID
      FROM lists
      WHERE UserID=:user
      LIMIT 1
      )";
      try {
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":user", $_POST['user-id'], PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
      }
      catch(PDOException $e)
      {
        die($e->getMessage());
      }

      // Delete the users lists(s)
      $sql = "DELETE FROM lists
      WHERE UserID=:user";
      try {
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":user", $_POST['user-id'], PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
      }
      catch(PDOException $e)
      {
        die($e->getMessage());
      }

      // Delete the user
      $sql = "DELETE FROM users
      WHERE UserID=:user
      AND Username=:email";
      try
      {
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":user", $_POST['user-id'], PDO::PARAM_INT);
        $stmt->bindParam(":email", $_SESSION['Username'], PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
      }
      catch(PDOException $e)
      {
        die($e->getMessage());
      }

      // Destroy the user's session and send to a confirmation page
      unset($_SESSION['LoggedIn'], $_SESSION['Username']);
      header("Location: /gone.php");
      exit;
    }
    else
    {
      header("Location: /account.php?delete=failed");
      exit;
    }
  }
}

public function updatePassword() {
  if(isset($_POST['p']) && isset($_POST['r']) {
    $sql = "UPDATE users
    SET Password=MD5(:pass), verified=1
    WHERE ver_code=:ver
    LIMIT 1";
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
}

public function retrieveAcccountInfo() {
  $sql = "SELECT UserID, ver_code
  FROM users
  WHERE Username=:user";
  try {
    $stmt = $this->_db->prepare($sql);
    $stmt->bindParam(':user', $_SESSION['Username'], PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch();
    $stmt->closeCursor();
    return array($row['UserID'], $row['ver_code']);
  }
  catch(PDOException $e)
  {
    return FALSE;
  }
}
}

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
