<?php


include('../include/session.php');

ini_set("display_errors","on");error_reporting(E_ALL);

//echo $_REQUEST['id'];exit;


if(!$session->logged_in  || ($session->userlevel != 9) || $_SERVER['HTTP_REFERER']!=SECURE_PATH.'home/')
{
	?>
	<script type="text/javascript">
		window.location = '<?php echo SECURE_PATH;?>';
	</script>
	<?php
	exit;
}

if(isset($_REQUEST['editform']))
{

	?>
	<div class="row">
		<div class="col-xl-12 col-lg-12">
			<div class="card border-0 shadow mb-4">
				<div
					class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					<h6 class="m-0 font-weight-bold text-primary">Edit Project</h6>
				</div>
				<div class="card-body">
					<form id="form">
						<div class="row">
							<div class="col-lg-6 col-md-12">
								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Name</label>
									<div class="col-sm-8">
										<input type="text" id="name" name="name" data-type="man" data-alias="Name" class="form-control" maxlength="30" value="<?php if(isset($_REQUEST['name'])) { echo $_REQUEST['name'];  } ?>">
										<span style="color:red"><?php if(isset($_SESSION['error']['name'])) { echo $_SESSION['error']['name']; } ?></span>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Description</label>
									<div class="col-sm-8">
										<textarea type="text" id="desc" name="desc" data-type="man" data-alias="Description" rows="4" class="form-control"><?php echo $form->value("desc"); ?><?php if(isset($_REQUEST['desc'])) { echo $_REQUEST['desc'];  } ?></textarea>
										<span style="color:red"><?php if(isset($_SESSION['error']['desc'])) { echo $_SESSION['error']['desc']; } ?></span>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Short
										Name</label>
									<div class="col-sm-8">
										<input type="text" id="short_name" name="short_name"  data-type="man" data-alias="Short Name" class="form-control" value="<?php if(isset($_REQUEST['short_name'])) { echo $_REQUEST['short_name'];  } ?>">
										<span style="color:red"><?php if(isset($_SESSION['error']['short_name'])) { echo $_SESSION['error']['short_name']; } ?></span>
									</div>

								</div>
								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Duration(In
										days)</label>
									<div class="col-sm-8">
										<input type="text" data-type="man" id="duration" name="duration" data-alias="Duration" class="form-control" value="<?php if(isset($_REQUEST['duration'])) { echo $_REQUEST['duration'];  } ?>">
										<span style="color:red"><?php if(isset($_SESSION['error']['duration'])) { echo $_SESSION['error']['duration']; } ?></span>
									</div>

								</div>
								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Project
										Manager</label>
									<div class="col-sm-8">
										<select class="custom-select" data-type="man" data-alias="Project Manager"  id="pm" name="pm">
											<option value="">select</option>
											<option value="1" <?php if(isset($_REQUEST['pm'])) { if($_REQUEST['pm']==1) { echo 'selected'; }  } ?>>Project-1</option>
											<option value="2" <?php if(isset($_REQUEST['pm'])) { if($_REQUEST['pm']==2) { echo 'selected'; }  } ?>>Project-2</option>
											<option value="3" <?php if(isset($_REQUEST['pm'])) { if($_REQUEST['pm']==3) { echo 'selected'; }  } ?>>Project-3</option>
											<option value="4" <?php if(isset($_REQUEST['pm'])) { if($_REQUEST['pm']==4) { echo 'selected'; }  } ?>>Project-4</option>
											<option value="5" <?php if(isset($_REQUEST['pm'])) { if($_REQUEST['pm']==5) { echo 'selected'; }  } ?>>Project-5</option>
										</select>
										<span style="color:red"><?php if(isset($_SESSION['error']['pm'])) { echo $_SESSION['error']['pm']; } ?></span>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Client</label>
									<div class="col-sm-8">
										<select class="custom-select" data-type="man" data-alias="Client" id="client" name="client">
											<option value="">select</option>
											<option value="1" <?php if(isset($_REQUEST['client'])) { if($_REQUEST['client']==1) { echo 'selected'; }  } ?>>Client-1</option>
											<option value="2" <?php if(isset($_REQUEST['client'])) { if($_REQUEST['client']==2) { echo 'selected'; }  } ?>>Client-2</option>
											<option value="3" <?php if(isset($_REQUEST['client'])) { if($_REQUEST['client']==3) { echo 'selected'; }  } ?>>Client-3</option>
											<option value="4" <?php if(isset($_REQUEST['client'])) { if($_REQUEST['client']==4) { echo 'selected'; }  } ?>>Client-4</option>
											<option value="5" <?php if(isset($_REQUEST['client'])) { if($_REQUEST['client']==5) { echo 'selected'; }  } ?>>Client-5</option>
										</select>
										<span style="color:red"><?php if(isset($_SESSION['error']['client'])) { echo $_SESSION['error']['client']; } ?></span>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-12">
								<div class="form-group row">
									<label class="col-sm-4 col-form-label">SignUp Date</label>
									<div class="col-sm-8">
										<input type="date" data-type="man" data-alias="SignUp Date" id="sudate" name="sudate" value="<?php echo $form->value("sudate"); ?>" class="form-control datepicker1">
										<span style="color:red"><?php if(isset($_SESSION['error']['sudate'])) { echo $_SESSION['error']['sudate']; } ?></span>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-4 col-form-label">Start Date</label>
									<div class="col-sm-8">
										<input type="date" data-type="man" data-alias="Start Date" id="stdate" name="stdate" class="form-control datepicker2" value="<?php echo $form->value("sudate"); ?>">
										<span style="color:red"><?php if(isset($_SESSION['error']['stdate'])) { echo $_SESSION['error']['stdate']; } ?></span>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-4 col-form-label">SignOff Date</label>
									<div class="col-sm-8">
										<input type="date" data-type="man" data-alias="SignOff Date" id="sodate" name="sodate" class="form-control datepicker3" value="<?php echo $form->value("sudate"); ?>">
										<span style="color:red"><?php if(isset($_SESSION['error']['sodate'])) { echo $_SESSION['error']['sodate']; } ?></span>
									</div>
								</div>

                                <input type="hidden" name="id" id="id" value="<?=$_REQUEST['id']?>">

								<div class="form-group row">
									<div class="offset-sm-4 col-sm-8">
										<button class="btn btn-md btn-primary"
        type="button"
        onclick="setState(
            'editadminTable',
            '<?php echo SECURE_PATH; ?>view_project_new/edit_project.php',
            'editform=1' +
            '&id=' + <?php echo (int)$_REQUEST['id']; ?> +
            '&validate=1' +
            '&name=' + encodeURIComponent($('#name').val()) +
            '&desc=' + encodeURIComponent($('#desc').val()) +
            '&short_name=' + encodeURIComponent($('#short_name').val()) +
            '&duration=' + encodeURIComponent($('#duration').val()) +
            '&pm=' + encodeURIComponent($('#pm').val()) +
            '&client=' + encodeURIComponent($('#client').val()) +
            '&sudate=' + encodeURIComponent($('#sudate').val()) +
            '&stdate=' + encodeURIComponent($('#stdate').val())
        );">
    Edit Project
