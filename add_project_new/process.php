<?php


include('../include/session.php');

ini_set("display_errors","on");error_reporting(E_ALL);


if(!$session->logged_in  || ($session->userlevel != 9) || $_SERVER['HTTP_REFERER']!=SECURE_PATH.'home/')
{
	?>
	<script type="text/javascript">
		window.location = '<?php echo SECURE_PATH;?>';
	</script>
	<?php
	exit;
}

if(isset($_REQUEST['addForm']))
{

	?>
	<div class="row">
		<div class="col-xl-12 col-lg-12">
			<div class="card border-0 shadow mb-4">
				<div
					class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
					<h6 class="m-0 font-weight-bold text-primary">Add Employee</h6>
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
                                <div class="form-group row">
									<div class="offset-sm-4 col-sm-8">
										<button class="btn btn-md btn-primary" type="button" onClick="setState('adminForm','<?php echo SECURE_PATH; ?>add_project_new/process.php','validateForm=1&name='+$('#name').val()+'&mobile='+$('#mobile').val()+'&email='+$('#email').val()+'&age='+$('#age').val()+'&qualification='+$('#qualification').val())">
											Submit</button>
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

if(isset($_REQUEST['validateForm']))
{
	$_SESSION['error']=array();
	
	$_REQUEST=$session->cleanInput($_REQUEST);

	if(isset($_REQUEST["name"]) && strlen($_REQUEST["name"])==0)
	{
		$_SESSION['error']['name']="*Name is mandatory";
	}

	if(isset($_REQUEST["mobile"]) && strlen($_REQUEST["mobile"])==0)
	{
		$_SESSION['error']['mobile']="*Mobile is mandatory";
	}

	if(isset($_REQUEST["email"]) && strlen($_REQUEST["email"])==0)
	{
		$_SESSION['error']['email']="*Email is mandatory";
	}
	

	//print_r($form);



	//Check if any errors exist
	if(count($_SESSION['error']) > 0 || $_REQUEST['validateForm'] == 2)
	{
		?>
		<script type="text/javascript">

		setState('adminForm','<?php echo SECURE_PATH;?>add_project_new/process.php','addForm=1&name=<?php echo $_REQUEST['name']; ?>&mobile=<?php echo $_REQUEST['mobile']; ?>&email=<?php echo $_REQUEST['email']; ?>&age=<?php echo $_REQUEST['age']; ?>&qualification=<?php echo $_REQUEST['qualification'];?><?php if(isset($_REQUEST['editform'])){ echo '&editform='.$post['editform'];}?>');

		</script>
		<?php
	}
	else
	{
		//insertion

		$ins=$database->connection->prepare("INSERT INTO employees VALUES (null,:name,:mobile,:email,:age,:qualification,:created_on)");
		$ins->execute(array(
			'name'=>$_REQUEST['name'],
			'mobile'=>$_REQUEST['mobile'],
			'email'=>$_REQUEST['email'],
			'age'=>$_REQUEST['age'],
			'qualification'=>$_REQUEST['qualification'],
			'created_on'=>date('Y-m-d H:i:s')
		));

		if($ins)
		{
			?>
			<script>
				alert('Data Saved Successfully');
				setState('adminForm','<?php echo SECURE_PATH; ?>add_project_new/process.php','addForm=1');
				setState('adminTable','<?php echo SECURE_PATH; ?>add_project_new/process.php','tableDisplay=1');
			</script>
			<?php
		}

	}
}

if(isset($_REQUEST['tableDisplay']))
{
	?>
		<div class="row">
			<div class="col-xl-12 col-lg-12">
				<div class="card border-0 shadow mb-4">
					<div
						class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
						<h6 class="m-0 font-weight-bold text-primary">Employees</h6>
					</div>
					<div class="card-body">
						<?php
							$get=$database->connection->prepare("SELECT * FROM employees ORDER BY id");
							$get->execute();
							if($get->rowCount())
							{
								?>
								<div class="table-responsive">
									<table class="table table-bordered table-condensed">
										<thead>
											<tr>
												<th>S.No</th>
												<th>Name</th>
												<th>Mobile</th>
												<th>Email</th>
												<th>Age</th>
												<th>Qualification</th>
												<th>Created at</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$sno=1;
												while($row=$get->fetch(PDO::FETCH_ASSOC))
												{
													?>
													<tr>
														<td><?php echo $sno++; ?></td>
														<td><?php echo $row['name']; ?></td>
														<td><?php echo $row['mobile']; ?></td>
														<td><?php echo $row['email']; ?></td>
														<td><?php echo $row['age']; ?></td>
														<td><?php echo $row['qualification']; ?></td>
														<td><?php echo date('d-m-Y',strtotime($row['created_on'])); ?></td>
													</tr>
													<?php
												}
											?>
										</tbody>
									</table>
								</div>
								<?php
							}
						?>
					</div>
				</div>
			</div>
		</div>
	<?php
}



	
?>