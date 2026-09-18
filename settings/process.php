<?php
include('../include/session.php');
ini_set("display_erros", "on");
error_reporting(E_ALL);
if (!$session->logged_in) {
?>
  <script type="text/javascript">
    setStateGet('main', '<?php echo SECURE_PATH; ?>login_process.php', 'loginForm=1');
  </script>

<?php
}

//Metircs Forms, Tables and Functions
//Display users form
if (isset($_REQUEST['addForm'])) {

  if ($_REQUEST['addForm'] == 2 && isset($_REQUEST['editform'])) {

    $data_sel = $database->connection->prepare("SELECT * FROM users WHERE username = :username");
    $data_sel->execute(array(
      'username' => $_REQUEST['editform']
    ));

    if ($data_sel->rowCount() > 0) {
      $data = $data_sel->fetch(PDO::FETCH_ASSOC);

      $_POST = array_merge($_POST, $data);
    }
  }

?>
  <div class="content-wrapper">
    <!-- Content Area-->
    <section class="content-area">
      <div class="container-fluid">
        <div class="row">
          <div class="col-xl-12 col-lg-12">
            <div class="card border-0 shadow mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">User Settings</h6>
              </div>
              <div class="card-body">
                <form role="form">
                  <div class="form-group">
                    <label for="user">Full Name</label>

                    <input type="text" name="name" placeholder="Full Name" class="form-control" id="name" value="<?php if (isset($_POST['name'])) {
                                                                                                                    echo $_POST['name'];
                                                                                                                  } ?>" />
                    <span class="error"><?php if (isset($_SESSION['error']['name'])) {
                                          echo $_SESSION['error']['name'];
                                        } ?></span>
                  </div>

                  <div class="form-group">
                    <label for="user">Username</label>

                    <input type="text" name="username" disabled="disabled" placeholder="Username" class="form-control" id="username" value="<?php echo $session->userinfo['username']; ?>" />
                    <span class="error"><?php if (isset($_SESSION['error']['username'])) {
                                          echo $_SESSION['error']['username'];
                                        } ?></span>
                  </div>

                  <div class="form-group">
                    <label for="password">Old Password</label>

                    <input type="password" name="o_password" class="form-control" id="o_password" value="<?php if (isset($_POST['o_password'])) {
                                                                                                            echo $_POST['o_password'];
                                                                                                          } ?>" />
                    <span class="error"><?php if (isset($_SESSION['error']['o_password'])) {
                                          echo $_SESSION['error']['o_password'];
                                        } ?></span>
                  </div>

                  <div class="form-group">
                    <label for="password">New Password</label>

                    <input type="password" name="password" class="form-control" id="password" value="" />
                    <span class="error"><?php if (isset($_SESSION['error']['password'])) {
                                          echo $_SESSION['error']['password'];
                                        } ?></span>
                  </div>
                  <div class="form-group">
                    <label for="c_password">Confirm Password</label>

                    <input type="password" name="c_password" class="form-control" id="c_password" value="" />
                    <span class="error"><?php if (isset($_SESSION['error']['c_password'])) {
                                          echo $_SESSION['error']['c_password'];
                                        } ?></span>
                  </div>

                  <div class="form-group">
                    <label for="email">Email</label>

                    <input type="text" name="email" placeholder="Email ID" class="form-control" id="email" value="<?php if (isset($_POST['email'])) {
                                                                                                                    echo $_POST['email'];
                                                                                                                  } ?>" />
                    <span class="error"><?php if (isset($_SESSION['error']['email'])) {
                                          echo $_SESSION['error']['email'];
                                        } ?></span>
                  </div>


                  <div class="form-group">
                    <label for="mobile">Mobile</label>
                    <div class="input-group">
                      <span class="input-group-addon">+91</span>
                      <input type="text" name="mobile" placeholder="10-digit Mobile number" class="form-control" id="mobile" value="<?php if (isset($_POST['mobile'])) {
                                                                                                                                      echo $_POST['mobile'];
                                                                                                                                    } ?>" />

                    </div>
                    <span class="error"><?php if (isset($_SESSION['error']['mobile'])) {
                                          echo $_SESSION['error']['mobile'];
                                        } ?></span>
                  </div>


















                  <div class="form-group">



                    <input type="button" class="btn btn-info" onClick="setState('adminForm','<?php echo SECURE_PATH; ?>settings/process.php','validateForm=1&name='+$('#name').val()+'&o_password='+$('#o_password').val()+'&c_password='+$('#c_password').val()+'&password='+$('#password').val()+'&email='+$('#email').val()+'&mobile='+$('#mobile').val()+'<?php if (isset($_POST['editform'])) {
                                                                                                                                                                                                                                                                                                                                                                echo '&editform=' . $_POST['editform'];
                                                                                                                                                                                                                                                                                                                                                              } ?>')" value="Save" />
                  </div>

                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>



  <?php
  unset($_SESSION['error']);
}



