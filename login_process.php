<?php
include('include/session.php');
ini_set("display_errors","on");
error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

//echo "PHP is working";

//abc(); // intentional error

if(isset($_GET['loginForm']))
{
	
?>
    <section class="login-page">
    <div class="container">
      <div class="row justify-content-center align-items-center">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
          <div class="card-block border-0 shadow p-5">
            <form class="">
              <div class="title text-center py-xl-4 py-lg-2 mb-3">
                <h1 class="h3 text-uppercase font-weight-bold">Log in</h1>
              </div>

              <div class="form-field">
                <!-- <input type="text" id="user" class="input" autocomplete="off" required> -->

                <select class="custom-select" data-type="man" data-alias="User Type"  id="user_type" name="user_type" required>
											<option value="">select</option>
											<option value="1" >User</option>
											<option value="2" >Employee</option>
										</select>
                <!-- <label for="inputUser" class="label">UserType</label> -->
              </div>



              <div class="form-field">
                <input type="text" id="user" class="input" autocomplete="off" required>
                <label for="inputUser" class="label">Username</label>
              </div>
              <div class="form-field">
                <input type="password" id="pass" class="input" required>
                <label for="inputPassword" class="label">Password</label>
              </div>
			<div class="form-field">
				<img onClick="setState('captcha-img','<?php echo SECURE_PATH;?>captcha.php','');" src="<?php echo SECURE_PATH; ?>vendor/images/captcha.png" height="75" width="75">
				<span class="text-center" id="captcha-img"></span><br>
			</div>
				<div class="form-field">
					<input type="text" id="captcha" class="input" autocomplete="off" required>
					<label for="inputCaptcha" class="label">Captcha</label>
				</div>
              <div class="form-field text-center">
                <a  onClick="setState('loginForm','<?php echo SECURE_PATH;?>login_process.php','login=1&user='+$('#user').val()+'&pass='+$('#pass').val()+'&user_type='+$('#user_type').val()+'&captcha='+$('#captcha').val())" class="radius-25 btn btn-theme px-5">Login</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
	<script>
		setState('captcha-img','<?php echo SECURE_PATH;?>captcha.php','');
	</script>
 <?php
}

