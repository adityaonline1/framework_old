<?php
/**
 * Constants.php
 *
 * This file is intended to group all constants to
 * make it easier for the site administrator to tweak
 * the login script.
 *
 * Written by: Jpmaster77 a.k.a. The Grandmaster of C++ (GMC)
 * Last Updated: August 2, 2009 by Ivan Novak
 */

define("BASEURL", "http://www.example.com/");

/**
 * Database Constants - these constants are required
 * in order for there to be a successful connection
 * to the MySQL database. Make sure the information is
 * correct.
 */
/*
define("DB_SERVER", "localhost");
define("DB_USER", "root");
define("DB_PASS", ""); 
*/
define("DB_SERVER", "localhost");
define("DB_USER", "root");
define("DB_PASS", ""); 

define("DB_NAME", "framework_old");


/**
 * Database Table Constants - these constants
 * hold the names of all the database tables used
 * in the script.
 */
define("TBL_USERS", "users");
define("TBL_ACTIVE_USERS",  "active_users");
define("TBL_ACTIVE_GUESTS", "active_guests");
define("TBL_BANNED_USERS",  "banned_users");
define("TBL_MAIL", "mail");
define("TBL_EMPLOYEES", "employees");
define("TBL_ACTIVE_EMPLOYEES",  "active_employees");

/**
 * Special Names and Level Constants - the admin
 * page will only be accessible to the user with
 * the admin name and also to those users at the
 * admin user level. Feel free to change the names
 * and level constants as you see fit, you may
 * also add additional level specifications.
 * Levels must be digits between 0-9.
 */
define("ADMIN_NAME", "admin");
define("GUEST_NAME", "Guest");
define("ADMIN_LEVEL", 9);
define("AUTHOR_LEVEL", 5);
define("USER_LEVEL",  1);
define("GUEST_LEVEL", 0);

/**
 * This boolean constant controls whether or
 * not the script keeps track of active users
 * and active guests who are visiting the site.
 */
define("TRACK_VISITORS", false);

/**
 * Timeout Constants - these constants refer to
 * the maximum amount of time (in minutes) after
 * their last page fresh that a user and guest
 * are still considered active visitors.
 */
define("USER_TIMEOUT", 10);
define("GUEST_TIMEOUT", 5);

/**
 * Cookie Constants - these are the parameters
 * to the setcookie function call, change them
 * if necessary to fit your website. If you need
 * help, visit www.php.net for more info.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default
define("COOKIE_PATH", "/");  //Avaible in whole domain

/**
 * Email Constants - these specify what goes in
 * the from field in the emails that the script
 * sends to users, and whether to send a
 * welcome email to newly registered users.
 */
define("EMAIL_FROM_NAME", "Ivan Novak");
define("EMAIL_FROM_ADDR", "inovak1@gmail.com");
define("EMAIL_WELCOME", true);

/**
 * This constant forces all users to have
 * lowercase usernames, capital letters are
 * converted automatically.
 */
define("ALL_LOWERCASE", false);

/**
 * This defines the absolute path
 */
define("ABSPATH", dirname(__FILE__).'/');

/**
 * This boolean constant controls wheter or
 * not the user to user mail function is active
 */
define("MAIL", true);


//$serverhost = "http://".$_SERVER['HTTP_HOST']."/mckgc";

$serverhost = "http://".$_SERVER['SERVER_NAME']."/framework_old";

//$serverhost = "http://".$_SERVER['HTTP_HOST']."/mckgc";


define('SECURE_PATH',$serverhost.'/');
define('USER_PATH',$serverhost.'/');
define('NODE_PATH','http://localhost:8100/');
define('CSS_PATH',$serverhost.'/');
define('JS_PATH',$serverhost.'/');


//define('SECURE_PATH','http://'.$_SERVER['HTTP_HOST'].'/');
define('VALID_MONTH','08');

//Scabs Version
define('VERSION','1.0 &alpha;');

define("SMSUSER","rajeev");
define("SMSPASS", "rudraveena");
define("SENDERID" ,"rajeev");

define("BILL_PREFIX","LIO");
define("PRINT_TITLE","Light It Out");

$unit_array = array('Milli Grams'=>'mgm','Grams'=>'gm','Kilograms'=>'kg','Milli Liters'=>'ml','Liters'=>'lt');

$appKey = '8e1r8vr7kw1b0qe';
$appSecret = 'r5ay1od4kpr4z5e';

define('JWT_SECRET', 'r5ay1od4kpr4z5e-secret-8e1r8vr7kw1b0qe');

?>
