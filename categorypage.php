<?php
session_start();
include('databaseconnect.php');

$top_categories_sql = "
    SELECT m.MainCategoryID, m.MainCategoryTitle, COUNT(i.idea_id) AS idea_count
    FROM maincategory m
    JOIN subcategory s ON m.MainCategoryID = s.MainCategoryID
    JOIN ideas i ON s.SubCategoryID = i.SubCategoryID
    GROUP BY m.MainCategoryID
    ORDER BY idea_count DESC
    LIMIT 3
";
$top_categories = $connect->query($top_categories_sql);

// Fetch all categories
$all_categories_sql = "SELECT * FROM maincategory";
$all_categories = $connect->query($all_categories_sql);

// Fetch 3 unused categories
$unused_categories_sql = "
    SELECT m.MainCategoryID, m.MainCategoryTitle
    FROM maincategory m
    WHERE m.MainCategoryID NOT IN (
        SELECT DISTINCT s.MainCategoryID
        FROM subcategory s
        JOIN ideas i ON s.SubCategoryID = i.SubCategoryID
    )
    LIMIT 3
";
$unused_categories = $connect->query($unused_categories_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-bottom: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: white; }
        .details-btn { background-color: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; }
        .details-btn:hover { background-color: #218838; }
        .highlight { background-color: #ffeb3b; }
        .unused { background-color: #f8d7da; }
    </style>
</head>
<body>

<h2>Top 3 Most Used Categories</h2>
<?php if ($top_categories && $top_categories->num_rows > 0): ?>
    <table>
        <tr>
            <th>Category ID</th>
            <th>Category Title</th>
            <th>Idea Count</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $top_categories->fetch_assoc()): ?>
        <tr class="highlight">
            <td><?= htmlspecialchars($row['MainCategoryID']) ?></td>
            <td><?= htmlspecialchars($row['MainCategoryTitle']) ?></td>
            <td><?= htmlspecialchars($row['idea_count']) ?></td>
            <td>
                <a href="category.php?category_id=<?= $row['MainCategoryID'] ?>" class="details-btn">Details</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No popular categories found.</p>
<?php endif; ?>

<h2>All Categories</h2>
<?php if ($all_categories && $all_categories->num_rows > 0): ?>
    <table>
        <tr>
            <th>Category ID</th>
            <th>Category Title</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $all_categories->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['MainCategoryID']) ?></td>
            <td><?= htmlspecialchars($row['MainCategoryTitle']) ?></td>
            <td>
                <a href="category_detail.php?category_id=<?= $row['MainCategoryID'] ?>" class="details-btn">Details</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No categories found.</p>
<?php endif; ?>

<h2>3 Unused Categories</h2>
<?php if ($unused_categories && $unused_categories->num_rows > 0): ?>
    <table>
        <tr>
            <th>Category ID</th>
            <th>Category Title</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $unused_categories->fetch_assoc()): ?>
        <tr class="unused">
            <td><?= htmlspecialchars($row['MainCategoryID']) ?></td>
            <td><?= htmlspecialchars($row['MainCategoryTitle']) ?></td>
            <td>
                <a href="category_detail.php?category_id=<?= $row['MainCategoryID'] ?>" class="details-btn">Details</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No unused categories found.</p>
<?php endif; ?>

</body>
</html>

