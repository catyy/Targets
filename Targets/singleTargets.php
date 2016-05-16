<?php
session_start();
error_reporting(E_ALL);
include 'targetFuncsions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Targets</title>
    <link href = "/FlowerPower/Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
    <script src = "/FlowerPower/Bootstrap/js/bootstrap.min.js"></script>
    <script src="/FlowerPower/Bootstrap/js/jquery-2.2.1.js"></script>
</head>

<body>
<div class="container-fluid"><br>
<h1>Targets</h1>

<ul class="nav nav-tabs">
    <li><a href="index.php">Home</a></li>
    <li class="active"><a href="singleTargets.php">Targets</a></li>

</ul>
<br>

   <form action="targetstable.php" method="post">

       <label>Targets:<br>
            <input type="radio" name="who" value="employee" id="employee" checked='checked' ><label for="employee" > employee &nbsp</label>
            <input type="radio" name="who" value="warehouse" id="warehouse" /><label for="warehouse"> warehouse </label><br>

            <br><label>Date from:
            <br><input type="date" name="date" required><br><br>
               <label>Targets:</p>
            <input type="radio" name="target" value="day" id="day" checked="checked"  /><label for="day"> day &nbsp </label>
            <input type="radio" name="target" value="week" id="week" ><label for="week" > week &nbsp</label>
            <input type="radio" name="target" value="month" id="month" /><label for="month"> month </label></label>
            <br><br>
            <input type="submit" value="Targets"></form><br>

    </div>
</body>
</html>

