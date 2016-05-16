
<?php
session_start();
error_reporting(E_ALL);
include "addTargetsFunctions.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Targets</title>
    <link href = "/FlowerPower/Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
    <script src = "/FlowerPower/Bootstrap/js/bootstrap.min.js"></script>
    <script src="/FlowerPower/Bootstrap/js/jquery-2.2.1.js"></script>
</head>

<script>

    $(document).ready(function(){

        //if all month target is entered, then single months disabled
        $("#months").keyup(function(){
            $(".month").prop('disabled', true);
            if(!$.trim($(this).val()).length) {
                $(".month").prop('disabled', false);
            }
        });

        //if all weeks target is entered, then single weeks disabled
        $("#weeks").keyup(function(){
            $(".week").prop('disabled', true);
            if(!$.trim($(this).val()).length) {
                $(".week").prop('disabled', false);
            }
        });

        //if single week is entered, all week input disabled
        $(".week").keyup(function(){
            $("#weeks").prop('disabled', true);
            if(!$.trim($(this).val()).length) {
                $(".weeks").prop('disabled', false);
            }
            var $nonempty = $('.week').filter(function() {
                return this.value != ''
            });

            if ($nonempty.length == 0) {
                $("#weeks").prop('disabled', false);
            }
        });


        //if single month is entered, all month input disabled
        $(".month").keyup(function(){
            $("#months").prop('disabled', true);
            if(!$.trim($(this).val()).length) {
                $(".months").prop('disabled', false);
            }
            var $nonempty = $('.month').filter(function() {
                return this.value != ''
            });

            if ($nonempty.length == 0) {
                $("#months").prop('disabled', false);
            }
        });
        })
</script>
<body>

<div class="container-fluid" ><br>
<h1>Add targets</h1>

<ul class="nav nav-tabs">

    <li><a href="index.php">Home</a></li>
    <li class="active"><a href="addTargets.php">Add targets</a></li>
    <li><a href="uploadTargets.php">Upload day targets</a></li>

</ul><br>
</div>
    <div class="container-fluid" style = "float:left">
    <form class="form-horizontal" method="post">
    <label>Targets:<br>

        <input type="radio" name="whoseTargets" value="employee" id="employee" checked='checked' ><label for="employee" >&nbsp; All employees </label>&nbsp;
        <input type="radio" name="whoseTargets" value="warehouse" id="warehouse" /><label for="warehouse">&nbsp; All warehouses</label></label>

    <br><br>
    <label >Select year/s:<br>

        <select class="selectpicker" multiple name="years[]" required>

 <?php
        $starting_year  = date("Y");
        $ending_year = date('Y', strtotime('+5 year'));

        for($i=$starting_year; $i <= $ending_year; $i++) {?>
            <option value="<?php echo $i;?>" > <?php echo $i;?> </option>';
      <?php  }; ?>

        </select></label><br>

    <label class="control-label" >

        All month targets:
        <input type="number" name="months" id="months"><br>

        All week targets:
        <input type="number" name="weeks" id="weeks"><br>
        <br>

        January:   <input type="number" name="month[]" id="month01" class="month"><br>
        February: <input type="number" name="month[]" id="month02" class="month"><br>
        March:  <input type="number" name="month[]" id="month03" class="month"><br>
        April:  <input type="number" name="month[]" id="month04" class="month"><br>
        May:   <input type="number" name="month[]" id="month05" class="month"><br>
        June:    <input type="number" name="month[]" id="month06" class="month"><br>
        July:   <input type="number" name="month[]" id="month07" class="month"><br>
        August:  <input type="number" name="month[]" id="month08" class="month"><br>
        September:  <input type="number" name="month[]" id="month09" class="month"><br>
        October:  <input type="number" name="month[]" id="month10" class="month"><br>
        November:    <input type="number" name="month[]" id="month11" class="month"><br>
        December:    <input type="number" name="month[]" id="month12" class="month"><br></label>

        <br><br><br>
</div>
<div class="container-fluid" style="float: left;">

            <label class="control-label" >Week targets:<br><br>
                <?php
                for($i=1;$i<54;$i++){
                $week_no = sprintf("%02d", $i);
                ?>
                    <?php echo $week_no;?>: <input type="number" name="week[]" id="week<?php echo $week_no;?>" class="week"><br>
                <?php
                }
                ?>
            </label><br>
</div>

<div class="container-fluid" style="float: left">
<input type="submit"  name="submitAddTargets" value="Save targets">
</form>

<form  method="post">
    <input type="submit" style="margin-top:20px;" value="Clear input">
</form>

</div>

</body>
</html>