//Process and Validate POST data
if (isset($_POST['validateForm'])) {
  $_SESSION['error'] = array();

  $post = $session->cleanInput($_POST);

  $id = 'NULL';
  $mobile = $post['mobile'];
  $email = $post['email'];
  $name = $post['name'];

  if (isset($post['editform'])) {
    $id = $post['editform'];
  }




  $field = 'mobile';
  if (!$mobile || strlen(trim($mobile)) == 0) {
    $_SESSION['error'][$field] = "* Mobile cannot be empty";
  } else if (strlen($mobile) < 10) {
    $_SESSION['error'][$field] = "* Mobile number below 10 digits";
  } else if (strlen($mobile) > 10) {
    $_SESSION['error'][$field] = "* Mobile number above 10 digits";
  }
  /* mobile number check */ else if (!preg_match("~^([7-8-9]{1}[0-9]{9})+$~", $mobile)) {
    $_SESSION['error'][$field] = "* Invalid Mobile number";
  }


  $field = 'email';
  if (!$email || strlen(trim($email)) == 0) {
    $_SESSION['error'][$field] = "* Email cannot be empty";
  } elseif (strlen($email) > 0) {
    /* Check if valid email address */
    $regex = "~^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
      . "@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
      . "\.([a-z]{2,}){1}$~";

    $email = stripslashes($email);
    if (!preg_match($regex, $email)) {
      $_SESSION['error'][$field] = "* Invalid Email ID";
    }
  }

  $ck = $database->connection->prepare("SELECT password FROM users WHERE username=:username");
  $ck->execute(array('username' => $session->userinfo['username']));
  $ck = $ck->fetch(PDO::FETCH_ASSOC);
  $ck = $ck['password'];

  $field = 'password';
  if (!$post['password'] || strlen(trim($post['password'])) == 0) {
    $_SESSION['error'][$field] = "* Password cannot be empty";
  }

  if (strlen($post["password"]) <= '8') {
    $_SESSION['error'][$field] = "* Password Must Contain At Least 8 Digits !";
  } elseif (!preg_match("#[0-9]+#", $post["password"])) {
    $_SESSION['error'][$field] = "* Password Must Contain At Least 1 Number !";
  } elseif (!preg_match("#[A-Z]+#", $post["password"])) {
    $_SESSION['error'][$field] = "* Password Must Contain At Least 1 Capital Letter !";
  } elseif (!preg_match("#[a-z]+#", $post["password"])) {
    $_SESSION['error'][$field] = "* Password Must Contain At Least 1 Lowercase Letter !";
  } elseif (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $post["password"])) {
    $_SESSION['error'][$field] = "* Password Must Contain At Least 1 Special Character !";
  } else if (md5($post['password']) == $ck) {
    $_SESSION['error'][$field] = "* New Password cannot be old one";
  }

  $field = 'o_password';
  if (!$post['o_password'] || strlen(trim($post['o_password'])) == 0) {
    $_SESSION['error'][$field] = "* Old Password cannot be empty";
  } else {

    if (md5($post['o_password']) != $ck) {
      echo md5($post['o_password']);
      $_SESSION['error'][$field] = "* Old Password doesnt match";
    }
  }

  $field = 'c_password';
  if (!$post['c_password'] || strlen(trim($post['c_password'])) == 0) {
    $_SESSION['error'][$field] = "*Confirm Password cannot be empty";
  } else if (strlen(trim($post['c_password'])) > 0) {
    if ($post['c_password'] != $post['password'])
      $_SESSION['error'][$field] = "* Passwords do not match";
  }



  if (strlen(trim($name)) == 0) {
    $name = $post['username'];
  }
  //exit;
  //Check if any errors exist	
  if (count($_SESSION['error']) > 0 || $post['validateForm'] == 2) {
  ?>
    <script type="text/javascript">
      $('#adminForm').slideDown();

      setState('adminForm', '<?php echo SECURE_PATH; ?>settings/process.php', 'addForm=1&o_password=<?php echo $post['o_password']; ?>&name=<?php echo $post['name']; ?>&password=<?php echo $post['password']; ?>&email=<?php echo $post['email']; ?>&mobile=<?php echo $post['mobile']; ?><?php if (isset($_POST['editform'])) {
                                                                                                                                                                                                                                                                                        echo '&editform=' . $post['editform'];
                                                                                                                                                                                                                                                                                      } ?>')
    </script>

  <?php
  } else {

    //$database->query("UPDATE users SET password = '".md5($post['password'])."', name = '".$name."', email = '".$post['email']."', mobile = '".$post['mobile']."' WHERE username = '".$post['username']."'");

    $up = $database->connection->prepare("UPDATE users SET password = :password, name = :name, email = :email, mobile = :mobile,updated=1 WHERE username = :username");
    $up->execute(array(
      'password' => md5($post['password']),
      'name' => $post['name'],
      'email' => $post['email'],
      'mobile' => $post['mobile'],
      'username' => $session->userinfo['username'],

    ));



  ?>

    <div class="alert alert-success"><i class="fa fa-thumbs-up fa-2x"></i> Your Settings Updated Successfully!</div>


<?php
  }
}


?>