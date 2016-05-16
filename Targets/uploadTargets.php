<?php
session_start();
error_reporting(E_ALL);
include "uploadDayTargetsFunctions.php";

//column check!!!!!


if(isset($_POST['submit'])){

    $_SESSION['warehouse_employee'] = $_POST['whoseTargets'];

    // Check for errors
    if($_FILES['file_upload']['error'] > 0){
        die('<br> An error ocurred when uploading.');
    }

    // Check filetype
    if($_FILES['file_upload']['type'] != 'text/csv'){
        die('<br> Unsupported filetype uploaded.');
    }

    // Check filesize
    if($_FILES['file_upload']['size'] > 500000){
        die('<br> File uploaded exceeds maximum upload size.');
    }

    // Check if the file exists
    if(file_exists('uploads/' . $_FILES['file_upload']['name'])){
        die('<br> File with that name already exists.');
    }

    $dir = "uploads";
    if( is_dir($dir) === false ) {
        mkdir($dir,0777,true);
    }

    // Upload file
    if(!move_uploaded_file($_FILES['file_upload']['tmp_name'], "uploads/" . $_FILES['file_upload']['name'])){
        die('<br> Error uploading file - check destination is writeable.' );
    }

    $csvFile ="uploads/" . $_FILES['file_upload']['name'];
    $myfile = fopen($csvFile, "r") or die("Unable to open file!");

    $data = array();
    while(!feof($myfile)) {
        $data[] = fgetcsv($myfile);
    }

    fclose($myfile);

    if($_SESSION['warehouse_employee'] == 'employee'){
        $_SESSION['empl_Wareh'] = getEmployees($api);
    }else{
        $_SESSION['empl_Wareh'] = getWarehouses($api);
    }

    //saves all employees day targets over
    saveUploadedDayTargets($data,$api);

    echo '<br> File uploaded successfully!';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Targets</title>
    <link href = "/FlowerPower/Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
    <script src = "/FlowerPower/Bootstrap/js/bootstrap.min.js"></script>
    <script src="/FlowerPower/Bootstrap/js/jquery-2.2.1.js"></script>
</head>

<body>
<div class="container-fluid"><br>

    <h1>Mark all targets</h1>
    <ul class="nav nav-tabs">
        <li><a href="index.php">Home</a></li>
        <li  ><a href="addTargets.php">Add targets</a></li>
        <li class="active" ><a href="uploadTargets.php">Upload day targets</a></li>
    </ul><br>

    <form enctype="multipart/form-data" method="POST">

        <input type="radio" name="whoseTargets" value="employee" id="employee" checked='checked' ><label for="employee" >&nbsp; All employees </label>&nbsp;
        <input type="radio" name="whoseTargets" value="warehouse" id="warehouse" /><label for="warehouse">&nbsp; All warehouses</label></label><br><br>

    Send this file:<br>
        <input name="file_upload" type="file" data-allowed-file-extensions='["csv"]' >
        <br><br>
        <input type="submit" name="submit" value="Upload File" >
    </form><br>

   <h3>CSV file example:</h3> <br>

    <div style="color:red">Please do not insert column titles!</div><br>
    Columns:  Day,  Month,  Year, Target <br><br>
    <img src="examplecsv.png" alt="file example" style="width:400px;height:228px;">

</div>
</body>
</html>
