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
					<h6 class="m-0 font-weight-bold text-primary">Add Project</h6>
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

								<div class="form-group row">
									<div class="offset-sm-4 col-sm-8">
										<button class="btn btn-md btn-primary" type="button" onClick="setState('adminForm','<?php echo SECURE_PATH; ?>add_project/process.php','validateForm=1&name='+$('#name').val()+'&sudate='+$('#sudate').val()+'&desc='+$('#desc').val()+'&stdate='+$('#stdate').val()+'&short_name='+$('#short_name').val()+'&duration='+$('#duration').val()+'&pm='+$('#pm').val()+'&client='+$('#client').val())">
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

	if(isset($_REQUEST["sudate"]) && strlen($_REQUEST["sudate"])==0)
	{
		$_SESSION['error']['sudate']="*signup date is mandatory";
	}

	if(isset($_REQUEST["stdate"]) && strlen($_REQUEST["stdate"])==0)
	{
		$_SESSION['error']['stdate']="*Signoff date is mandatory";
	}

	if(isset($_REQUEST["duration"]) && strlen($_REQUEST["duration"])==0)
	{
		$_SESSION['error']['stdate']="*duration is mandatory";
	}
	

	//print_r($form);



	//Check if any errors exist
	if(count($_SESSION['error']) > 0 || $_REQUEST['validateForm'] == 2)
	{
		?>
		<script type="text/javascript">

		setState('adminForm','<?php echo SECURE_PATH;?>add_project/process.php','addForm=1&name=<?php echo $_REQUEST['name']; ?>&sudate=<?php echo $_REQUEST['sudate']; ?>&desc=<?php echo $_REQUEST['desc']; ?>&stdate=<?php echo $_REQUEST['stdate']; ?>&short_name=<?php echo $_REQUEST['short_name']; ?>&duration=<?php echo $_REQUEST['duration']; ?>&pm=<?php echo $_REQUEST['pm']; ?>&client=<?php echo $_REQUEST['client']; ?><?php if(isset($_REQUEST['editform'])){ echo '&editform='.$post['editform'];}?>');

		</script>
		<?php
	}
	else
	{
		//insertion

		$ins=$database->connection->prepare("INSERT INTO project_details VALUES (NULL,:name,:sudate,:description,:stdate,:short_name,:duration,:pm,:client,:timestamp)");
		$ins->execute(array(
			'name'=>$_REQUEST['name'],
			'sudate'=>$_REQUEST['sudate'],
			'description'=>$_REQUEST['desc'],
			'stdate'=>$_REQUEST['stdate'],
			'short_name'=>$_REQUEST['short_name'],
			'duration'=>$_REQUEST['duration'],
			'pm'=>$_REQUEST['pm'],
			'client'=>$_REQUEST['client'],
			'timestamp'=>time()
		));

		if($ins)
		{
			?>
			<script>
				alert('Data Saved Successfully');
				setState('adminForm','<?php echo SECURE_PATH; ?>add_project/process.php','addForm=1');
				setState('adminTable','<?php echo SECURE_PATH; ?>add_project/process.php','tableDisplay=1');
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
						<h6 class="m-0 font-weight-bold text-primary">Projects</h6>
					</div>
					<div class="card-body">
						<?php
							$get=$database->connection->prepare("SELECT * FROM project_details ORDER BY id");
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
												<th>Signup Date</th>
												<th>Description</th>
												<th>Start Date</th>
												<th>Short Name</th>
												<th>Duration</th>
												<th>Project Manager</th>
												<th>Client</th>
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
														<td><?php echo $row['sudate']; ?></td>
														<td><?php echo $row['description']; ?></td>
														<td><?php echo $row['stdate']; ?></td>
														<td><?php echo $row['short_name']; ?></td>
														<td><?php echo $row['duration']; ?></td>
														<td><?php echo $row['pm']; ?></td>
														<td><?php echo $row['client']; ?></td>
														<td><?php echo date('d-m-Y',$row['timestamp']); ?></td>
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