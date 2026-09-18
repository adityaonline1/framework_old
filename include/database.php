<?php
/**
 * Database.php
 * 
 * The Database class is meant to simplify the task of accessing
 * information from the website's database.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: June 15, 2011 by Ivan Novak
 */
include("constants.php");



class MySQLDB
{
   var $connection; 
   var $connection1;            //The MySQL database connection
   var $num_active_users;   //Number of active users viewing site
   var $num_active_guests;  //Number of active guests viewing site
   var $num_members;        //Number of signed-up users
   /* Note: call getNumMembers() to access $num_members! */

   /* Class constructor */
   function __construct(){
      /* Make connection to database */



        try 
        {
            $this->connection = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_NAME,DB_USER, DB_PASS);

            // set the PDO error mode to exception
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            

        }
        catch(PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
        }

      
     // mysqli_select_db(DB_NAME, $this->connection) or die(mysqli_error());
      
      /**
       * Only query database to find out number of members
       * when getNumMembers() is called for the first time,
       * until then, default value set.
       */
      $this->num_members = -1;
      
      if(TRACK_VISITORS){
         /* Calculate number of users at site */
       //  $this->calcNumActiveUsers();
      
         /* Calculate number of guests at site */
       //  $this->calcNumActiveGuests();
      }
   }

   /**
    * confirmUserPass - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given password is the same password in the database
    * for that user. If the user doesn't exist or if the
    * passwords don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserPass($username, $password){
      /* Add slashes if necessary (for query) */
      
	   
	   $username = addslashes($username);

      /* Verify that user is in database */
      $q = $this->connection->prepare("SELECT password FROM ".TBL_USERS." where username = :username");
       $result = $q->execute(array('username' => $username));
     
      if(!$result || ($q->rowCount()) < 1){
         return 1; //Indicates username failure
      }

      /* Retrieve password from result, strip slashes */
     $dbarray = $q->fetch( PDO::FETCH_ASSOC );
      $dbarray['password'] = stripslashes($dbarray['password']);
      $password = stripslashes($password);

      /* Validate that password is correct */
      if(md5($password) == $dbarray['password']){  
         return 0; //Success! Username and password confirmed
      }
      else{
         return 2; //Indicates password failure
      }
   }
   
   /**
    * confirmUserID - Checks whether or not the given
    * username is in the database, if so it checks if the
    * given userid is the same userid in the database
    * for that user. If the user doesn't exist or if the
    * userids don't match up, it returns an error code
    * (1 or 2). On success it returns 0.
    */
   function confirmUserID($username, $userid){
      /* Add slashes if necessary (for query) */
      //if(!get_magic_quotes_gpc()) {
	      $username = addslashes($username);
      //}

// check if user id is corrct ot not


      /* Verify that user is in database */
      $q = $this->connection->prepare("SELECT userid FROM ".TBL_USERS." WHERE username=:username");
       $result = $q->execute(array('username' => $username));
       
       
       
      if( !$result || $q->rowCount() < 1){
         return 1; //Indicates username failure
      }
      /* Retrieve userid from result, strip slashes */
      $dbarray = $q->fetch( PDO::FETCH_ASSOC );
      $dbarray['userid'] = stripslashes($dbarray['userid']);
      $userid = stripslashes($userid);


      /* Validate that userid is correct */
      if($userid == $dbarray['userid']){
         return 0; //Success! Username and userid confirmed
      }
      else{
         return 2; //Indicates userid invalid
      }
   }
   
   /**
    * usernameTaken - Returns true if the username has
    * been taken by another user, false otherwise.
    */
   function usernameTaken($username){
      if(!get_magic_quotes_gpc()){
         $username = addslashes($username);
      }
      $q = $this->connection->prepare("SELECT username FROM ".TBL_USERS." WHERE username = :username");
      $result = $q->execute(array('username' => $username));
      return ($q->rowCount() > 0);
   }
   
   
   /**
    * emailTaken - Returns true if the email has
    * been taken by another user, false otherwise.
    */
    function emailTaken($email){
       if(!get_magic_quotes_gpc()){
          $email = addslashes($email);
       }
        $q = $this->connection->prepare("SELECT email FROM ".TBL_USERS." WHERE email = :email");
        $result = $q->execute(array(':email' => $email));
       
       return ($q->rowCount() > 0);
    }
    
   /**
    * usernameBanned - Returns true if the username has
    * been banned by the administrator.
    */
   function usernameBanned($username){
      if(!get_magic_quotes_gpc()){
         $username = addslashes($username);
      }
    
       $q = $this->connection->prepare("SELECT username FROM ".TBL_BANNED_USERS." WHERE username = :username");
      $result = $q->execute(array('username' => $username));
      return ($q->rowCount() > 0);
   }
   
   /**
    * addNewUser - Inserts the given (username, password, email)
    * info into the database. Appropriate user level is set.
    * Returns true on success, false otherwise.
    */
   function addNewUser($username, $password, $email, $userid, $name){
      $time = time();
      /* If admin sign up, give admin user level */
      if(strcasecmp($username, ADMIN_NAME) == 0){
         $ulevel = ADMIN_LEVEL;
      }else{
         $ulevel = USER_LEVEL;
      }
       $q = $this->connection->prepare("INSERT INTO ".TBL_USERS." VALUES (:username, :password, :userid, :ulevel, :email, :time, :a, :name, :b, :c)");
       return $q->execute([
           'username' => $username,
            'password'=>$password,
            'userid'=>$userid,
            'ulevel'=>$ulevel,
            'email'=>$email,
            'time'=>$time,
            'a'=>'0',
            'name'=>$name,
            'b'=>'0',
            'c'=>'0',
         ]);
   }
   
   /**
    * updateUserField - Updates a field, specified by the field
    * parameter, in the user's row of the database.
    */
   function updateUserField($username, $field, $value){
      $q = $this->connection->prepare("UPDATE ".TBL_USERS." SET $field=:value WHERE username=:username");
       return $q->execute([
           'value' => $value,
            'username'=>$username,
         ]);
   }
   
   /**
    * getUserInfo - Returns the result array from a mysql
    * query asking for all information stored regarding
    * the given username. If query fails, NULL is returned.
    */
   function getUserInfo($username){
      $q = $this->connection->prepare("SELECT * FROM ".TBL_USERS." WHERE username=:username");
       $result=$q->execute([
            'username'=>$username,
         ]);
      //$result = $this->query($q);
      /* Error occurred, return given name by default */
      if(!$result || ($q->rowCount()) < 1){
         return NULL;
      }
      /* Return result array */
      $dbarray= $q->fetch( PDO::FETCH_ASSOC );
      return $dbarray;
   }
   
   function getUserInfoFromHash($hash){
   		$q = $this->connection->prepare("SELECT * FROM ".TBL_USERS." WHERE hash=:hash");
       $result = $q->execute([
            'hash'=>$hash,
         ]);
      //$result = $this->query($q);
      /* Error occurred, return given name by default */
      if(!$result || ($q->rowCount()) < 1){
         return NULL;
      }
      /* Return result array */
      $dbarray= $q->fetch( PDO::FETCH_ASSOC );
      return $dbarray;
   }
   
   /**
    * getNumMembers - Returns the number of signed-up users
    * of the website, banned members not included. The first
    * time the function is called on page load, the database
    * is queried, on subsequent calls, the stored result
    * is returned. This is to improve efficiency, effectively
    * not querying the database when no call is made.
    */
   function getNumMembers(){
      if($this->num_members < 0){
         $q = "SELECT * FROM ".TBL_USERS;
         $result = $this->query($q);
         $this->num_members = $result->rowCount();
      }
      return $this->num_members;
   }
   
   /**
    * calcNumActiveUsers - Finds out how many active users
    * are viewing site and sets class variable accordingly.
    */
   function calcNumActiveUsers(){
      /* Calculate number of users at site 
      $q = "SELECT * FROM ".TBL_ACTIVE_USERS;
      $result = $this->query($q);
      $this->num_active_users = $result->rowCount();*/
       $this->num_active_users = 1000;
   }
   
   /**
    * calcNumActiveGuests - Finds out how many active guests
    * are viewing site and sets class variable accordingly.
    */
   function calcNumActiveGuests(){
      /* Calculate number of guests at site 
      $q = "SELECT * FROM ".TBL_ACTIVE_GUESTS;
      $result = $this->query($q);
       $this->num_active_guests = $result->rowCount();*/
       $this->num_active_guests = 100;
   }
   
   /**
    * addActiveUser - Updates username's last active timestamp
    * in the database, and also adds him to the table of
    * active users, or updates timestamp if already there.
    */
   function addActiveUser($username, $time){
      $q = $this->connection->prepare("UPDATE ".TBL_USERS." SET timestamp = :timestamp WHERE username = :username");
    $result = $q->execute(array('timestamp'=>$time,'username' => $username));
    
    $del = $this->connection->prepare("delete from ".TBL_ACTIVE_USERS." where username=:username");
    $del->execute(array(':username'=>$username));
      
      if(!TRACK_VISITORS) return;
      $q = $this->connection->prepare("INSERT INTO ".TBL_ACTIVE_USERS." VALUES (:username, :time)");
       $result = $q->execute(array('username'=>$username,'time' => $time));
       
      $this->calcNumActiveUsers();
   }
   
   /* addActiveGuest - Adds guest to active guests table */
   function addActiveGuest($ip, $time){
      if(!TRACK_VISITORS) return;
      $q = $this->connection->prepare("REPLACE INTO ".TBL_ACTIVE_GUESTS." VALUES (:ip, :time)");
      $result = $q->execute(array('ip'=>$ip,'time' => $time));
      $this->calcNumActiveGuests();
   }
   
   /* These functions are self explanatory, no need for comments */
   
   /* removeActiveUser */
   function removeActiveUser($username){
      if(!TRACK_VISITORS) return;
      $q = $this->connection->prepare("DELETE FROM ".TBL_ACTIVE_USERS." WHERE username = :username");
      $result = $q->execute(array('username'=> $username));
      $this->calcNumActiveUsers();
   }
   
   /* removeActiveGuest */
   function removeActiveGuest($ip){
      if(!TRACK_VISITORS) return;
      $q = $this->connection->prepare("DELETE FROM ".TBL_ACTIVE_GUESTS." WHERE ip = :ip");
       $result = $q->execute(array('ip'=> $ip));
      $this->calcNumActiveGuests();
   }
   
   /* removeInactiveUsers */
   function removeInactiveUsers(){
      if(!TRACK_VISITORS) return;
      $timeout = time()-USER_TIMEOUT*60;
      $q = $this->connection->prepare("DELETE FROM ".TBL_ACTIVE_USERS." WHERE timestamp < :timeout");
	  $result = $q->execute(array('timeout'=> $timeout));
      $this->calcNumActiveUsers();
   }

   /* removeInactiveGuests */
   function removeInactiveGuests(){
      if(!TRACK_VISITORS) return;
      $timeout = time()-GUEST_TIMEOUT*60;
		$q = $this->connection->prepare("DELETE FROM ".TBL_ACTIVE_GUESTS." WHERE timestamp < :timeout");
		$result = $q->execute(array('timeout'=> $timeout));
      $this->calcNumActiveGuests();
   }
    
    function insert($table,$values)
    {
        $columns=array();$columns_bindings = array();$exec=array();

        $col_q=$this->query("DESCRIBE ".$table);
        while($row=$col_q->fetch(PDO::FETCH_ASSOC))
        {
            $columns[]=$row['Field'];
        }
        
        $ins_query = 'INSERT INTO `' . $table. '` ';
        
        for($i=0;$i<count($columns);$i++) 
        {
            $columns_bindings[$i] = ':'.$columns[$i];
            $exec[$columns[$i]]=$values[$i];
        }

        $ins_query = $ins_query . '(' . implode(', ', $columns) . ') VALUES (' . implode(',', $columns_bindings) . ')';


        $stmt = $this->connection->prepare($ins_query);
        //$result = $stmt->execute($exec);

        if (!$stmt->execute($exec)) 
        {
            return $stmt->errorInfo();
        } 
        else 
        {
            return true;
        }
        
    }
    
    function update($table,$update_fileds,$unique)
    {
        $columns_bindings = array();$exec=array();
        
        $up_query = 'UPDATE `' . $table. '` ';
        
        $cols=array_keys($update_fileds);
        for($i=0;$i<count($update_fileds);$i++) 
        {
            $columns_bindings[$i] = $cols[$i].'=:'.$cols[$i];
            $exec[$cols[$i]]=$update_fileds[$cols[$i]];
        }
        

        $up_query .= " SET " . implode(', ', $columns_bindings) . " WHERE ";
        $up_query .=$unique[0]."=:".$unique[0];
        $exec[$unique[0]]=$unique[1];
        
        $stmt = $this->connection->prepare($up_query);
        
        if (!$stmt->execute($exec)) 
        {
            return false;
        } 
        else 
        {
            return true;
        }
    }
    
    function select($table,$select_fileds,$base_con,$conditions,$order)
    {
        $names=array();$columns_bindings = array();$exec=array();$sel_query='';
        
        $sel_query = 'SELECT '.$select_fileds.' FROM '.$table;
        
        if(count($conditions)>0 || strlen($base_con)>0)
        {
            $sel_query .= " WHERE ".$base_con;
			if(count($conditions)>0)
			{
				 $sel_query .=" AND "; 
				$names=array_keys($conditions);
				for($i=0;$i<count($conditions);$i++) 
				{
					//$columns_bindings[$i] = $names[$i].'=:'.$names[$i];
					//$exec[$names[$i]]=$conditions[$names[$i]];

					$columns_bindings[$i] = $names[$i].'=:param'.$i;
					$exec['param'.$i]=$conditions[$names[$i]];
				}
			}
    
            $sel_query .= implode(' AND ', $columns_bindings);
        }

		$sel_query .=$order; 
		//echo $sel_query."<br>";print_r($exec);
        $stmt = $this->connection->prepare($sel_query);

        if (!$stmt->execute($exec)) 
        {
            return false;
        } 
        else 
        {
            return $stmt;
        }
    }
    
    function delete($table,$key,$value)
    {
        $del_q="DELETE FROM ".$table;
        
        if(strlen($key)>0 && strlen($value)>0)
        {
            $del_q.=" WHERE ".$key."=:".$key;
            $exec[$key]=$value;
        }
        
        $stmt = $this->connection->prepare($del_q);
        
        if (!$stmt->execute($exec)) 
        {
            return false;
        } 
        else 
        {
            return true;
        }
    }
   
   /**
    * query - Performs the given query on the database and
    * returns the result, which may be false, true or a
    * resource identifier.
    */
    function query($query)
    {


        $operation = substr($query,0,6);


        //Test for basic injection
        $comment = explode('--',$query);

        $union1 = explode("' UNION SELECT",$query);

        $union2 = explode("'; UNION SELECT",$query);

        $sleep = explode("SLEEP",$query);

        $benchmark = explode("BENCHMARK",$query);
        $union3 = explode("' UNION ALL SELECT",$query);

        $makeset = explode("MAKE",$query);

        $version  = explode("version",strtolower($query));

        $infoschema = explode("information_schema", strtolower($query));


        $randnum = explode("randnum(", strtolower($query));
        $elt = explode("elt(", strtolower($query));

        $ordmid = explode("ord(mid",strtolower($query));



        $xss  = explode("script",strtolower($query));





        if(count($comment) > 1 || count($union1) > 1 || count($union2) > 1 || count($sleep) > 1 || count($benchmark) > 1 || count($union3) > 1 || count($makeset) > 1|| count($version) > 1 || count($xss) > 1 || count($infoschema) > 1|| count($randnum) > 1|| count($elt) > 1 || count($ordmid) > 1 ){
        return false;   
        }
        /*

        if(strtoupper($operation) == 'SELECT'){

        if (mysqli_ping($this->connection1)) {
        //    printf ("ok!\n");
        }
        return mysqli_query( $this->connection1,$query);

        }

        */
        return $this->connection->query($query);
    }
   
   
            /*function to get some value needed in database table*/

   function get_name($table,$column,$id,$what)
   {

        $query="SELECT * FROM `".$table."` WHERE `".$column."` = '".$id."'";

        $selection = $this->query($query);

        $data = $selection->fetch( PDO::FETCH_ASSOC );

        if($what != NULL){

        return $data[$what];

        }

        else{

        return $selection;

        }

  }

  //employees

  function confirmEmployeePass($username, $password){
      /* Add slashes if necessary (for query) */
      
	   
	   $username = addslashes($username);

      /* Verify that user is in database */
      $q = $this->connection->prepare("SELECT password FROM ".TBL_EMPLOYEES." where name = :username");
       $result = $q->execute(array('username' => $username));
     
      if(!$result || ($q->rowCount()) < 1){
         return 1; //Indicates username failure
      }

      /* Retrieve password from result, strip slashes */
     $dbarray = $q->fetch( PDO::FETCH_ASSOC );
      $dbarray['password'] = stripslashes($dbarray['password']);
      $password = stripslashes($password);

      /* Validate that password is correct */
      if(md5($password) == $dbarray['password']){  
         return 0; //Success! Username and password confirmed
      }
      else{
         return 2; //Indicates password failure
      }
   }

   function getEmployeeInfo($username){
      $q = $this->connection->prepare("SELECT * FROM ".TBL_EMPLOYEES." WHERE name=:username");
       $result=$q->execute([
            'username'=>$username,
         ]);
      //$result = $this->query($q);
      /* Error occurred, return given name by default */
      if(!$result || ($q->rowCount()) < 1){
         return NULL;
      }
      /* Return result array */
      $dbarray= $q->fetch( PDO::FETCH_ASSOC );
      return $dbarray;
   }

   function updateEmployeeField($username, $field, $value){
      $q = $this->connection->prepare("UPDATE ".TBL_EMPLOYEES." SET $field=:value WHERE name=:username");
       return $q->execute([
           'value' => $value,
            'username'=>$username,
         ]);
   }

   function calcNumActiveEmployees(){
      /* Calculate number of users at site 
      $q = "SELECT * FROM ".TBL_ACTIVE_Employees;
      $result = $this->query($q);
      $this->num_active_users = $result->rowCount();*/
       $this->num_active_users = 1000;
   }

   function addActiveEmployee($username, $time){
      $q = $this->connection->prepare("UPDATE ".TBL_EMPLOYEES." SET timestamp = :timestamp WHERE name = :username");
      $result = $q->execute(array('timestamp'=>$time,'username' => $username));
    
    $del = $this->connection->prepare("delete from ".TBL_ACTIVE_EMPLOYEES." where username=:username");
    $del->execute(array(':username'=>$username));
      
      if(!TRACK_VISITORS) return;
      $q = $this->connection->prepare("INSERT INTO ".TBL_ACTIVE_EMPLOYEES." VALUES (:username, :time)");
       $result = $q->execute(array('username'=>$username,'time' => $time));
       
      $this->calcNumActiveEmployees();
   }

   function removeActiveGuestEmployee($ip){
      if(!TRACK_VISITORS) return;
      $q = $this->connection->prepare("DELETE FROM ".TBL_ACTIVE_EMPLOYEE_GUESTS." WHERE ip = :ip");
       $result = $q->execute(array('ip'=> $ip));
      $this->calcNumActiveGuests();
   }

   function confirmEmployeeID($username, $userid){
      /* Add slashes if necessary (for query) */
      //if(!get_magic_quotes_gpc()) {
	      $username = addslashes($username);
      //}

// check if user id is corrct ot not


      /* Verify that user is in database */
      $q = $this->connection->prepare("SELECT userid FROM ".TBL_EMPLOYEES." WHERE name=:username");
       $result = $q->execute(array('username' => $username));
       
       
       
      if( !$result || $q->rowCount() < 1){
         return 1; //Indicates username failure
      }
      /* Retrieve userid from result, strip slashes */
      $dbarray = $q->fetch( PDO::FETCH_ASSOC );
      $dbarray['userid'] = stripslashes($dbarray['userid']);
      $userid = stripslashes($userid);


      /* Validate that userid is correct */
      if($userid == $dbarray['userid']){
         return 0; //Success! Username and userid confirmed
      }
      else{
         return 2; //Indicates userid invalid
      }
   }

}



/* Create database connection */
$database = new MySQLDB;

?>