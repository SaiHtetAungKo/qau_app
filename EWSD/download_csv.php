<?php
// Show errors if any
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('connection.php'); // or require_once, both are fine

$connect = new Connect();               // create instance of your connection class
$connection = $connect->getConnection(); // get the actual mysqli connection object

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_csv'])) {
    $query = "
    WITH department_idea_data AS (
      SELECT 
        d.department_id AS department_id,
        d.department_name AS department_name,
        COUNT(i.idea_id) AS total_ideas,
        COUNT(DISTINCT u.user_id) AS total_posters
      FROM departments d
      LEFT JOIN users u ON u.department_id = d.department_id
      LEFT JOIN ideas i ON i.userID = u.user_id
      GROUP BY d.department_id, d.department_name
    ),
    total_idea_count AS (
      SELECT COUNT(*) AS total_ideas FROM ideas
    )
    SELECT 
      did.department_id,
      did.department_name,
      did.total_ideas,
      did.total_posters,
      ROUND(CAST(did.total_ideas AS FLOAT) / tic.total_ideas * 100, 2) AS contribution_percentage
    FROM department_idea_data did
    CROSS JOIN total_idea_count tic
    ORDER BY did.total_ideas DESC;
    ";

    $result = mysqli_query($connection, $query);

    if (!$result) {
        die('Query Failed: ' . mysqli_error($connection));
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="department_report.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Department ID', 'Department Name', 'Total Ideas', 'Total Posters', 'Contribution %']);

    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row['department_id'],
            $row['department_name'],
            $row['total_ideas'],
            $row['total_posters'],
            $row['contribution_percentage']
        ]);
    }

    fclose($output);
    exit;
} else {
    echo "Invalid access.";
}
?>
