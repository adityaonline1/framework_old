<?php
ini_set("display_errors", 0);
//error_reporting(E_ALL);

error_reporting(E_ALL);
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');

//error_reporting(E_ALL);
//ini_set("display_errors",'1');

//abc();

include("database.php");
//include("mailer.php"); 
include("form.php");
include("pagination.php");
date_default_timezone_set('Asia/Kolkata');


require_once("libs/jwt/vendor/autoload.php");

use Firebase\JWT\JWT;

class Session
{
    var $access_token = '';
    var $refresh_token = '';

    var $username;                 //Username given on sign-up
    var $userid;                   //Random value generated on current login
    var $userlevel;                //The level to which the user pertains
    var $time;                     //Time user was last active (page loaded)
    var $logged_in;                //True if user is logged in, false otherwise
    var $userinfo = array();      //The array holding all user info
    var $url;                      //The page url current being viewed
    var $referrer;                 //Last recorded site page viewed
    var $token;                 // stroing csrf token
    var $tokenGeneration = array("addForm", "addForm1", "addForm2", "addForm3", "loginForm");
    var $tokenValidation = array("validateForm", "validateForm1", "validateForm2", "validateForm3", "login");





    /**
     * Note: referrer should really only be considered the actual
     * page referrer in process.php, any other time it may be
     * inaccurate.
     */