if(isset($_POST['login']))
{
	
	 
    $_POST = $session->cleanInput($_POST);

    //print_r($_POST);exit;

    $subuser = $_POST['user'];
    $subpass = $_POST['pass'];
    $captcha = $_POST['captcha'];
    

    if($_POST['user_type']==2){
      //echo 'hii';


      $login_error='';

        /* Username error checking */


          if(!$subuser || strlen($subuser = trim($subuser)) == 0){

            $login_error =  "* Username not entered";
          //$login_error =   "* Username not entered";
          }


          /* Password error checking */
          $field = "pass";  //Use field name for password
          if(!$subpass){
            $login_error =   "* Password not entered";
        // $login_error .=   "* Password not entered";
          }

      $field = "captcha";  //Use field name for password
          if(!$captcha){
            $login_error =   "* captcha not entered";
          //$login_error .=   "* captcha not entered";
          }



            $field = "user";  //Use field name for username
            $q = "SELECT valid FROM ".TBL_EMPLOYEES." WHERE name=:username";
            $q = $database->connection->prepare($q);
            $res = $q->execute(array('username'=>$subuser));

            $valid = $q->fetch(PDO::FETCH_ASSOC);

            if($q->rowCount() > 0)
            {
                if($valid['valid'] == 0)
                {
                    $login_error = '* Your Account has been Disabled! Please Contact Admin';
                }
            }

          /* Return if form errors exist */
          if(strlen($login_error) == 0){



          /* Checks that username is in database and password is correct */
          $subuser = stripslashes($subuser);
          $result = $database->confirmEmployeePass($subuser, $subpass);

        /* Check error codes */
        if($result == 1){
        $field = "user";
          //$usererror =   "* Username not found";
          $login_error =   "* Invalid Username /Password";
        }
        else if($result == 2)
        {
        $field = "pass";
          //$passerror =   "* Invalid password";
          $login_error =   "* Invalid Username /Password";
        }
        if($_SESSION['captcha']!=$_POST['captcha'])
        {

          $login_error =   "* Invalid Captcha";
        }
        }

          /* Login successful */ 
          if(strlen($login_error) == 0){
          /* Username and password correct, register session variables */
        $session->userinfo  = $database->getEmployeeInfo($subuser);

        

        //print_r($session->userinfo);exit;
        $session->username  = $_SESSION['username'] = $session->userinfo['name'];
        $session->userid    = $_SESSION['userid']   = $session->generateRandID();
        $session->userlevel = $_SESSION['userlevel']=$session->userinfo['userlevel'];
        $_SESSION['last_activity'] = time(); //your last activity was now
        $_SESSION['expire_time'] = 1*15*60; //expire time in hours*min*sec

        $_SESSION['user_type']= 2;

        $session->user_type =   $_SESSION['user_type'];

        session_regenerate_id();
        
          /* Insert userid into database and update active users table */
          $database->updateEmployeeField($session->username, "userid", $session->userid);
          $database->addActiveEmployee($session->username, $session->time);
          $database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

          /**
           * This is the cool part: the user has requested that we remember that
           * he's logged in, so we set two cookies. One to hold his username,
           * and one to hold his random value userid. It expires by the time
           * specified in constants.php. Now, next time he comes to our site, we will
           * log him in automatically, but only if he didn't log out before he left.
           */
    //      if($subremember){
            // setcookie("cookname", $session->username, time()+COOKIE_EXPIRE, COOKIE_PATH);
            // setcookie("cookid",   $session->userid,   time()+COOKIE_EXPIRE, COOKIE_PATH);

          $domain = "";
          $secure = "";
          $httponly = "";
          setcookie("cookname", base64_encode(bin2hex($session->username)), time()+COOKIE_EXPIRE, COOKIE_PATH,$domain,$secure,1);
          setcookie("cookid",   base64_encode(bin2hex($session->userid)),   time()+COOKIE_EXPIRE, COOKIE_PATH,$domain,$secure,1);
    //     }
    //$database->query("INSERT INTO  password_log VALUES (NULL,'".$_SESSION['username']."','".$_SERVER['REMOTE_ADDR']."','".time()."','login')");

        //Generate access and refresh tokens.
        $session->generateAccessToken();
        $session->generateRefreshToken();
        $log=$database->connection->prepare("INSERT INTO employees_log VALUES (NULL,:username,:timestamp)");

        //print_r($session);exit;
        $log->execute(array('username'=>$session->username,'timestamp'=>time()));

        //echo $session->username;exit;

    ?>
    <script type="text/javascript">
    window.location = '<?php echo SECURE_PATH;?>employee_home/';
    </script>
    <?php
          }
          /* Login failed */
          else{

      $session->csrfToken('/login_process.php login-user-form');
    ?>
        <section class="login-page">
        <div class="container">
          <div class="row justify-content-center align-items-center">
            <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
              <div class="card-block border-0 shadow p-5">
                <form class="">
                  <div class="title text-center py-xl-4 py-lg-2 mb-3">

                  </div>

                  <div class="form-field">
                    <!-- <input type="text" id="user" class="input" autocomplete="off" required> -->

                    <select class="custom-select" data-type="man" data-alias="User Type"  id="user_type" name="user_type" required>
                          <option value="">select</option>
                          <option value="1" >User</option>
                          <option value="2" >Employee</option>
                        </select>
                    <!-- <label for="inputUser" class="label">UserType</label> -->
                  </div>

                  <div class="form-field">
                    <input type="text" id="user" class="input" autocomplete="off" value="<?php echo $subuser; ?>" required>
                    <label for="inputUser" class="label">Username</label>
                  </div>
            
                  <div class="form-field">
                    <input type="password" id="pass" class="input" value="<?php echo $subpass; ?>" required>
                    <label for="inputPassword" class="label">Password</label>
                  </div>
            

            <div class="form-field">
                      <img onClick="setState('captcha-img','<?php echo SECURE_PATH;?>captcha.php','');" src="<?php echo SECURE_PATH; ?>vendor/images/captcha.png" height="75" width="75">
              <span class="text-center" id="captcha-img"></span><br>
                    </div>
            <div class="form-field">
              <input type="text" id="captcha" class="input" autocomplete="off" required>
              <label for="inputCaptcha" class="label">Captcha</label>
            </div>
            
            <span style="color: red"><?php echo $login_error; ?></span>

                  <div class="form-field text-center">
                    <a  onClick="setState('loginForm','<?php echo SECURE_PATH;?>login_process.php','login=1&user='+$('#user').val()+'&pass='+$('#pass').val()+'&user_type='+$('#user_type').val()+'&captcha='+$('#captcha').val())" class="radius-25 btn btn-theme px-5">Login</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
      <script>
        setState('captcha-img','<?php echo SECURE_PATH;?>captcha.php','');
      </script>

    <?php
          }


    }else{
    

   
        $login_error='';

        /* Username error checking */


          if(!$subuser || strlen($subuser = trim($subuser)) == 0){

            $login_error =  "* Username not entered";
          //$login_error =   "* Username not entered";
          }


          /* Password error checking */
          $field = "pass";  //Use field name for password
          if(!$subpass){
            $login_error =   "* Password not entered";
        // $login_error .=   "* Password not entered";
          }

      $field = "captcha";  //Use field name for password
          if(!$captcha){
            $login_error =   "* captcha not entered";
          //$login_error .=   "* captcha not entered";
          }



            $field = "user";  //Use field name for username
            $q = "SELECT valid FROM ".TBL_USERS." WHERE username=:username";
            $q = $database->connection->prepare($q);
            $res = $q->execute(array('username'=>$subuser));

            $valid = $q->fetch(PDO::FETCH_ASSOC);

            if($q->rowCount() > 0)
            {
                if($valid['valid'] == 0)
                {
                    $login_error = '* Your Account has been Disabled! Please Contact Admin';
                }
            }

          /* Return if form errors exist */
          if(strlen($login_error) == 0){



          /* Checks that username is in database and password is correct */
          $subuser = stripslashes($subuser);
          $result = $database->confirmUserPass($subuser, $subpass);

        /* Check error codes */
        if($result == 1){
        $field = "user";
          //$usererror =   "* Username not found";
          $login_error =   "* Invalid Username /Password";
        }
        else if($result == 2)
        {
        $field = "pass";
          //$passerror =   "* Invalid password";
          $login_error =   "* Invalid Username /Password";
        }
        if($_SESSION['captcha']!=$_POST['captcha'])
        {

          $login_error =   "* Invalid Captcha";
        }
        }

          /* Login successful */
          if(strlen($login_error) == 0){
          /* Username and password correct, register session variables */
        $session->userinfo  = $database->getUserInfo($subuser);
        $session->username  = $_SESSION['username'] = $session->userinfo['username'];
        $session->userid    = $_SESSION['userid']   = $session->generateRandID();
        $session->userlevel = $_SESSION['userlevel']=$session->userinfo['userlevel'];
        $_SESSION['last_activity'] = time(); //your last activity was now
        $_SESSION['expire_time'] = 1*15*60; //expire time in hours*min*sec

        $_SESSION['user_type']= 1;

        $session->user_type =   $_SESSION['user_type'];

        session_regenerate_id();

          /* Insert userid into database and update active users table */
          $database->updateUserField($session->username, "userid", $session->userid);
          $database->addActiveUser($session->username, $session->time);
          $database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

          /**
           * This is the cool part: the user has requested that we remember that
           * he's logged in, so we set two cookies. One to hold his username,
           * and one to hold his random value userid. It expires by the time
           * specified in constants.php. Now, next time he comes to our site, we will
           * log him in automatically, but only if he didn't log out before he left.
           */
    //      if($subremember){
            // setcookie("cookname", $session->username, time()+COOKIE_EXPIRE, COOKIE_PATH);
            // setcookie("cookid",   $session->userid,   time()+COOKIE_EXPIRE, COOKIE_PATH);

          $domain = "";
          $secure = "";
          $httponly = "";
          setcookie("cookname", base64_encode(bin2hex($session->username)), time()+COOKIE_EXPIRE, COOKIE_PATH,$domain,$secure,1);
          setcookie("cookid",   base64_encode(bin2hex($session->userid)),   time()+COOKIE_EXPIRE, COOKIE_PATH,$domain,$secure,1);
    //     }
    //$database->query("INSERT INTO  password_log VALUES (NULL,'".$_SESSION['username']."','".$_SERVER['REMOTE_ADDR']."','".time()."','login')");

        //Generate access and refresh tokens.
        $session->generateAccessToken();
        $session->generateRefreshToken();
        $log=$database->connection->prepare("INSERT INTO users_log VALUES (NULL,:username,:timestamp)");
        $log->execute(array('username'=>$session->username,'timestamp'=>time()));

    ?>
    <script type="text/javascript">
    window.location = '<?php echo SECURE_PATH;?>home/';
    </script>
    <?php
          }
          /* Login failed */
          else{

      $session->csrfToken('/login_process.php login-user-form');
    ?>
        <section class="login-page">
        <div class="container">
          <div class="row justify-content-center align-items-center">
            <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
              <div class="card-block border-0 shadow p-5">
                <form class="">
                  <div class="title text-center py-xl-4 py-lg-2 mb-3">

                  </div>

                  <div class="form-field">
                    <!-- <input type="text" id="user" class="input" autocomplete="off" required> -->

                    <select class="custom-select" data-type="man" data-alias="User Type"  id="user_type" name="user_type" required>
                          <option value="">select</option>
                          <option value="1" >User</option>
                          <option value="2" >Employee</option>
                        </select>
                    <!-- <label for="inputUser" class="label">UserType</label> -->
                  </div>

                  <div class="form-field">
                    <input type="text" id="user" class="input" autocomplete="off" value="<?php echo $subuser; ?>" required>
                    <label for="inputUser" class="label">Username</label>
                  </div>
            
                  <div class="form-field">
                    <input type="password" id="pass" class="input" value="<?php echo $subpass; ?>" required>
                    <label for="inputPassword" class="label">Password</label>
                  </div>
            

            <div class="form-field">
                      <img onClick="setState('captcha-img','<?php echo SECURE_PATH;?>captcha.php','');" src="<?php echo SECURE_PATH; ?>vendor/images/captcha.png" height="75" width="75">
              <span class="text-center" id="captcha-img"></span><br>
                    </div>
            <div class="form-field">
              <input type="text" id="captcha" class="input" autocomplete="off" required>
              <label for="inputCaptcha" class="label">Captcha</label>
            </div>
            
            <span style="color: red"><?php echo $login_error; ?></span>

                  <div class="form-field text-center">
                    <a  onClick="setState('loginForm','<?php echo SECURE_PATH;?>login_process.php','login=1&user='+$('#user').val()+'&pass='+$('#pass').val()+'&captcha='+$('#captcha').val())" class="radius-25 btn btn-theme px-5">Login</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
      <script>
        setState('captcha-img','<?php echo SECURE_PATH;?>captcha.php','');
      </script>

    <?php
          }

    }

  }


  if(isset($_REQUEST['userLogout']))
  {

      $session->logout();
  ?>
  <script type="text/javascript">
  window.location = '<?php echo SECURE_PATH;?>';
  </script>
  <?php
  }

  if(isset($_REQUEST['checkInactivity']))
  {
    //echo $_SESSION['expire_time'];

    if(time()>$_SESSION['expire_time'])
    {
      ?>
      <script>
        alert(<?php echo  $_SESSION['expire_time']; ?>+' expired');
        //setState('content-page','<?php echo SECURE_PATH; ?>login_process.php','userLogout=true');
      </script>
      <?php
    }


  }
  ?>
  <script type="text/javascript">
  $('#loginForm').keyup(function(e) {

    if ( e.which == 13) {
        //stuff
  setState('loginForm','<?php echo SECURE_PATH;?>login_process.php','login=1&user='+$('#user').val()+'&pass='+$('#pass').val()+'&user_type='+$('#user_type').val()+'&captcha='+$('#captcha').val());
  }

  });

</script>
