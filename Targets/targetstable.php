<?php
session_start();
error_reporting(E_ALL);
include("targetsTableFunctions.php");

$selected_who =$_POST['who'];
$rangeTargets = null;
$targets = array();
$date = null;


if($selected_who == 'warehouse'){
    $date = warehouseInfo($api,null);
    $_SESSION['tempTargets'] = $_SESSION['sessionTargets'];

}elseif($selected_who == 'employee'){
    $date = employeeInfo($api,null);
    $_SESSION['tempTargets'] = $_SESSION['sessionTargets'];
}


//not clicked next or previous
if($_POST['getNext'] == null && $_POST['getPrevious'] == null ){
//
//    print "<pre>";
//    print_r($date);
//    print "</pre>";
//

    $rangeTargets = rangeTargets($date,$_SESSION['sessionTargets']);

    if( $_SESSION['week_month']=='month'){
        $showTargets = singleMonthTarget($rangeTargets);
    }elseif($_SESSION['week_month']=='week'){
        $showTargets = singleWeekTarget($rangeTargets);
    }elseif($_SESSION['week_month']=='day'){
        $showTargets = singleDayTarget($rangeTargets);
    }

//click button previous
}else if(isset($_POST['getPrevious']) && $_POST['getPrevious'] == "Previous") {

    $date = $_POST['prevDate'];
    $rangeTargets = $_SESSION['tempTargets'];

    if( $_SESSION['week_month']=='month'){
        $showTargets = getPreviousNextMonth($date,$_POST['getPrevious'] );
        $date = $showTargets[0]['date'];

    }elseif($_SESSION['week_month']=='week'){
        $showTargets = getPreviousNextWeek($date,$_POST['getPrevious'] );

        foreach($showTargets as $value){
            $date = $value['date'];
            break;
        }

    }elseif($_SESSION['week_month']=='day'){

        $showTargets = getPreviousNextDay($date,$_POST['getPrevious'] );

        foreach($showTargets as $value){
            $date = $value['date'];
            break;
        }

    }


//click button next
}else if (isset($_POST['getNext'])  && $_POST['getNext'] == "Next"){

    $date = $_POST['nextDate'];
    $rangeTargets = $_SESSION['tempTargets'];

    if( $_SESSION['week_month']=='month'){

        $showTargets = getPreviousNextMonth($date,$_POST['getNext'] );
        $date = $showTargets[0]['date'];

    }elseif($_SESSION['week_month']=='week'){

        $showTargets = getPreviousNextWeek($date,$_POST['getNext'] );

        foreach($showTargets as $value){
            $date = $value['date'];
            break;
        }

    }elseif($_SESSION['week_month']=='day'){

        $showTargets = getPreviousNextDay($date,$_POST['getNext'] );

        foreach($showTargets as $value){
            $date = $value['date'];
            break;
        }
    }


}



if(isset($_POST['updateTarget'])  && $_POST['updateTarget'] == "Update"){

// -----------------  PEale update ei suuna enam jäänud kohale tagasi---- GET ?
//    print "<pre>";
//    print_r($week_month);
//    print "</pre>";
//
//    print "<pre>";
//    print_r($input);
//    print "</pre>";
//
//    $input = $_POST['saveTarget'];
//    $inputDate = $_POST['saveDate'];
//
//    print "<pre>";
//    print_r($week_month);
//    print "</pre>";
//
//    print "<pre>";
//    print_r($input);
//    print "</pre>";

    updateTarget($input,$inputDate,$_SESSION['warehouse_employee'],$api);

    $rangeTargets = rangeTargets($date,$_SESSION['w_m']);
    $showTargets = singleMonthTarget($rangeTargets);

}


?>
<!DOCTYPE html>
<html>

<head>
    <title>Targets</title>
    <script src="/FlowerPower/Bootstrap/js/jquery-2.2.1.js"></script>
    <link href = "/FlowerPower/Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
    <script src = "/FlowerPower/Bootstrap/js/bootstrap.min.js"></script>

</head>

<body>
<div class="container-fluid">
    <br>

<h1>Targets</h1>

<ul class="nav nav-tabs">
    <li><a href="index.php">Home</a></li>
    <li><a href="singleTargets.php">Pick target</a></li>
    <li  class="active"><a href="targetstable.php">Targets</a></li>
</ul>
<br>

<h2><?php

    if( $_SESSION['week_month']=='month'){
        echo "Month: ".getMonth($date)."&nbsp;".getYear($date);
    }elseif( $_SESSION['week_month']=='day'){
        echo "Day: ".getDay($date)."&nbsp;".getMonth($date)."&nbsp;".getYear($date);
    }else{
        $startEnd = getStartAndEndDate(getWeek($date), getYear($date));
        $startD = date('d', strtotime($startEnd['week_start']));
        $endD = date('d', strtotime($startEnd['week_end']));
        $startM = date('m', strtotime($startEnd['week_start']));
        $endM = date('m', strtotime($startEnd['week_end']));
        $startY = date('Y', strtotime($startEnd['week_start']));
        $endY = date('Y', strtotime($startEnd['week_end']));

        echo "Week nr:&nbsp".getWeek($date)."&nbsp;&nbsp;&nbsp(".$startD.".".$startM.".".$startY."-".$endD.".".$endM.".".$endY.")";
    }
     ?></h2>

<table class="table table-striped table-condensed" border="1" style="width:25%">
    <br>

    <form method="post">
        <input type="hidden" name="prevDate" value="<?php echo $date ?>" >
        <input type="submit" name="getPrevious" value="Previous" >
    </form>

    <form method="post">
        <input type="hidden" name="nextDate" value="<?php echo $date ?>" >
        <input type="submit" style="margin-left:18.5%" name="getNext" value="Next">
    </form>
    <br>
    <br>
    <form method="post">
        <tr>
            <th>Warehouse</th>
            <th>Target</th>
        </tr>
    <tr>
        <?php foreach($showTargets as $st){?>
    <tr>
        <td><?php echo  htmlentities($st['name']) ?></td>
        <td><input type="number" name="saveTarget[<?php echo $st['id']?>]" min="0" value="<?php if($st['target'] == null){echo 0;}else{echo $st['target'];}?>"></td>
    </tr>
    <?php }?>
</table>
    <input type="hidden" name="saveDate" value="<?php echo $date ?>" >
    <input type="submit" name="updateTarget" value="Update"></form>
<br>
    </div>

</body>
</html>