    /* Class constructor */
    function __construct()
    {
        $this->time = time();
        $this->startSession();
        if (isset($_REQUEST['0'])) {
            $_GET = $_POST = $_REQUEST = $this->decodeString($_REQUEST['0']);
        }

        if (isset($_SESSION['last_activity']) && isset($_SESSION['expire_time'])) {
            //echo $_SESSION['last_activity']." ".$_SESSION['expire_time'];
            if ($_SESSION['last_activity'] < time() - $_SESSION['expire_time']) {
                //have we expired?
                //redirect to logout.php
                //header('Location: http://yoursite.com/logout.php'); //change yoursite.com to the name of you site!!
                unset($_SESSION['last_activity']);
                unset($_SESSION['expire_time']);
?>
                <script>
                    alert('session expired')
                </script>
        <?php
                $this->logout();
            } else {
                //if we haven't expired:
                $_SESSION['last_activity'] = time(); //this was the moment of last activity.
            }
            //echo "Expires in ".date("h:i:s A",($_SESSION['last_activity']));

        }


        $allowed_host = array('localhost', '');

        if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_host)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            exit;
        }


        //TokenGeneration-addForm,addForm1,addForm2,addForm3
        //TokenValidation-validateForm,validateForm1,validateForm2,validateForm3
        //



        foreach ($this->tokenGeneration as $key => $value) {

            if (isset($_REQUEST[$value])) {
                $_GET = $_POST = $_REQUEST = $this->cleanInput($_REQUEST);
                $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $_SESSION['temp_var'] = $actual_link . " " . $value;
                $this->csrfToken($_SESSION['temp_var']);
                //echo $_SESSION['temp_var']."<br>";
            }
        }

        foreach ($this->tokenValidation as $key1 => $value1) {
            if (isset($_REQUEST[$value1])) {
                $_GET = $_POST = $_REQUEST = $this->cleanInput($_REQUEST);

                if (!$this->verifyToken($_SESSION['temp_var']))
                    exit;
            }
        }
    }

    /**
     * startSession - Performs all the actions necessary to
     * initialize this session object. Tries to determine if the
     * the user has logged in already, and sets the variables
     * accordingly. Also takes advantage of this page load to
     * update the active visitors tables.
     */
    function startSession()
    {
        global $database;  //The database connection
        session_start();   //Tell PHP to start the session

        /* Determine if user is logged in */
        $this->logged_in = $this->checkLogin();

        //print_r($_SESSION['user_type']);exit;

        if($_SESSION['user_type']==1){
        

            /**
             * Set guest value to users not logged in, and update
             * active guests table accordingly.
             */
            if (!$this->logged_in) {
                $this->username = $_SESSION['username'] = GUEST_NAME;
                $this->userlevel = GUEST_LEVEL;
                $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
            }
            
            /* Update users last active timestamp */ else {
                $database->addActiveUser($this->username, $this->time);
                if (isset($_SESSION['access_token']))
                    $this->access_token = $_SESSION['access_token'];

                if (isset($_SESSION['refresh_token']))
                    $this->refresh_token = $_SESSION['refresh_token'];
                $_SESSION['expire_time'] += 10;
            }

            /* Remove inactive visitors from database */

            if (date('i') % 20 == 0) {
                $database->removeInactiveUsers();
                $database->removeInactiveGuests();
            }


            /* Set referrer page */
            if (isset($_SESSION['url'])) {
                $this->referrer = $_SESSION['url'];
            } else {
                $this->referrer = "/";
            }

            /* Set current url */
            $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
        }else{
            /**
             * Set guest value to users not logged in, and update
             * active guests table accordingly.
             */
            if (!$this->logged_in) {
                $this->username = $_SESSION['username'] = GUEST_NAME;
                $this->userlevel = GUEST_LEVEL;
                $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
            }
            
            /* Update users last active timestamp */ else {
                $database->addActiveEmployee($this->username, $this->time);
                if (isset($_SESSION['access_token']))
                    $this->access_token = $_SESSION['access_token'];

                if (isset($_SESSION['refresh_token']))
                    $this->refresh_token = $_SESSION['refresh_token'];
                $_SESSION['expire_time'] += 10;
            }

            /* Remove inactive visitors from database */

            if (date('i') % 20 == 0) {
                $database->removeInactiveUsers();
                $database->removeInactiveGuests();
            }


            /* Set referrer page */
            if (isset($_SESSION['url'])) {
                $this->referrer = $_SESSION['url'];
            } else {
                $this->referrer = "/";
            }

            /* Set current url */
            $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
        }
    }

    /**
     * checkLogin - Checks if the user has already previously
     * logged in, and a session with the user has already been
     * established. Also checks to see if user has been remembered.
     * If so, the database is queried to make sure of the user's
     * authenticity. Returns true if the user has logged in.
     */
    function checkLogin()
    {
        global $database;  //The database connection
        /* Check if user has been remembered */
        //print_r($_SESSION['user_type']);exit;

        if($_SESSION['user_type']==2){
            //echo 'hi';exit;

            //print_r($_COOKIE['cookid']);exit;

            $_SESSION['userid'] = hex2bin(base64_decode($_COOKIE['cookid']));
            if(!empty($_COOKIE['cookid'])){
                $_SESSION['userid'] = hex2bin(base64_decode($_COOKIE['cookid']));
            }else{
                $_SESSION['userid'] = '';
            }


            if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
                $this->username = $_SESSION['username'] = hex2bin(base64_decode($_COOKIE['cookname']));
                $this->userid   = $_SESSION['userid']   = hex2bin(base64_decode($_COOKIE['cookid']));
            }

            //Refresh Token validation
            if (isset($_SESSION['refresh_token'])) {
                if (!$this->validateRefreshToken())
                    return false;
            }

            /* Username and userid have been set and not guest */
            if (
                isset($_SESSION['username']) && isset($_SESSION['userid']) &&
                $_SESSION['username'] != GUEST_NAME
            ) {
                /* Confirm that username and userid are valid */
                if ($database->confirmEmployeeID($_SESSION['username'], $_SESSION['userid']) != 0) {
                    /* Variables are incorrect, user not logged in */
                    unset($_SESSION['username']);
                    unset($_SESSION['userid']);
                    return false;
                }

                /* User is logged in, set class variables */
                $this->userinfo  = $database->getEmployeeInfo($_SESSION['username']);
                $this->username  = $this->userinfo['username'];
                $this->userid    = $this->userinfo['userid'];
                $this->userlevel = $this->userinfo['userlevel'];

                /* auto login hash expires in three days */
                if ($this->userinfo['hash_generated'] < (time() - (60 * 60 * 24 * 3))) {
                    /* Update the hash */
                    $database->updateUserField($this->userinfo['username'], 'hash', $this->generateRandID());
                    $database->updateUserField($this->userinfo['username'], 'hash_generated', time());
                }

                return true;
            }
            /* User not logged in */ else {
                return false;
            }

        }else{

            $_SESSION['userid'] = hex2bin(base64_decode($_COOKIE['cookid']));
            if(!empty($_COOKIE['cookid'])){
                $_SESSION['userid'] = hex2bin(base64_decode($_COOKIE['cookid']));
            }else{
                $_SESSION['userid'] = '';
            }


            if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
                $this->username = $_SESSION['username'] = hex2bin(base64_decode($_COOKIE['cookname']));
                $this->userid   = $_SESSION['userid']   = hex2bin(base64_decode($_COOKIE['cookid']));
            }

            //Refresh Token validation
            if (isset($_SESSION['refresh_token'])) {
                if (!$this->validateRefreshToken())
                    return false;
            }

            /* Username and userid have been set and not guest */
            if (
                isset($_SESSION['username']) && isset($_SESSION['userid']) &&
                $_SESSION['username'] != GUEST_NAME
            ) {
                /* Confirm that username and userid are valid */
                if ($database->confirmUserID($_SESSION['username'], $_SESSION['userid']) != 0) {
                    /* Variables are incorrect, user not logged in */
                    unset($_SESSION['username']);
                    unset($_SESSION['userid']);
                    return false;
                }

                /* User is logged in, set class variables */
                $this->userinfo  = $database->getUserInfo($_SESSION['username']);
                $this->username  = $this->userinfo['username'];
                $this->userid    = $this->userinfo['userid'];
                $this->userlevel = $this->userinfo['userlevel'];

                /* auto login hash expires in three days */
                if ($this->userinfo['hash_generated'] < (time() - (60 * 60 * 24 * 3))) {
                    /* Update the hash */
                    $database->updateUserField($this->userinfo['username'], 'hash', $this->generateRandID());
                    $database->updateUserField($this->userinfo['username'], 'hash_generated', time());
                }

                return true;
            }
            /* User not logged in */ else {
                return false;
            }
        }
    }

    /**
     * login - The user has submitted his username and password
     * through the login form, this function checks the authenticity
     * of that information in the database and creates the session.
     * Effectively logging in the user if all goes well.
     */
    function login($subuser, $subpass, $subremember)
    {
        global $database, $form;  //The database and form object

        /* Username error checking */
        $field = "user";  //Use field name for username
        $q = "SELECT valid FROM " . TBL_USERS . " WHERE username='$subuser'";
        $valid = $database->query($q);
        //$valid = mysqli_fetch_array($valid);
        $valid = $valid->fetch(PDO::FETCH_ASSOC);

        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "* Username not entered");
        } else {
            /* Check if username is not alphanumeric */
            if (!ctype_alnum($subuser)) {
                $form->setError($field, "* Username not alphanumeric");
            }
        }

        /* Password error checking */
        $field = "pass";  //Use field name for password
        if (!$subpass) {
            $form->setError($field, "* Password not entered");
        }

        /* Return if form errors exist */
        if ($form->num_errors > 0) {
            return false;
        }

        /* Checks that username is in database and password is correct */
        $subuser = stripslashes($subuser);
        $result = $database->confirmUserPass($subuser, $subpass);

        /* Check error codes */
        if ($result == 1) {
            $field = "user";
            $form->setError($field, "* Username not found");
        } else if ($result == 2) {
            $field = "pass";
            $form->setError($field, "* Invalid password");
        }

        /* Return if form errors exist */
        if ($form->num_errors > 0) {
            return false;
        }


        if (EMAIL_WELCOME) {
            if ($valid['valid'] == 0) {
                $form->setError($field, "* User's account has not yet been confirmed.");
            }
        }

        /* Return if form errors exist */
        if ($form->num_errors > 0) {
            return false;
        }



        /* Username and password correct, register session variables */
        $this->userinfo  = $database->getUserInfo($subuser);
        $this->username  = $_SESSION['username'] = $this->userinfo['username'];
        $this->userid    = $_SESSION['userid']   = $this->generateRandID();
        $this->userlevel = $this->userinfo['userlevel'];

        /* Insert userid into database and update active users table */
        $database->updateUserField($this->username, "userid", $this->userid);
        $database->addActiveUser($this->username, $this->time);
        $database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

        /**
         * This is the cool part: the user has requested that we remember that
         * he's logged in, so we set two cookies. One to hold his username,
         * and one to hold his random value userid. It expires by the time
         * specified in constants.php. Now, next time he comes to our site, we will
         * log him in automatically, but only if he didn't log out before he left.
         */
        if ($subremember) {
            $domain = "";
            $secure = "";
            $httponly = "true";
            setcookie("cookname", base64_encode(bin2hex($this->username)), time() + COOKIE_EXPIRE, COOKIE_PATH, $domain, $secure, $httponly);
            setcookie("cookid",   base64_encode(bin2hex($this->userid)),   time() + COOKIE_EXPIRE, COOKIE_PATH, $domain, $secure, $httponly);
        }

        /* Login completed successfully */
        return true;
    }

    /**
     * logout - Gets called when the user wants to be logged out of the
     * website. It deletes any cookies that were stored on the users
     * computer as a result of him wanting to be remembered, and also
     * unsets session variables and demotes his user level to guest.
     */
    function logout()
    {
        global $database;  //The database connection
        /**
         * Delete cookies - the time must be in the past,
         * so just negate what you added when creating the
         * cookie.
         */
        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            setcookie("cookname", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
            setcookie("cookid",   "", time() - COOKIE_EXPIRE, COOKIE_PATH);
        }

        /* Unset PHP session variables */
        unset($_SESSION['username']);
        unset($_SESSION['userid']);

        /* Reflect fact that user has logged out */
        $this->logged_in = false;

        /**
         * Remove from active users table and add to
         * active guests tables.
         */
        $database->removeActiveUser($this->username);
        $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);

        /* Set user level to guest */
        $this->username  = GUEST_NAME;
        $this->userlevel = GUEST_LEVEL;

        //Remove access and refresh tokens from session
        if (isset($_SESSION['access_token'])) {
            unset($_SESSION['access_token']);
            unset($_SESSION['refresh_token']);
        }

        //regenerate php session id
        session_regenerate_id();
        ?>
        <script>
            window.location = '<?php echo SECURE_PATH ?>';
        </script>
    <?php

    }


    // decode string

    public function decodeString($encodeString)
    {
        $key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");

        //    $ciphertext_dec = urldecode($encodeString);

        $ciphertext_dec = base64_decode($encodeString);

        $iv_dec = pack('H*', "101112131415161718191a1b1c1d1e1f");

        $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

        $block = 16;
        $pad = ord($str[($len = strlen($str)) - 1]);
        $len = strlen($str);
        $pad = ord($str[$len - 1]);
        $str = substr($str, 0, strlen($str) - $pad);

        //    echo 'from session i/p:'.$encodeString;
        //    echo 'from session o/p:'.$str;


        return json_decode($str, true);
    }


    /**
     * register - Gets called when the user has just submitted the
     * registration form. Determines if there were any errors with
     * the entry fields, if so, it records the errors and returns
     * 1. If no errors were found, it registers the new user and
     * returns 0. Returns 2 if registration failed.
     */
    function register($subuser, $subpass, $subemail, $subname)
    {

        global $database, $form, $mailer;  //The database, form and mailer object

        /* Username error checking */
        $field = "user";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "* Username not entered");
        } else {
            /* Spruce up username, check length */
            $subuser = stripslashes($subuser);
            if (strlen($subuser) < 5) {
                $form->setError($field, "* Username below 5 characters");
            } else if (strlen($subuser) > 30) {
                $form->setError($field, "* Username above 30 characters");
            }
            /* Check if username is not alphanumeric */ else if (!ctype_alnum($subuser)) {
                $form->setError($field, "* Username not alphanumeric");
            }
            /* Check if username is reserved */ else if (strcasecmp($subuser, GUEST_NAME) == 0) {
                $form->setError($field, "* Username reserved word");
            }
            /* Check if username is already in use */ else if ($database->usernameTaken($subuser)) {
                $form->setError($field, "* Username already in use");
            }
            /* Check if username is banned */ else if ($database->usernameBanned($subuser)) {
                $form->setError($field, "* Username banned");
            }
        }

        /* Password error checking */
        $field = "pass";  //Use field name for password
        if (!$subpass) {
            $form->setError($field, "* Password not entered");
        } else {
            /* Spruce up password and check length*/
            $subpass = stripslashes($subpass);
            if (strlen($subpass) < 4) {
                $form->setError($field, "* Password too short");
            }
            /* Check if password is not alphanumeric */ else if (!ctype_alnum(($subpass = trim($subpass)))) {
                $form->setError($field, "* Password not alphanumeric");
            }
            /**
             * Note: I trimmed the password only after I checked the length
             * because if you fill the password field up with spaces
             * it looks like a lot more characters than 4, so it looks
             * kind of stupid to report "password too short".
             */
        }

        /* Email error checking */
        $field = "email";  //Use field name for email
        if (!$subemail || strlen($subemail = trim($subemail)) == 0) {
            $form->setError($field, "* Email not entered");
        } else {
            /* Check if valid email address */
            if (filter_var($subemail, FILTER_VALIDATE_EMAIL) == FALSE) {
                $form->setError($field, "* Email invalid");
            }
            /* Check if email is already in use */
            if ($database->emailTaken($subemail)) {
                $form->setError($field, "* Email already in use");
            }

            $subemail = stripslashes($subemail);
        }

        /* Name error checking */
        $field = "name";
        if (!$subname || strlen($subname = trim($subname)) == 0) {
            $form->setError($field, "* Name not entered");
        } else {
            $subname = stripslashes($subname);
        }

        $randid = $this->generateRandID();

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            if ($database->addNewUser($subuser, md5($subpass), $subemail, $randid, $subname)) {
                if (EMAIL_WELCOME) {
                    $mailer->sendWelcome($subuser, $subemail, $subpass, $randid);
                }
                return 0;  //New user added succesfully
            } else {
                return 2;  //Registration attempt failed
            }
        }
    }

    /**
     * editAccount - Attempts to edit the user's account information
     * including the password, which it first makes sure is correct
     * if entered, if so and the new password is in the right
     * format, the change is made. All other fields are changed
     * automatically.
     */
    function editAccount($subcurpass, $subnewpass, $subemail, $subname)
    {
        global $database, $form;  //The database and form object
        /* New password entered */
        if ($subnewpass) {
            /* Current Password error checking */
            $field = "curpass";  //Use field name for current password
            if (!$subcurpass) {
                $form->setError($field, "* Current Password not entered");
            } else {
                /* Check if password too short or is not alphanumeric */
                $subcurpass = stripslashes($subcurpass);
                if (
                    strlen($subcurpass) < 4 ||
                    !preg_match("^([0-9a-z])+$", ($subcurpass = trim($subcurpass)))
                ) {
                    $form->setError($field, "* Current Password incorrect");
                }
                /* Password entered is incorrect */
                if ($database->confirmUserPass($this->username, md5($subcurpass)) != 0) {
                    $form->setError($field, "* Current Password incorrect");
                }
            }

            /* New Password error checking */
            $field = "newpass";  //Use field name for new password
            /* Spruce up password and check length*/
            $subpass = stripslashes($subnewpass);
            if (strlen($subnewpass) < 4) {
                $form->setError($field, "* New Password too short");
            }
            /* Check if password is not alphanumeric */ else if (!preg_match("^([0-9a-z])+$", ($subnewpass = trim($subnewpass)))) {
                $form->setError($field, "* New Password not alphanumeric");
            }
        }
        /* Change password attempted */ else if ($subcurpass) {
            /* New Password error reporting */
            $field = "newpass";  //Use field name for new password
            $form->setError($field, "* New Password not entered");
        }

        /* Email error checking */
        $field = "email";  //Use field name for email
        if ($subemail && strlen($subemail = trim($subemail)) > 0) {
            /* Check if valid email address */
            if (filter_var($subemail, FILTER_VALIDATE_EMAIL) == FALSE) {
                $form->setError($field, "* Email invalid");
            }
            $subemail = stripslashes($subemail);
        }

        /* Name error checking */
        $field = "name";
        if (!$subname || strlen($subname = trim($subname)) == 0) {
            $form->setError($field, "* Name not entered");
        } else {

            $subname = stripslashes($subname);
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return false;  //Errors with form
        }

        /* Update password since there were no errors */
        if ($subcurpass && $subnewpass) {
            $database->updateUserField($this->username, "password", md5($subnewpass));
        }

        /* Change Email */
        if ($subemail) {
            $database->updateUserField($this->username, "email", $subemail);
        }

        /* Change Name */
        if ($subname) {
            $database->updateUserField($this->username, "name", $subname);
        }

        /* Success! */
        return true;
    }

    /**
     * isAdmin - Returns true if currently logged in user is
     * an administrator, false otherwise.
     */
    function isAdmin()
    {
        return ($this->userlevel == ADMIN_LEVEL ||
            $this->username  == ADMIN_NAME);
    }

    /**
     * isAuthor - Returns true if currently logged in user is
     * an author or an administrator, false otherwise.
     */
    function isAuthor()
    {
        return ($this->userlevel == AUTHOR_LEVEL ||
            $this->userlevel == ADMIN_LEVEL);
    }

    /**
     * generateRandID - Generates a string made up of randomized
     * letters (lower and upper case) and digits and returns
     * the md5 hash of it to be used as a userid.
     */
    function generateRandID()
    {
        return md5($this->generateRandStr(16));
    }

    /**
     * generateRandStr - Generates a string made up of randomized
     * letters (lower and upper case) and digits, the length
     * is a specified parameter.
     */
    function generateRandStr($length)
    {
        $randstr = "";
        for ($i = 0; $i < $length; $i++) {
            $randnum = mt_rand(0, 61);
            if ($randnum < 10) {
                $randstr .= chr($randnum + 48);
            } else if ($randnum < 36) {
                $randstr .= chr($randnum + 55);
            } else {
                $randstr .= chr($randnum + 61);
            }
        }
        return $randstr;
    }

    function cleanInput($post = array())
    {
        foreach ($post as $k => $v) {

            $post[$k] = trim(htmlspecialchars($v));
            if (strlen(htmlspecialchars($v)) == 0) {

                //$post[$k] = '0';

            }
        }
        return $post;
    }


    /*Pagination code*/
    function showPagination($pagename, $tbl_name, $start, $limit, $page, $condition)
    {
        global $database;

        // How many adjacent pages should be shown on each side?
        $adjacents = 3;

        /*
        First get total number of rows in data table.
        If you have a WHERE clause in your query, make sure you mirror it here.
        */
        //echo "SELECT COUNT(*) as num FROM $tbl_name $condition ";
        $query = "SELECT COUNT(*) as num FROM $tbl_name $condition ";

        //echo $query;

        $tpq = $database->query($query);
        $tp = $tpq->fetch(PDO::FETCH_ASSOC);
        //        echo $total_pages['num'].',';
        $total_pages = $tp['num'];




        /* Setup vars for query. */
        $targetpage = $pagename;     //your file name  (the name of this file)
        //how many items to show per page




        /* Setup page vars for display. */
        if ($page == 0) $page = 1;                    //if no page var is given, default to 1.
        $prev = $page - 1;                            //previous page is page - 1
        $next = $page + 1;                            //next page is page + 1
        $lastpage = ceil($total_pages / $limit);        //lastpage is = total pages / items per page, rounded up.
        $lpm1 = $lastpage - 1;                        //last page minus 1



        /*
        Now we apply our rules and draw the pagination object.
        We're actually saving the code to a variable in case we want to draw it more than once.
        */
        $pagination = "";
        if ($lastpage > 1) {

            $pagination .= "<ul class=\"pagination\">";
            //previous button
            if ($page > 1) {

                $pagination .= " <li class=\"page-item\">
            <a class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $prev . "')\">Previous</a></li>";
            } else {
                $pagination .= "<li class=\"page-item disabled\">
            <a class=\"page-link\">Previous</a></li>";
            }
            //pages
            if ($lastpage < 7 + ($adjacents * 2))    //not enough pages to bother breaking it up
            {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
                    else
                        $pagination .= "<li class=\"page-item \"><a class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $counter . "')\">$counter</a></li>";
                }
            } elseif ($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some
            {
                //close to beginning; only hide later pages
                if ($page < 1 + ($adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $page)
                            $pagination .= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
                        else
                            $pagination .= "<li class=\"page-item \"><a class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $counter . "')\">$counter</a></li>";
                    }
                    $pagination .= "...";
                    $pagination .= "<li class=\"page-item \"><a class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $lpm1 . "')\">$lpm1</a></li>";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $lastpage . "')\">$lastpage</a></li>";
                }
                //in middle; hide some front and some back
                elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\"  onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=1')\">1</a></li>";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=2')\">2</a></li>";
                    $pagination .= "...";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page)
                            $pagination .= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
                        else
                            $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $counter . "')\">$counter</a></li>";
                    }
                    $pagination .= "...";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\">$lpm1</a></li>";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $lastpage . "')\">$lastpage</a></li>";
                }
                //close to end; only hide early pages
                else {
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','page=1')\">1</a></li>";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\"  onclick=\"setStateGet('adminTable','" . $targetpage . "','page=2')\">2</a></li>";
                    $pagination .= "...";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page)
                            $pagination .= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
                        else
                            $pagination .= "<li class=\"page-item active\"><a class=\"page-link\"  onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $counter . "')\">$counter</a></li>";
                    }
                }
            }

            //next button
            if ($page < $counter - 1)
                $pagination .= "<li class=\"page-item active\"><a class=\"page-link\"  onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $next . "')\">Next</a></li>";
            else
                $pagination .= "<li class=\"page-item disabled\">
            <a class=\"page-link\">Next</a></li>";
            $pagination .= "</ul>\n";
        }


        return $pagination;
    }

    /*Pagination code*/
    function showPaginationTest($pagename, $con, $tbl_name, $select_fields, $base_con, $condition, $order, $start, $limit, $page)
    {
        global $database;

        $display = '';

        $names = array_keys($con);

        $display .= $con['baseName'] . "=1";
        for ($i = 0; $i < count($con) - 1; $i++) {
            $display .= "&" . $names[$i] . "=" . $con[$names[$i]];
        }
        //echo $display;
        // How many adjacent pages should be shown on each side?
        $adjacents = 3;

        /*
        First get total number of rows in data table.
        If you have a WHERE clause in your query, make sure you mirror it here.
        */
        //echo "SELECT COUNT(*) as num FROM $tbl_name $condition ";
        //$query = "SELECT COUNT(*) as num FROM $tbl_name $condition ";

        //echo $query;
        //unset($con['baseName']);
        $tpq = $database->select($tbl_name, $select_fields, $base_con, $condition, $order);
        //$tp=$tpq->fetch(PDO::FETCH_ASSOC);
        //        echo $total_pages['num'].',';
        $total_pages = $tpq->rowCount();




        /* Setup vars for query. */
        $targetpage = $pagename;     //your file name  (the name of this file)
        //how many items to show per page




        /* Setup page vars for display. */
        if ($page == 0) $page = 1;                    //if no page var is given, default to 1.
        $prev = $page - 1;                            //previous page is page - 1
        $next = $page + 1;                            //next page is page + 1
        $lastpage = ceil($total_pages / $limit);        //lastpage is = total pages / items per page, rounded up.
        $lpm1 = $lastpage - 1;                        //last page minus 1



        /*
        Now we apply our rules and draw the pagination object.
        We're actually saving the code to a variable in case we want to draw it more than once.
        */
        $pagination = "";
        if ($lastpage > 1) {

            $pagination .= "<ul class=\"pagination\">";
            //previous button
            if ($page > 1) {

                $pagination .= " <li class=\"page-item\">
            <a class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','$display&page=" . $prev . "')\">Previous</a></li>";
            } else {
                $pagination .= "<li class=\"page-item disabled\">
            <a class=\"page-link\">Previous</a></li>";
            }
            //pages
            if ($lastpage < 7 + ($adjacents * 2))    //not enough pages to bother breaking it up
            {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
                    else
                        $pagination .= "<li class=\"page-item \"><a class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','$display&page=" . $counter . "')\">$counter</a></li>";
                }
            } elseif ($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some
            {
                //close to beginning; only hide later pages
                if ($page < 1 + ($adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $page)
                            $pagination .= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
                        else
                            $pagination .= "<li class=\"page-item \"><a class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','$display&page=" . $counter . "')\">$counter</a></li>";
                    }
                    $pagination .= "...";
                    $pagination .= "<li class=\"page-item \"><a class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','$display=1&page=" . $lpm1 . "')\">$lpm1</a></li>";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','$display&page=" . $lastpage . "')\">$lastpage</a></li>";
                }
                //in middle; hide some front and some back
                elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\"  onclick=\"setStateGet('adminTable','" . $targetpage . "','$display&page=1')\">1</a></li>";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','$display&page=2')\">2</a></li>";
                    $pagination .= "...";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page)
                            $pagination .= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
                        else
                            $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','$display&page=" . $counter . "')\">$counter</a></li>";
                    }
                    $pagination .= "...";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\">$lpm1</a></li>";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','$display&page=" . $lastpage . "')\">$lastpage</a></li>";
                }
                //close to end; only hide early pages
                else {
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','page=1')\">1</a></li>";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\"  onclick=\"setStateGet('adminTable','" . $targetpage . "','page=2')\">2</a></li>";
                    $pagination .= "...";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page)
                            $pagination .= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
                        else
                            $pagination .= "<li class=\"page-item active\"><a class=\"page-link\"  onclick=\"setStateGet('adminTable','" . $targetpage . "','$display&page=" . $counter . "')\">$counter</a></li>";
                    }
                }
            }

            //next button
            if ($page < $counter - 1)
                $pagination .= "<li class=\"page-item active\"><a class=\"page-link\"  onclick=\"setStateGet('adminTable','" . $targetpage . "','$display&page=" . $next . "')\">Next</a></li>";
            else
                $pagination .= "<li class=\"page-item disabled\">
            <a class=\"page-link\">Next</a></li>";
            $pagination .= "</ul>\n";
        }


        return $pagination;
    }




    /*imageResize - Resizes images of all types to the specified dimentions.
  Preserves image alpha channel information also*/

    function image_resize($src, $dst, $width, $height, $crop = 0)
    {

        if (!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

        $type = strtolower(substr(strrchr($src, "."), 1));
        if ($type == 'jpeg') $type = 'jpg';
        switch ($type) {
            case 'bmp':
                $img = imagecreatefromwbmp($src);
                break;
            case 'gif':
                $img = imagecreatefromgif($src);
                break;
            case 'jpg':
                $img = imagecreatefromjpeg($src);
                break;
            case 'png':
                $img = imagecreatefrompng($src);
                break;
            default:
                return "Unsupported picture type!";
        }

        // resize
        if (is_array($crop)) {
            // $_SESSION['crop'] = 'Is here.. reading crop array';
            $ratio = max($width / $w, $height / $h);

            if ($w < $width or $h < $height) {
                //$_SESSION['crop'].= "Picture is too small!".$ratio;

            }

            $h = $crop['height'];
            $x = $crop['x'];
            $w = $crop['width'];
            $y = $crop['y'];
        } else {
            if ($w < $width and $h < $height) return "Picture is too small!";
            $ratio = min($width / $w, $height / $h);
            $width = $w * $ratio;
            $height = $h * $ratio;
            $x = 0;
            $y = 0;
        }

        $new = imagecreatetruecolor($width, $height);

        // preserve transparency
        if ($type == "gif" or $type == "png") {
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        // $_SESSION['crop'].= "x:".$x."--y:".$y."--W:".$w."--H:".$h."--Width:".$width."--Height:".$height;
        imagecopyresampled($new, $img, 0, 0, $x, $y, $width, $height, $w, $h);

        switch ($type) {
            case 'bmp':
                imagewbmp($new, $dst);
                break;
            case 'gif':
                imagegif($new, $dst);
                break;
            case 'jpg':
                imagejpeg($new, $dst);
                break;
            case 'png':
                imagepng($new, $dst);
                break;
        }
        return true;
    }


    function commonUserCSS()
    {
    ?>
        <!-- Bootstrap core CSS-->
        <link href="<?php echo SECURE_PATH; ?>vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template-->
        <link href="<?php echo SECURE_PATH; ?>vendor/app/css/style.css" rel="stylesheet">

        <!-- Scrollbar Custom CSS -->
        <link rel="stylesheet" href="<?php echo SECURE_PATH; ?>vendor/jquery-mousewheel-scrollbar/css/jquery.mCustomScrollbar.min.css">
    <?php
    }

    function commonUserJS()
    {
    ?>

        <!-- Bootstrap core JavaScript-->
        <script src="<?php echo SECURE_PATH; ?>vendor/jquery/jquery.min.js"></script>
        <script src="<?php echo SECURE_PATH; ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="<?php echo SECURE_PATH; ?>vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- bootstrap-datetimepicker JS -->
        <script src="<?php echo SECURE_PATH; ?>vendor/bootstrap-datetimepicker/js/moment.min.js"></script>
        <script src="<?php echo SECURE_PATH; ?>vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>

        <!-- Page level plugin JavaScript-->
        <script src="<?php echo SECURE_PATH; ?>vendor/chart.js/Chart.min.js"></script>
        <script src="<?php echo SECURE_PATH; ?>vendor/datatables/jquery.dataTables.js"></script>
        <script src="<?php echo SECURE_PATH; ?>vendor/datatables/dataTables.bootstrap4.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="<?php echo SECURE_PATH; ?>vendor/app/js/admin.js"></script>

        <!-- Demo scripts for this page-->
        <script src="<?php echo SECURE_PATH; ?>vendor/app/js/custom-js/datatables-demo.js"></script>
        <script src="<?php echo SECURE_PATH; ?>vendor/app/js/custom-js/chart-area-demo.js"></script>
        <script src="<?php echo SECURE_PATH; ?>vendor/app/js/custom-js/to-do-list.js"></script>


        <!-- jQuery Custom Scroller CDN -->
        <script src="<?php echo SECURE_PATH; ?>vendor/jquery-mousewheel-scrollbar/js/jquery.mCustomScrollbar.min.js"></script>
        <script>
            $(function() {
                $('.elog-datepicker').datetimepicker({
                    // format: 'DD-MM-YYYY HH:mm:ss'
                    format: 'DD-MMMM'
                });
            });
        </script>




        <!-- jQuery Custom Scroller CDN -->
        <script src="<?php echo SECURE_PATH; ?>vendor/jquery-mousewheel-scrollbar/js/jquery.mCustomScrollbar.min.js"></script>

        <!--<script src="<?php echo SECURE_PATH; ?>vendor/js/modernizr.min.js"></script>-->
        <script src="<?php echo SECURE_PATH; ?>vendor/js/nprogress.js"></script>
        <script type="text/javascript" src="<?php echo SECURE_PATH; ?>vendor/js/crypto-js/crypto-js.js"></script>
        <script src="<?php echo SECURE_PATH; ?>vendor/js/ajaxfunction.js"></script>
        <!--<script src="<?php echo SECURE_PATH; ?>assets/js/upload/fileuploader.js"></script>-->





        <?php

    }




    function floorToFraction($number, $denominator = 1)
    {
        $x = $number * $denominator;
        $x = floor($x);
        $x = $x / $denominator;
        return $x;
    }

    function ceilToFraction($number, $denominator = 1)
    {
        $original  = $x = $number * $denominator;

        $x = ceil($x);

        $x = $x / $denominator;

        if (($x - $original) >= 0.25) {
            $x = $this->floorToFraction($number, $denominator);
        }

        return $x;
    }



    function activity($log, $user, $display)
    {
        global $database;

        $database->query("INSERT INTO log VALUES(NULL,'" . $log . " - IP:" . $_SERVER['REMOTE_ADDR'] . "','" . $user . "','" . time() . "'," . $display . ",0)");
    }




    function number_to_words($number)
    {


        $no = round($number);
        $point = round($number - $no, 2) * 100;
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            '0' => '', '1' => 'one', '2' => 'two',
            '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
            '7' => 'seven', '8' => 'eight', '9' => 'nine',
            '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
            '13' => 'thirteen', '14' => 'fourteen',
            '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
            '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
            '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
            '60' => 'sixty', '70' => 'seventy',
            '80' => 'eighty', '90' => 'ninety'
        );
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;
            if ($number) {

                $counter = count($str);
                $plural = null; //(($counter = count($str)) && $number > 9) ? 's' : null;


                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str[] = ($number < 21) ? $words[$number] .
                    " " . $digits[$counter] . $plural . " " . $hundred
                    :
                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
            } else $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);
        $points = ($point) ?
            "." . $words[$point / 10] . " " .
            $words[$point = $point % 10] : '';
        return $result;
    }



    // csrf  token generate
    function generateToken($page)
    {


        $_SESSION['rand'] = bin2hex(md5(time()));

        $_SESSION['token'] = hash_hmac('sha256', $page, $_SESSION['rand']);


        return $_SESSION['token'];
    }

    // check token

    function checkToken($token)
    {


        if ($_SESSION['token'] != $token || strlen($token) == 0) {



            $_SESSION['token'] = '';

            $this->logout();

        ?>

            <script>
                alert('Something went wrong. try again..');

                location.href = '<?php echo SECURE_PATH; ?>';
            </script>

        <?php
            exit;
        }
    }

    function phash_equals($str1, $str2)
    {
        //echo $str1."<br>".$str2;exit;
        if (strlen($str1) != strlen($str2)) {
            return false;
        } else {
            $res = $str1 ^ $str2;
            $ret = 0;
            for ($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
            return !$ret;
        }
    }


    function csrfToken($data)
    {


        $random_salt = time(); //openssl_random_pseudo_bytes (rand(16,96));

        if ($this->logged_in)
            $random_salt .= '.' . $this->access_token;

        $newToken = hash_hmac('sha256', $data . '.' . $random_salt, session_id() . $this->username);

        $_SESSION['token'] = $newToken;
        $_SESSION['random_salt'] = $random_salt;


        if ($this->logged_in) {
        ?>
            <span style="display:none;" id="sT"><?php echo $newToken; ?></span>
            <span style="display:none;" id="aT"><?php echo $this->access_token; ?></span>

        <?php

        } else {
        ?>
            <span style="display:none;" id="sT"><?php echo $newToken; ?></span>

        <?php

        }
    }


    function verifyToken($data)
    {

        $headers = getallheaders();

        $token = $headers['X-Authentication'];


        $random_salt = '';
        if (isset($_SESSION['random_salt']))
            $random_salt = $_SESSION['random_salt'];

        $calc = hash_hmac('sha256', $data . '.' . $random_salt, session_id() . $this->username);





        if ($this->phash_equals($calc, $token)) {



            if (!$this->logged_in)
                return true;

            elseif ($this->logged_in) {


                if ($this->validateAccessToken())
                    return true;
                else

                    header("HTTP/1.1 401 Unauthorized");
            }
        } else {
            header("HTTP/1.1 401 Unauthorized");


            return false;
        }
    }

    function generateAccessToken()
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + (60 * 5);  // jwt valid for 60 seconds from the issued time

        $payload = array(
            'username' => $this->username,
            'iat' => $issuedAt,
            'exp' => $expirationTime
        );

        $key = JWT_SECRET;
        $alg = 'HS256';
        return $this->access_token = $_SESSION['access_token'] = JWT::encode($payload, $key, $alg);
    }



    function validateAccessToken()
    {
        $headers = getallheaders();



        if (!isset($headers['X-Access-Token']))
            return false;

        $token = $headers['X-Access-Token'];


        // echo "Access Token: ".$token.'<br />';

        $key = JWT_SECRET;

        try {
            $decode = (array) JWT::decode($token, $key, array('HS256'));


            //  echo "time: ".time();
            //  print_r($decode);


            if ($decode['exp'] < time()   || $decode['username'] != $this->username)
                return false;


            else if ($this->validateRefreshToken()) {
                $this->generateAccessToken();


                return true;
            } else {

                $this->logout();


                return false;
            }
        } catch (Exception $e) {
            //  echo $e->getMessage();

            if ($e->getMessage() == 'Expired token') {
                if ($this->validateRefreshToken()) {
                    $this->generateAccessToken();


                    return true;
                } else {

                    $this->logout();


                    return false;
                }
            }
        }




        return false;
    }


    function generateRefreshToken()
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + (3600 * 12);  // jwt valid for 60 seconds from the issued time

        $payload = array(
            'username' => $this->username,
            'iat' => $issuedAt,
            'exp' => $expirationTime
        );

        $key = JWT_SECRET;
        $alg = 'HS256';
        return $this->refresh_token = $_SESSION['refresh_token'] = JWT::encode($payload, $key, $alg);
    }



    function validateRefreshToken()
    {



        if (!isset($_SESSION['refresh_token']))
            return false;

        $token = $_SESSION['refresh_token'];



        // echo "Refresh Token: ".$token;
        $key = JWT_SECRET;

        try {
            $decode = (array) JWT::decode($token, $key, array('HS256'));

            if ($decode['exp'] < time())
                return false;

            if ($decode['iat'] > (time() + 3600 * 12 - 5 * 60))
                return false;

            if ($decode['username'] != $this->username)
                return false;
        } catch (Exception $e) {
            return false;
        }



        return true;
    }


    function checkUserAccess($folder)
    {
        global $database;

        $userlevel = $database->get_name("nav_items", 'folder', $folder, 'userlevel');

        if (strlen($userlevel) == 0) {
            $userlevel = $database->get_name("sub_nav_items", 'folder', $folder, 'userlevel');
        }

        $userlevels = explode(",", $userlevel);

        //print_r($userlevels);exit;

        /*echo "logged in ".$this->logged_in."<br>";
		echo "Current userlevel ".$this->userlevel."<br>";
		echo "DB userlevel ".implode(",",$userlevels)."<br>";
		echo "Referer ".$_SERVER['HTTP_REFERER']."<br>";*/

        //exit;

        if (!$this->logged_in || !in_array($this->userlevel, $userlevels) || $_SERVER['HTTP_REFERER'] != SECURE_PATH . 'home/') {
        ?>
            <script type="text/javascript">
                window.location = '<?php echo SECURE_PATH; ?>';
                //alert('');
            </script>
<?php
            exit;
        }
    }

    function Pagination($tables, $table_connections, $conditions, $filters, $pagename, $numres)
    {

        global $database;

        $total_pages = $numres;

        /* Setup vars for query. */
        $targetpage = $pagename;     //your file name  (the name of this file)
        //how many items to show per page

        /* Setup page vars for display. */
        if ($page == 0) $page = 1;                    //if no page var is given, default to 1.
        $prev = $page - 1;                            //previous page is page - 1
        $next = $page + 1;                            //next page is page + 1
        $lastpage = ceil($total_pages / $limit);        //lastpage is = total pages / items per page, rounded up.
        $lpm1 = $lastpage - 1;                        //last page minus 1



        /*
        Now we apply our rules and draw the pagination object.
        We're actually saving the code to a variable in case we want to draw it more than once.
        */
        $pagination = "";
        if ($lastpage > 1) {

            $pagination .= "<ul class=\"pagination\">";
            //previous button
            if ($page > 1) {

                $pagination .= " <li class=\"page-item\">
				<a class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $prev . "')\">Previous</a></li>";
            } else {
                $pagination .= "<li class=\"page-item disabled\">
				<a class=\"page-link\">Previous</a></li>";
            }
            //pages
            if ($lastpage < 7 + ($adjacents * 2))    //not enough pages to bother breaking it up
            {
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
                    else
                        $pagination .= "<li class=\"page-item \"><a class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $counter . "')\">$counter</a></li>";
                }
            } elseif ($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some
            {
                //close to beginning; only hide later pages
                if ($page < 1 + ($adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $page)
                            $pagination .= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
                        else
                            $pagination .= "<li class=\"page-item \"><a class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $counter . "')\">$counter</a></li>";
                    }
                    $pagination .= "...";
                    $pagination .= "<li class=\"page-item \"><a class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $lpm1 . "')\">$lpm1</a></li>";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $lastpage . "')\">$lastpage</a></li>";
                }
                //in middle; hide some front and some back
                elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\"  onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=1')\">1</a></li>";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=2')\">2</a></li>";
                    $pagination .= "...";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page)
                            $pagination .= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
                        else
                            $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $counter . "')\">$counter</a></li>";
                    }
                    $pagination .= "...";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\">$lpm1</a></li>";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $lastpage . "')\">$lastpage</a></li>";
                }
                //close to end; only hide early pages
                else {
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\" onclick=\"setStateGet('adminTable','" . $targetpage . "','page=1')\">1</a></li>";
                    $pagination .= "<li class=\"page-item \"><a  class=\"page-link\"  onclick=\"setStateGet('adminTable','" . $targetpage . "','page=2')\">2</a></li>";
                    $pagination .= "...";
                    for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $page)
                            $pagination .= "<li class=\"page-item active\"><a class=\"page-link\" >$counter <span class=\"sr-only\">(current)</span></a></li>";
                        else
                            $pagination .= "<li class=\"page-item active\"><a class=\"page-link\"  onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $counter . "')\">$counter</a></li>";
                    }
                }
            }

            //next button
            if ($page < $counter - 1)
                $pagination .= "<li class=\"page-item active\"><a class=\"page-link\"  onclick=\"setStateGet('adminTable','" . $targetpage . "','tableDisplay=1&page=" . $next . "')\">Next</a></li>";
            else
                $pagination .= "<li class=\"page-item disabled\">
				<a class=\"page-link\">Next</a></li>";
            $pagination .= "</ul>\n";
        }


        return $pagination;
    }
};


/**
 * Initialize session object - This must be initialized before
 * the form object because the form uses session variables,
 * which cannot be accessed unless the session has started.
 */
$session = new Session;

/* Initialize form object */
$form = new Form;

?>