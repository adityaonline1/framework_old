<?php

include('../include/session.php');

/* =========================
   AUTHORIZATION
   ========================= */

if(!$session->logged_in || $session->userlevel != 9)
{
    header('Location: ' . SECURE_PATH);
    exit;
}

/* =========================
   CSV DOWNLOAD
   ========================= */

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=projects_' . date('Y-m-d') . '.csv');
header('Pragma: no-cache');
header('Expires: 0');

/* =========================
   OPEN OUTPUT
   ========================= */

$output = fopen('php://output', 'w');

/* =========================
   CSV HEADER
   ========================= */

fputcsv($output, array(
    'S.No',
    'Name',
    'Signup Date',
    'Description',
    'Start Date',
    'Short Name',
    'Duration',
    'Project Manager',
    'Client',
    'Created At'
));

/* =========================
   GET PROJECTS
   ========================= */

$get = $database->connection->prepare("
    SELECT
        id,
        name,
        sudate,
        description,
        stdate,
        short_name,
        duration,
        pm,
        client,
        timestamp
    FROM project_details
    ORDER BY id
");

$get->execute();

/* =========================
   WRITE CSV DATA
   ========================= */

$sno = 1;

while($row = $get->fetch(PDO::FETCH_ASSOC))
{
    fputcsv($output, array(
        $sno++,
        $row['name'],
        $row['sudate'],
        $row['description'],
        $row['stdate'],
        $row['short_name'],
        $row['duration'],
        $row['pm'],
        $row['client'],
        date('d-m-Y', $row['timestamp'])
    ));
}

fclose($output);
exit;

?>