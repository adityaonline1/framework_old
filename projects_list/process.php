<?php


include('../include/session.php');

//print_r($_SESSION);exit;

ini_set("display_errors","on");error_reporting(E_ALL);


if(!$session->logged_in  || ($session->userlevel != 9) || $_SERVER['HTTP_REFERER']!=SECURE_PATH.'employee_home/')
{
	?>
	<script type="text/javascript">
		window.location = '<?php echo SECURE_PATH;?>';
	</script>
	<?php
	exit;
}


if(isset($_REQUEST['tableDisplay']))
{
    ?>

    <div class="row">
        <div class="col-xl-12 col-lg-12">

            <div class="card border-0 shadow mb-4">

                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        My Projects
                    </h6>
                </div>

                <div class="card-body">

                    <?php

                    /* -------------------------------------------------
                       Get logged-in employee
                       ------------------------------------------------- */

                    $username = $_SESSION['username'];

                    $employee = $database->connection->prepare("
                        SELECT id, name, mobile
                        FROM employees
                        WHERE name = :name
                        LIMIT 1
                    ");

                    $employee->execute(array(
                        ':name' => $username
                    ));

                    $employeeData = $employee->fetch(PDO::FETCH_ASSOC);


                    if($employeeData)
                    {
                        $employeeId = $employeeData['id'];
                        $mobile     = $employeeData['mobile'];


                        /* -------------------------------------------------
                           Get projects assigned to this employee
                           ------------------------------------------------- */

                        $get = $database->connection->prepare("
                            SELECT
                                pd.*
                            FROM employee_projects ep

                            INNER JOIN project_details pd
                                ON pd.id = ep.project_id

                            WHERE ep.employee_id = :employee_id

                            ORDER BY pd.id
                        ");

                        $get->execute(array(
                            ':employee_id' => $employeeId
                        ));


                        if($get->rowCount())
                        {
                            ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-condensed">

                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Project Name</th>
                                            <th>Short Name</th>
                                            <th>Description</th>
                                            <th>Duration</th>
                                            <th>Project Manager</th>
                                            <th>Client</th>
                                            <th>Start Date</th>
                                            <th>Signup Date</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php

                                        $sno = 1;

                                        while($row = $get->fetch(PDO::FETCH_ASSOC))
                                        {
                                            ?>

                                            <tr>
                                                <td>
                                                    <?php echo $sno++; ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($row['name']); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($row['short_name']); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($row['description']); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($row['duration']); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($row['pm']); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($row['client']); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($row['stdate']); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($row['sudate']); ?>
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
                        else
                        {
                            ?>

                            <div class="alert alert-info">
                                No projects are assigned to you.
                            </div>

                            <?php
                        }
                    }
                    else
                    {
                        ?>

                        <div class="alert alert-danger">
                            Employee record not found.
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