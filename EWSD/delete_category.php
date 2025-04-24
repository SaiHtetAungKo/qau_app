<?php
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryID = intval($_POST['category_id']);

    // Delete the main category and cascading subcategories
    $deleteQuery = "DELETE FROM maincategory WHERE MainCategoryID = ?";
    $stmt = mysqli_prepare($connection, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $categoryID);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($connection)]);
    }

    mysqli_stmt_close($stmt);
}
?>
