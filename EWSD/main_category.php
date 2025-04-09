<?php
session_start();
include('connection.php');
if(isset($_POST['btnSave'])) 
{
	$MainCategoryTitle=$_POST['MainCategoryTitle'];
	$Description=$_POST['Description'];
	$Status=$_POST['Status'];
	
	$query="SELECT * FROM MainCategory
			WHERE MainCategoryTitle='$txtloantypename' ";
	$ret=mysqli_query($connect,$query);
	$count=mysqli_num_rows($ret);

	if ($count > 0) 
	{
		echo "<script>window.alert('loan type already exist !');</script>";
		echo "<script>window.location='addloan.php'</script>";
	}
	else
	{
		$Insert="INSERT INTO MainCategory 
			 (MainCategoryTitle,Description,Status)
			 VALUES 
			 ('$MainCategoryTitle','$Description','$Status')";
		$ret=mysqli_query($connect,$Insert);
	}
	
	if($ret) 
	{
		echo "<script>window.alert('Successfully Saved!');</script>";
	}
	else
	{
		echo "<p>Something went wrong : " . mysqli_error($connect) . "</p>";
	}

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <label for="categoryname">category name</label>
    <input type="text" name="categoryname">
    <br>
    <label for="description">description</label>
    <input type="text" name="description">
    <br>
	<label for="status">status</label>
    <input type="text" name="status">
	<br>
    <input type="sumbit" name="btnsub" value="submit">
</body>
</html>