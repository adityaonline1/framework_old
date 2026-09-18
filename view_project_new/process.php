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

/*projects*/
if(isset($_REQUEST['tableDisplayy']))
{
	?>
		<div class="row">
			<div class="col-xl-12 col-lg-12">
				<div class="card border-0 shadow mb-4">
					<div
						class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
						<h6 class="m-0 font-weight-bold text-primary">Projects</h6>

						<a href="<?php echo SECURE_PATH; ?>view_project_new/export_projects.php"
						class="btn btn-sm btn-success">
							<i class="fa fa-download"></i> Export CSV
						</a>
						
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
												<th>Actions</th>
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

														<td>
															<a href="javascript:void(0);"
															onclick="showEditproject(<?php echo (int)$row['id']; ?>);">
																<i class="fa fa-pencil" aria-hidden="true"></i>
															</a>

															<a href="javascript:void(0);"
															onclick="if(confirm('Are you sure you want to delete this project?')) { deleteeprojectdata(<?php echo (int)$row['id']; ?>); }">
																<i class="fa fa-trash" aria-hidden="true"></i>
															</a>
														
														</td>

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
/*projects*/

/*employees*/
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
												<th>Actions</th>
												
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
														<td>
															<a href="javascript:void(0);"
															onclick="showEditEmployee(<?php echo (int)$row['id']; ?>);">
																<i class="fa fa-pencil" aria-hidden="true"></i>
															</a>

															<a href="javascript:void(0);"
															onclick="if(confirm('Are you sure you want to delete this employee?')) { deleteemployeedata(<?php echo (int)$row['id']; ?>); }">
																<i class="fa fa-trash" aria-hidden="true"></i>
															</a>
														
														</td>

													
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
/*employees*/




	
?>


