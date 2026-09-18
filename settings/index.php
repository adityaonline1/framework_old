<?php
include('../include/session.php');

if(!$session->logged_in){
?>
<script type="text/javascript">
window.location = '<?php echo SECURE_PATH?>mckgc/';
</script>

<?php
}
else{
?>
<div id="adminForm" style="margin-top:2% !important">
	<script type="text/javascript">
        setStateGet('adminForm','<?php echo SECURE_PATH;?>settings/process.php','addForm=2&editform=<?php echo $session->username;?>');
    </script>
</div>

<script type="text/javascript">

function userControl(val){

if(val== '9'){

$('#userfunc').text('Can Edit/Delete: Notifications,Users;View & Download  Files;Dashboard');

$('#politician').slideUp();

}
else if(val == '5'){
$('#userfunc').text('App Login & Usage; Manage Account, Notifications and Files uploaded');
$('#politician').slideDown();
}

else if(val == '0'){$('#userfunc').text('');


	$('#politician').slideUp();
}




}


</script>
<?php
}
?>