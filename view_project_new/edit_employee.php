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
					<h6 class="m-0 font-weight-bold text-primary">Edit Employee</h6>
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
									<label class="col-sm-4 col-form-label">Mobile</label>
									<div class="col-sm-8">
										<input type="text" id="mobile" name="mobile" data-type="man" data-alias="Mobile" class="form-control" maxlength="10" value="<?php if(isset($_REQUEST['mobile'])) { echo $_REQUEST['mobile'];  } ?>">
										<span style="color:red"><?php if(isset($_SESSION['error']['mobile'])) { echo $_SESSION['error']['mobile']; } ?></span>
									</div>
								</div>

                                <div class="form-group row">
									<label class="col-sm-4 col-form-label">Email</label>
									<div class="col-sm-8">
										<input type="email" id="email" name="email" data-type="man" data-alias="Email" class="form-control" maxlength="50" value="<?php if(isset($_REQUEST['email'])) { echo $_REQUEST['email'];  } ?>">
										<span style="color:red"><?php if(isset($_SESSION['error']['email'])) { echo $_SESSION['error']['email']; } ?></span>
									</div>
								</div>

                                <div class="form-group row">
									<label class="col-sm-4 col-form-label">Age</label>
									<div class="col-sm-8">
										<input type="tel" id="age" name="age" data-type="man" data-alias="Age" class="form-control" maxlength="3" value="<?php if(isset($_REQUEST['age'])) { echo $_REQUEST['age'];  } ?>">
										<span style="color:red"><?php if(isset($_SESSION['error']['age'])) { echo $_SESSION['error']['age']; } ?></span>
									</div>
								</div>

                                <div class="form-group row">
									<label class="col-sm-4 col-form-label">Qualification</label>
									<div class="col-sm-8">
										<input type="text" id="qualification" name="qualification" data-type="man" data-alias="Qualification" class="form-control" maxlength="10" value="<?php if(isset($_REQUEST['qualification'])) { echo $_REQUEST['qualification'];  } ?>">
										<span style="color:red"><?php if(isset($_SESSION['error']['qualification'])) { echo $_SESSION['error']['qualification']; } ?></span>
									</div>
								</div>

								<input type="hidden" name="id" id="id" value="<?=$_REQUEST['id']?>">
								
                                <div class="form-group row">
									<div class="offset-sm-4 col-sm-8">
										<button class="btn btn-md btn-primary"
        type="button"
        onclick="setState(
            'editadminTable',
            '<?php echo SECURE_PATH; ?>view_project_new/edit_employee.php',
            'editform=1' +
            '&id=' + <?php echo (int)$_REQUEST['id']; ?> +
            '&validate=1' +
            '&name=' + encodeURIComponent($('#name').val()) +
            '&mobile=' + encodeURIComponent($('#mobile').val()) +
            '&email=' + encodeURIComponent($('#email').val()) +
            '&age=' + encodeURIComponent($('#age').val()) +
            '&qualification=' + encodeURIComponent($('#qualification').val())
        );">
    Edit Employee
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

    if(!isset($_REQUEST['mobile']) || strlen(trim($_REQUEST['mobile'])) == 0)
    {
        $_SESSION['error']['mobile'] = "*Mobile is mandatory";
    }

    if(!isset($_REQUEST['email']) || strlen(trim($_REQUEST['email'])) == 0)
    {
        $_SESSION['error']['email'] = "*Email is mandatory";
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
            '<?php echo SECURE_PATH;?>view_project_new/edit_employee.php',
            'editform=1' +
            '&name=<?php echo urlencode($_REQUEST['name']); ?>' +
            '&mobile=<?php echo urlencode($_REQUEST['mobile']); ?>' +
            '&email=<?php echo urlencode($_REQUEST['email']); ?>' +
            '&age=<?php echo urlencode($_REQUEST['age']); ?>' +
            '&qualification=<?php echo urlencode($_REQUEST['qualification']); ?>' +
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
            UPDATE employees
            SET
                name = :name,
                mobile = :mobile,
                email = :email,
                age = :age,
                qualification = :qualification
            WHERE id = :id
        ");

        $result = $update->execute(array(
            ':name'          => $_REQUEST['name'],
            ':mobile'        => $_REQUEST['mobile'],
            ':email'         => $_REQUEST['email'],
            ':age'           => $_REQUEST['age'],
            ':qualification' => $_REQUEST['qualification'],
            ':id'            => $id
        ));


        /* =========================
           SUCCESS
           ========================= */

        if($result)
        {
            ?>

            <script type="text/javascript">

                alert('Employee Updated Successfully');

                $('#editadminTable').hide();

                $('#adminTable').show();

                setState(
                    'adminTable',
                    '<?php echo SECURE_PATH;?>view_project_new/process.php',
                    'tableDisplay=1'
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
			url:"../view_project_new/get_employee.php",
			data:{employee_id:employee_id},
			type:"post",
			"success":function(res){
				//const response = JSON.parse(res);
				console.log(res.data.name);

				$('#name').val(res.data.name);

				$('#mobile').val(res.data.mobile);

				$('#email').val(res.data.email);

				$('#age').val(res.data.age);

				$('#qualification').val(res.data.qualification);
			}
		})
	});
	</script>