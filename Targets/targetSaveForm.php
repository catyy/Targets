<?php
session_start();
error_reporting(E_ALL);
include 'targetFuncsions.php';
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Save Targets</title>
</head>
<body>
<form action="targetSaveForm.php" method="post">

    <label>Employee name:<br>
        <select name='employee[]' multiple='multiple' ><option></option>

           <?php
            $employeesToList = array();
            $employeesToList = getEmployees($api);
            $_SESSION['empl_Wareh'] = $employeesToList;

            foreach($employeesToList as $value){
                $category = htmlspecialchars($value[0]);
                echo "<option value='$value[1],$category'>$category</option>";
            }
            ?>
        </select><br>

        <br><label>Warehouse name:<br>
            <select name='warehouse[]' multiple='multiple' ><option></option>

                <?php
                $warehouseToList = array();
                $warehouseToList = getWarehouses($api);
                $_SESSION['empl_Wareh'] = $warehouseToList;

                foreach($warehouseToList as $value){
                    $category = htmlspecialchars($value[0]);
                    echo "<option value='$value[1],$category'>$category</option>";
                }
                ?>
            </select>

        <br><br>

        <br><label>Date from:
            <br><input type="date" name="date" required><br><br>

        <br><label>Month target:
            <br><input type="number" name="month">

        <br><label>Week target:
            <br><input type="number" name="week"><br>
            <input type="submit" value="Save target"></form>
<a href="index.php">Main page</a>

</body>
</html>

