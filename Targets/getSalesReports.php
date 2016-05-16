<?php
session_start();
error_reporting(E_ALL);
include("targetFuncsions.php");


$e_wData = array();

$_SESSION['reportW_E'] = $_POST['emp_ware'];

if($_SESSION['reportW_E'] == "employee"){
    $all_W_E = $_SESSION['e_wData'];
    $selected_W_E = $_POST['employee'];
    $_SESSION['apigetE_W'] = apigetEmployees($api);
}else{
    $all_W_E = $_SESSION['e_wData'];
    $selected_W_E =  $_POST['warehouse'];
    $_SESSION['apigetE_W'] = apigetWarehouses($api);
}



if($_POST['next'] ==null && $_POST['prev']==null ){

    $e_w = $_SESSION['reportW_E']; //employee or warehouse
    $d_w_m = $_POST['time'];
    $_SESSION['time'] = $d_w_m;
    $date = $_POST['date'];

    if ($e_w == "employee") {

        $selected_W_E = w_e_arrayMaker($all_W_E,$selected_W_E);
        $showReport = getEmployeeSaleReport($d_w_m, $date, $selected_W_E,$api);
        $apiGet = $_SESSION['apigetE_W'];
        $attributes = getEmployeeAttributes($apiGet,$d_w_m,$all_W_E);

//        echo "attributes";
//        print "<pre>";
//        print_r($selected_W_E);
//        print "</pre>";

        if($d_w_m == "day"){
            echo "day";

            echo $date;

        print "<pre>";
        print_r($selected_W_E);
        print "</pre>";

            $sortData = filterMonth($attributes,$date);
            $sortData = singleDayTarget($sortData,$date,$selected_W_E);

        }elseif($d_w_m == "week"){
            $sortData = singleWeekTarget($attributes);
        }else{
            $sortData = singleMonthTarget($attributes,$date,$selected_W_E);
        }

        $showData = joinSaleTarget($sortData,$showReport);
//        print "<pre>";
//        print_r($showData);
//        print "</pre>";

    }else {

        $showReport = getWarehouseSaleReport($selected_radio, $date, $months, $warehouse,$api);
        $attributes = getWarehouseAttributes($_SESSION['apigetE_W'],$selected_radio,$months);
    }

}



if(isset($_POST['prev'])) {

    $warehouse =$_SESSION['sessWarehouse'];
    $employee =  $_SESSION['sessEmployee'];
    $selected_radio = $_SESSION['sessSelected_radio'];
    $showReport = array();
    $date = $_POST['newDate'];

    if ($selected_radio == 'month') {
        $date = date("Y-m-d", strtotime($date . " -1 MONTH"));
    } elseif ($selected_radio == 'week') {
        $date = date("Y-m-d", strtotime($date . " -1 WEEK"));
    }

    if($warehouse == null){
        $_SESSION['sessionEmployee'] =$employee;
        $showReport = getEmployeeSaleReport($selected_radio, $date, $months, $employee,$api);
        $attributes = getEmployeeAttributes($outputE,$selected_radio,$months);
    }else{
        $showReport = getWarehouseSaleReport($selected_radio, $date, $months, $warehouse,$api);
        $attributes = getWarehouseAttributes($outputW,$selected_radio,$months);
    }
}

if (isset($_POST['next'])) {

    $warehouse = $_SESSION['sessWarehouse'];
    $employee = $_SESSION['sessEmployee'];
    $selected_radio = $_SESSION['sessSelected_radio'];
    $showReport = array();
    $date = $_POST['newDate'];

    if ($selected_radio == 'month') {
        $date = date("Y-m-d", strtotime($date . " +1 MONTH"));
    } elseif ($selected_radio == 'week') {
        $date = date("Y-m-d", strtotime($date . " +1 WEEK"));
    }

    if ($warehouse == null) {
        $_SESSION['sessionEmployee'] =$employee;
        $showReport = getEmployeeSaleReport($selected_radio, $date, $months, $employee, $api);
        $attributes = getEmployeeAttributes($outputE,$selected_radio,$months);
    } else {
        $showReport = getWarehouseSaleReport($selected_radio, $date, $months, $warehouse, $api);
        $attributes = getWarehouseAttributes($outputW,$selected_radio,$months);
    }
}


