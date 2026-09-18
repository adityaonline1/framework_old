<?php
include('include/session.php');
header('Content-Type: application/json');

if (!isset($_POST['project_id']) || empty($_POST['project_id'])) {
    echo json_encode([
        'status' => false,
        'message' => 'Project ID is required'
    ]);
    exit;
}

$product_id = (int) $_POST['project_id'];

try {

    $stmt = $database->connection->prepare("
        SELECT *
        FROM project_details
        WHERE id = :id
        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $product_id
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
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

} catch (PDOException $e) {

    echo json_encode([
        'status' => false,
        'message' => 'Database error',
        'error' => $e->getMessage()
    ]);
}

exit;
?>
