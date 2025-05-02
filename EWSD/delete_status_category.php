<?php
include('connection.php');
$connect = new Connect();
$connection = $connect->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryID = intval($_POST['category_id']);

    $getTitleQuery = "SELECT MainCategoryTitle FROM maincategory WHERE MainCategoryID = ?";
    $getTitleStmt = mysqli_prepare($connection, $getTitleQuery);
    mysqli_stmt_bind_param($getTitleStmt, "i", $categoryID);
    mysqli_stmt_execute($getTitleStmt);
    mysqli_stmt_bind_result($getTitleStmt, $categoryTitle);
    mysqli_stmt_fetch($getTitleStmt);
    mysqli_stmt_close($getTitleStmt);

    $updateQuery = "UPDATE maincategory SET status = 'inactive' WHERE MainCategoryID = ?";
    $stmt = mysqli_prepare($connection, $updateQuery);
    mysqli_stmt_bind_param($stmt, "i", $categoryID);

    if (mysqli_stmt_execute($stmt)) {
        $message = urlencode("$categoryTitle category deactivated successfully.");
    } else {
        $message = urlencode("Failed to deactivate category: " . mysqli_error($connection));
    }

    mysqli_stmt_close($stmt);
    
    header("Location: qa_manager_all_cat_list.php?message=$message");
    exit();
}
?>
