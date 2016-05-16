<?php
session_start();
error_reporting(E_ALL);
include 'targetFuncsions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Warehouse reports</title>
    <link href = "/FlowerPower/Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
</head>

<body>
<div class="container-fluid"><br>
<h1>Reports</h1>
<ul class="nav nav-tabs">
    <li><a href="index.php">Home</a></li>
    <li><a href="employeesReportForm.php">Employees</a></li>
    <li class="active"><a href="warehouseReportForm.php">Warehouses</a></li>
</ul>
<br>

<script src = "/FlowerPower/Bootstrap/js/bootstrap.min.js"></script>

<form action="getSalesReports.php" method="post">

        <label>Warehouse name:<br>
            <select name='warehouse[]' multiple='multiple' >
                <option></option>

                <?php
                $warehouseToList = array();
                $warehouseToList = getWarehouses($api);

                foreach ($warehouseToList as $key => $row) {
                    $name[$key]  = $row['name'];
                    $id[$key] = $row['id'];
                }

                array_multisort($name, SORT_ASC, $id, SORT_ASC, $warehouseToList);

                foreach($warehouseToList as $value){
                    $category = htmlspecialchars($value[0]);
                    echo "<option value='$value[1],$category'>$category</option>";
                }
                ?>
            </select>

            <br>
            <br><label>Date from:
                <br><input type="date" name="date" required><br><br>
                <input type="radio" name="target" value="week" id="week" checked='checked' ><label for="week" > week targets</label>
                <input type="radio" name="target" value="month" id="month" /><label for="month"> month targets</label><br><br>
                <input type="submit" value="Get Reports"></form>
<br><br>

    </div>
</body>
</html>