<?php

ini_set("display_errors",1);error_reporting(E_ALL);

include('../include/session.php');

if(!$session->logged_in  || ($session->userlevel != 9) || $_SERVER['HTTP_REFERER']!=SECURE_PATH.'home/')
{
    //echo SECURE_PATH;exit;
	?>
	<script type="text/javascript">
		window.location = '<?php echo SECURE_PATH;?>';
	</script>
	<?php 
	exit;
}

 

	?>
	<div class="" id="adminForm">
		<script>
			setState('adminForm','<?php echo SECURE_PATH; ?>add_project_new/process.php','addForm=1');
		</script>
	</div>

	<div class="" id="adminTable">
		<script>
			setState('adminTable','<?php echo SECURE_PATH; ?>add_project_new/process.php','tableDisplay=1');
		</script>
	</div>
	<?php

	
?>