</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php

	unset($_SESSION['error']);

}



if(isset($_REQUEST['validate']))
{
    $_SESSION['error'] = array();

    $_REQUEST = $session->cleanInput($_REQUEST);

	//print_r($_REQUEST);exit;
    /* =========================
       VALIDATION
       ========================= */

    if(!isset($_REQUEST['name']) || strlen(trim($_REQUEST['name'])) == 0)
    {
        $_SESSION['error']['name'] = "*Name is mandatory";
    }

    if(!isset($_REQUEST['sudate']) || strlen(trim($_REQUEST['sudate'])) == 0)
    {
        $_SESSION['error']['sudate'] = "*Sign Up date is mandatory";
    }

    if(!isset($_REQUEST['stdate']) || strlen(trim($_REQUEST['stdate'])) == 0)
    {
        $_SESSION['error']['stdate'] = "*Start Date is mandatory";
    }

    if(!isset($_REQUEST['id']) || empty($_REQUEST['id']))
    {
        $_SESSION['error']['id'] = "*Employee ID is required";
    }


    /* =========================
       IF VALIDATION ERROR
       ========================= */

    if(count($_SESSION['error']) > 0)
    {
        ?>

        <script type="text/javascript">

        setState(
            'editadminTable',
            '<?php echo SECURE_PATH;?>view_project_new/edit_project.php',
            'editform=1' +
            '&name=' + encodeURIComponent($('#name').val()) +
            '&desc=' + encodeURIComponent($('#desc').val()) +
            '&short_name=' + encodeURIComponent($('#short_name').val()) +
            '&duration=' + encodeURIComponent($('#duration').val()) +
            '&pm=' + encodeURIComponent($('#pm').val()) +
            '&client=' + encodeURIComponent($('#client').val()) +
            '&sudate=' + encodeURIComponent($('#sudate').val()) +
            '&stdate=' + encodeURIComponent($('#stdate').val())
            '&id=<?php echo (int)$_REQUEST['id']; ?>'
        );

        </script>

        <?php
    }


    /* =========================
       UPDATE EMPLOYEE
       ========================= */

    else
    {
        $id = (int)$_REQUEST['id'];

        $update = $database->connection->prepare("
            UPDATE project_details
            SET
                name = :name,
                description = :desc,
                short_name = :short_name,
                duration = :duration,
                pm = :pm,
                client = :client,
                sudate = :sudate,
                stdate = :stdate
            WHERE id = :id
        ");

        $result = $update->execute(array(
            ':name'          => $_REQUEST['name'],
            ':desc'        => $_REQUEST['desc'],
            ':short_name'         => $_REQUEST['short_name'],
            ':duration'           => $_REQUEST['duration'],
            ':pm' => $_REQUEST['pm'],
            ':client' => $_REQUEST['client'],
            ':sudate' => $_REQUEST['sudate'],
            ':stdate' => $_REQUEST['stdate'],
            ':id'            => $id
        ));


        /* =========================
           SUCCESS
           ========================= */

        if($result)
        {
            ?>

            <script type="text/javascript">

                alert('Project Updated Successfully');

                $('#editadminTable').hide();

                $('#adminTable').show();

                setState(
                    'adminTable',
                    '<?php echo SECURE_PATH;?>view_project_new/process.php',
                    'tableDisplayy=1'
                );

            </script>

            <?php
        }
    }
}



	
?>

<script>
	$(document).ready(function(){
		//alert(<?=$_REQUEST['id']?>);

		var employee_id = "<?=$_REQUEST['id']?>";
		$.ajax({
			url:"../view_project_new/get_project.php",
			data:{employee_id:employee_id},
			type:"post",
			"success":function(res){
				//const response = JSON.parse(res);
				console.log(res.data.name);

				$('#name').val(res.data.name);

				$('#desc').val(res.data.description);

				$('#short_name').val(res.data.short_name);

				$('#duration').val(res.data.duration);

				$('#pm').val(res.data.pm);

                $('#client').val(res.data.client);

                $('#sudate').val(res.data.sudate);

                $('#stdate').val(res.data.stdate);

                //$('#sodate').val(res.data.sodate);
			}
		})
	});
	</script>