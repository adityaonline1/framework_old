<?php

header('Content-Type: application/json');

include('../include/session.php');

if(!isset($_POST['employee_id']) || empty($_POST['employee_id']))
{
    echo json_encode([
        'status' => false,
        'message' => 'Project ID is required'
    ]);
    exit;
}

$employee_id = (int)$_POST['employee_id'];

try
{
    $get = $database->connection->prepare("
        SELECT *
        FROM project_details
        WHERE id = :id
        LIMIT 1
    ");

    $get->execute([
        ':id' => $employee_id
    ]);

    $row = $get->fetch(PDO::FETCH_ASSOC);

    if(!$row)
    {
        echo json_encode([
            'status' => false,
            'message' => 'Project not found'
        ]);
        exit;
    }

    echo json_encode([
        'status' => true,
        'data' => $row
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
