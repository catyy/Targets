<?php
session_start();
error_reporting(E_ALL);
include 'targetFuncsions.php';
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Employee reports</title>
        <script src="/FlowerPower/Bootstrap/js/jquery-2.2.1.js"></script>
        <link href = "/FlowerPower/Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
        <script src = "/FlowerPower/Bootstrap/js/bootstrap.min.js"></script>

    </head>

    <body>
    <div class="container-fluid"><br>
    <h1>Reports</h1>
    <ul class="nav nav-tabs">
        <li><a href="index.php">Home</a></li>
        <li class="active"><a href="employeesReportForm.php">Employees</a></li>
        <li><a href="warehouseReportForm.php">Warehouses</a></li>
    </ul>
    <br>

    <form action="getSalesReports.php" method="post">
        <label>Employee name:<br>
            <select style="height: 190px;"name='employee[]' multiple='multiple' ><option></option>

                <?php
                $employeesToList = array();
                $employeesToList = getEmployees($api);
                $_SESSION['e_wData'] = $employeesToList;

                foreach($employeesToList as $value){

                    $name = htmlspecialchars($value['name']);
                    $id = htmlspecialchars($value['id']);
                    echo "<option value='$id'>$name</option>";
                }
                ?>
            </select><br>

                <br><label>Date from:
                    <br><input type="date" name="date" required><br><br>
                Targets:<br>
                    <input type="radio" name="time" value="day" id="day" checked='checked' ><label for="day" > day </label>
                    <input type="radio" name="time" value="week" id="week" ><label for="week" > week </label>
                    <input type="radio" name="time" value="month" id="month" /><label for="month"> month </label><br><br>
                    <input type="hidden" name="emp_ware" value="employee">
                    <input type="submit" value="Get Reports"></form>
    <br><br>

</div>
    </body>
    </html>