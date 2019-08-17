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
  }
?>
  
