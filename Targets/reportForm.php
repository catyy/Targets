<?php
session_start();
error_reporting(E_ALL);
include 'targetFuncsions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reports</title>
    <link href = "/FlowerPower/Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
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

<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src = "/FlowerPower/Bootstrap/js/bootstrap.min.js"></script>


<!--form action="getSalesReports.php" method="post">
    <label>Employee name:<br>
        <select name='employee[]' multiple='multiple' ><option></option>

          <?php
/*         $employeesToList = array();
       $employeesToList = getEmployees($api);

       foreach($employeesToList as $value){
           $category = htmlspecialchars($value[0]);
           echo "<option value='$value[1],$category'>$category</option>";
       } */
       ?>
        </select><br>

        <br><label>Warehouse name:<br>
            <select name='warehouse[]' multiple='multiple' >
                <option></option>

                <?php
           /*     $warehouseToList = array();
                $warehouseToList = getWarehouses($api);

                foreach ($warehouseToList as $key => $row) {
                    $name[$key]  = $row['name'];
                    $id[$key] = $row['id'];
                }

                array_multisort($name, SORT_ASC, $id, SORT_ASC, $warehouseToList);

                foreach($warehouseToList as $value){
                    $category = htmlspecialchars($value[0]);
                    echo "<option value='$value[1],$category'>$category</option>";
                }*/
                ?>
            </select>

            <br><br>
            <br><label>Date from:
                <br><input type="date" name="date" required><br>
                <input type="radio" name="target" value="week" id="week" checked='checked' ><label for="week" > week targets</label>
                <input type="radio" name="target" value="month" id="month" /><label for="month"> month targets</label><br><br>
                <input type="submit" value="Get Reports"></form>
<br-->

<a href="index.php">Main page</a>
</div>
</body>
</html>