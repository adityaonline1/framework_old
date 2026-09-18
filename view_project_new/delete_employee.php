<?php

header('Content-Type: application/json');

include('../include/session.php');


/* =========================
   CHECK EMPLOYEE ID
   ========================= */

if(!isset($_POST['employee_id']) || empty($_POST['employee_id']))
{
    echo json_encode([
        'status' => false,
        'message' => 'Employee ID is required'
    ]);

    exit;
}


$employee_id = (int)$_POST['employee_id'];


try
{
    /* =========================
       DELETE EMPLOYEE
       ========================= */

    $delete = $database->connection->prepare("
        DELETE FROM employees
        WHERE id = :id
    ");

    $delete->execute([
        ':id' => $employee_id
    ]);


    /* =========================
       CHECK DELETE
       ========================= */

    if($delete->rowCount() == 0)
    {
        echo json_encode([
            'status' => false,
            'message' => 'Employee not found'
        ]);

        exit;
    }


    /* =========================
       SUCCESS
       ========================= */

    echo json_encode([
        'status' => true,
        'message' => 'Employee deleted successfully',
        'employee_id' => $employee_id
    ]);
}
catch(PDOException $e)
{
    echo json_encode([
        'status' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
}

exit;

?>