if(isset($_POST['top'])){

    $sort = $_POST['sort'];
    $sortData = $_SESSION['sessionSort'];

    foreach ($sortData as $key => $row) {
        $name[$key]  = $row['name'];
        $sale[$key] = $row['sale'];
        $target[$key]  = $row['target'];
        $percent[$key] = $row['percent'];
        $percent[$key] = $row['barpercent'];
    }

    if($sort == 'Alfa'){
        array_multisort($name, SORT_ASC, $sale, SORT_ASC, $sortData); //$target, $percent,$percent

    }elseif($sort == 'Neto'){
        array_multisort($sale, SORT_DESC, $name, SORT_ASC, $sortData);
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reports</title>
    <script src="/FlowerPower/Bootstrap/js/jquery-2.2.1.js"></script>
    <link href = "/FlowerPower/Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
    <script src = "/FlowerPower/Bootstrap/js/bootstrap.min.js"></script>
    <style>
        progress {
            color: #0063a6;
            font-size: .6em;
            line-height: 1.5em;
            text-indent: .5em;
            width: 15em;
            height: 1.8em;
            border: 1px solid #0063a6;
            background: #fff;
        }

        progress::-webkit-progress-bar {
            background: #C80000;
        }
        progress::-webkit-progress-value {
            background: #00FF00;
        }

    </style>
</head>

<body>
<div class="container-fluid">
    <br>

    <h1>Report</h1>
    <ul class="nav nav-tabs">
        <li><a href="index.php">Home</a></li>
        <li  class="active"><a href="targetstable.php">Targets</a></li>
    </ul>
    <br>

<h2><?php

    if(  $_SESSION['time'] =='month'){
        echo "Month: ".getMonth($date)."&nbsp;".getYear($date);
    }elseif( $_SESSION['time'] =='day'){

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
    <script src="/FlowerPower/Bootstrap/js/jquery-2.2.1.js"></script>
    <link href = "/FlowerPower/Bootstrap/css/bootstrap.min.css" rel = "stylesheet">
    <script src = "/FlowerPower/Bootstrap/js/bootstrap.min.js"></script>

</head>

<table class="table table-striped table-condensed" border="1" style="width:40%">
    <br>
    <form method="post">
        <input type="hidden" name="newDate" value="<?php echo $date; ?>">
        <input type="submit" name="prev" value="Previous" >
    </form>

    <form method="post">
        <select class="form-group" style="margin-left:11.5%" name="sort">
            <option value="">Select...</option>
            <option name="sort" value="Alfa">alphabetical order</option>
            <option name="sort" value="Neto">neto sale</option>
        </select>
        <input type="submit" style="margin-left:1%" name="top" value="Sort" >
    </form>

    <form method="post">
        <input type="hidden" name="newDate" value="<?php echo $date; ?>">
        <input style="margin-left:11.5%" type="submit" name="next" value="Next">
    </form>

    <tr>
        <th><?php if($_SESSION['sessWarehouse'] != null){
                echo "Warehouse";
            }else{
                echo "Employee";
            };?></th>
        <th>Net Sales</th>
        <th>Target</th>
        <th>%</th>
    </tr>
    <tr>
        <?php

        if(isset($_POST['top'])){

            foreach($showData as $data){?>
            <tr>
                <td><?php echo htmlspecialchars($data['name']) ?></td>
                <td><?php echo $data['net_sale'] ?></td>
                <td><?php echo $data['target']?></td>
                <td><?php echo $data['percent']."&nbsp;";?>
                    <progress value="<?php echo $data['percentBar'];?>" max="100"></progress>
                </td>
            </tr>
      <?php  }
        }else{
          foreach($showData as $data){?>
              <tr>
                  <td><?php echo htmlspecialchars($data['name']) ?></td>
                  <td><?php echo $data['net_sale'] ?></td>
                  <td><?php echo $data['target'] ?></td>
                  <td><?php echo $data['percent']."&nbsp;";?>
                      <progress value="<?php echo $data['percentBar'];?>" max="100"></progress>
                      <?php $dataForSort[] = array('name' => $data['name'], 'sale' => $data['sale'], 'target' => $data['target'], 'percent' => $data['percent'], 'percentBar' => $data['percentBar']); ?>
                  </td>
              </tr>
      <?php } $_SESSION['sessionSort'] =$dataForSort; }?>

</table><br>

    <br><br>

</div>
</body>
</html>



