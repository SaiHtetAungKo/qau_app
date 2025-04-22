<?php
session_start();
include('connection.php');

if (!isset($_GET['category_id']) || empty($_GET['category_id'])) {
    die("Invalid category ID.");
}

$category_id = intval($_GET['category_id']);


$category_sql = "SELECT MainCategoryTitle FROM maincategory WHERE MainCategoryID = $category_id";
$category_result = $connect->query($category_sql);
$category_row = $category_result->fetch_assoc();
$category_name = $category_row['MainCategoryTitle'] ?? 'Unknown Category';

$sql = "SELECT 
        ideas.idea_id, 
        ideas.title AS IdeaTitle, 
        ideas.description AS IdeaDescription, 
        departments.department_name, 
        subcategory.SubCategoryTitle
    FROM ideas
    INNER JOIN subcategory ON ideas.SubCategoryID = subcategory.SubCategoryID
    INNER JOIN maincategory ON subcategory.MainCategoryID = maincategory.MainCategoryID
    INNER JOIN departments ON ideas.requestIdea_id = departments.department_id
    WHERE maincategory.MainCategoryID = $category_id
    ORDER BY departments.department_name, ideas.title
";

$result = $connect->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideas for <?= htmlspecialchars($category_name) ?></title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>

<h2>Ideas under "<?= htmlspecialchars($category_name) ?>"</h2>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Idea ID</th>
            <th>Idea Title</th>
            <th>Description</th>
            <th>Department</th>
            <th>Sub Category</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['idea_id']) ?></td>
            <td><?= htmlspecialchars($row['IdeaTitle']) ?></td>
            <td><?= htmlspecialchars($row['IdeaDescription']) ?></td>
            <td><?= htmlspecialchars($row['department_name']) ?></td>
            <td><?= htmlspecialchars($row['SubCategoryTitle']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No ideas found for this category.</p>
<?php endif; ?>

<a href="categorypage.php">Back to Categories</a>

</body>
</html>
