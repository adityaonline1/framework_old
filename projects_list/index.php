<?php

ini_set("display_errors",1);error_reporting(E_ALL);

include('../include/session.php');

if(!$session->logged_in  || ($session->userlevel != 9) || $_SERVER['HTTP_REFERER']!=SECURE_PATH.'employee_home/')
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
	
	<!-- <div class="" id="adminTable1">

		<script>
			setState('adminTable1','<?php echo SECURE_PATH; ?>add_project/process.php','tableDisplay=1');
		</script>
	</div> -->


	<div class="" id="adminTable">

	
		<script>
			setState('adminTable','<?php echo SECURE_PATH; ?>projects_list/process.php','tableDisplay=1');
		</script>
	</div>


	<div class="" id="editadminTable" style="display:none">

	

		<!-- <script>
			setState('editadminTable','<?php echo SECURE_PATH; ?>view_project_new/edit_employee.php','editform=1');
		</script> -->
	</div>





	<?php

	
?>
<script>

var selectedEmployeeId = 0;

function showEditEmployee(id)
{
    selectedEmployeeId = id;

    $('#editadminTable').show();

    setState(
        'editadminTable',
        '<?php echo SECURE_PATH; ?>view_project_new/edit_employee.php',
        'editform=1&id=' + id
    );
}



function deleteemployeedata(id){
	//alert(id);

	var employee_id = id;
	$.ajax({
		url:"../view_project_new/delete_employee.php",
		data:{employee_id:employee_id},
		type:"post",
		"success":function(res){
			alert('data deleted successfully');

			window.location.reload();
		}
	})
}

function showEditproject(id)
{
    selectedEmployeeId = id;

    $('#editadminTable').show();

    setState(
        'editadminTable',
        '<?php echo SECURE_PATH; ?>view_project_new/edit_project.php',
        'editform=1&id=' + id
    );
}

function deleteeprojectdata(id){
	//alert(id);

	var employee_id = id;
	$.ajax({
		url:"../view_project_new/delete_project.php",
		data:{employee_id:employee_id},
		type:"post",
		"success":function(res){
			alert('data deleted successfully');

			window.location.reload();
		}
	})
}
</script>