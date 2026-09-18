<?php
include('include/session.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    
	<title>Admin</title>
	<?php $session->commonUserCSS(); ?>
	<?php $session->commonUserJS(); ?>
</head>

<body onLoad="$('#user').focus();" class="login-body">


<div id="loginForm">
<?php
if($session->logged_in){

?>

<script type="text/javascript">
//navigate('<?php echo SECURE_PATH;?>home/','home');
window.location = '<?php echo SECURE_PATH;?>home/';

</script>
<?php	
}
else{
?>
<script type="text/javascript">
setStateGet('loginForm','<?php echo SECURE_PATH;?>login_process.php','loginForm=1');
</script>


<?php

}
?>
     
 <script type="text/javascript">
function addslashes(str) {

str  = encodeURIComponent(str);


  return (str + '')
    .replace(/[\\"']/g, '\\$&')
    .replace(/\u0000/g, '\\0');
}
	

</script>    
     
</div>


</body>
</html>