<?php
session_start();
error_reporting(E_ALL);
include "targetFuncsions.php";

$targets = array();

//warehouse week/month attributes
function warehouseInfo($api,$date=null,$week_month=null){

    $_SESSION['warehouse_employee'] = 'warehouse';
    $_SESSION['apiEmpl_Wareh'] = apigetWarehouses($api);
    $_SESSION['empl_Wareh'] = getWarehouses($api);

    if($date == null)$date = $_POST['date'];
    if($week_month == null)$week_month = $_POST['target'];

    $_SESSION['$date'] = $date;
    $_SESSION['week_month'] = $week_month;
    $targets = getWarehouseAttributes($_SESSION['apiEmpl_Wareh'],$week_month);
    $_SESSION['sessionTargets'] = $targets;

    return $date;
}


//employee week/month attributes
function employeeInfo($api,$date=null,$week_month=null){

    $_SESSION['warehouse_employee'] = 'employee';
    $_SESSION['apiEmpl_Wareh'] = apigetEmployees($api);
    $_SESSION['empl_Wareh'] = getEmployees($api);

    if($date == null)$date = $_POST['date'];
    if($week_month == null)$week_month = $_POST['target'];

    echo $week_month;
    $_SESSION['$date'] = $date;
    $_SESSION['week_month'] = $week_month;
    $targets = getEmployeeAttributes($_SESSION['apiEmpl_Wareh'],$week_month);
    $_SESSION['sessionTargets'] = $targets;

    return $date;
}



//get current year/week month targets
function rangeTargets($date,$targets){

    $objects =  $_SESSION['empl_Wareh'];
    $month = getMonth($date);
    $year = getYear($date);
    $showTargets = array();

    if($_SESSION['week_month'] == "month"){

        //make array, key - id
        foreach($objects as $v){
            $showTargets[$v['id']] = array("id"=>$v['id'], "date" => $date,"name"=>$v['name'],"year"=>$year,"target"=>"");
        }

        foreach($targets as $value){
            if($value['selected'] == "month" && $value['year'] == $year ) {
                $showTargets[$value['id']] = array("id"=>$value['id'], "date" => $date, "name"=>$value['name'], "year"=>$year, "target"=>$value["value"]);
            }
        }

    }elseif($_SESSION['week_month'] == "week"){

        foreach($targets as $value){
            if($value['selected'] == "week" && $value['year'] == $year) {
                $showTargets[] = array("id"=>$value['id'], "date" => $date, "name"=>$value['name'], "year"=>$year, "target"=>$value["value"]);
            }
        }
        if($showTargets == null ){
            foreach($objects as $v){
                $showTargets[] = array("id"=>$v['id'], "date" => $date,"name"=>$v['name'],"year"=>$year,"target"=>"");
            }
        }

    }else{

        foreach($targets as $value){
            if($value['selected'] == "day" && $value['year'] == $year && $value['month'] == $month) {
                $showTargets[] = array("id"=>$value['id'], "date" => $date, "name"=>$value['name'], "year"=>$year, "target"=>$value["value"],"month"=>$month);
            }
        }
        if($showTargets == null ){
            foreach($objects as $v){
                $showTargets[] = array("id"=>$v['id'], "date" => $date,"name"=>$v['name'],"year"=>$year,"target"=>"","month"=>"");
            }
        }
    }

    $_SESSION['showTargets'] = $showTargets;

    return $showTargets;
}






//gets next or previous month target
function getPreviousNextMonth($date,$nextPrev ){

    $week_month = $_SESSION['week_month'];
    $rangeTargets = $_SESSION['showTargets'];
    $prevNextTargets = array();
    $currentDate = $date;

    if($week_month == 'month' && $nextPrev == "Previous"){
        $newDate = date("Y-m-d", strtotime($currentDate." -1 month"));
    }else if($week_month == 'month' && $nextPrev == "Next") {
        $newDate = date("Y-m-d", strtotime($currentDate." +1 month"));
    }
    $month = getMonth($newDate);

    if(($month=="12" && $nextPrev == "Previous")  || ($month=="01" && $nextPrev == "Next") ){

        if($_SESSION['warehouse_employee'] == 'employee'){
            $targets = getEmployeeAttributes($_SESSION['apiEmpl_Wareh'],"month");
        }else{
            $targets = getWarehouseAttributes($_SESSION['apiEmpl_Wareh'],"month");
        }
        $prevNextTargets = rangeTargets($newDate,$targets);
        $prevNextTargets = singleMonthTarget($prevNextTargets);

    }else{
        foreach($rangeTargets as $target){
            $decodeTargets = json_decode($target['target'],true);
            if($decodeTargets == null){
                $decodeTargets[$month] = 0;
            }
            $prevNextTargets[] = array("id"=>$target['id'],"name"=>$target['name'],"target"=>$decodeTargets[$month],"date"=>$newDate);
        }
    }
    return $prevNextTargets;
}


//gets next or previous week target
function getPreviousNextWeek($date,$nextPrev){

    $week_month = $_SESSION['week_month'];
    $rangeTargets = $_SESSION['showTargets'];
    $year = getYear($date);

    if($week_month == 'week' && $nextPrev == "Previous"){
        $newDate = date("Y-m-d", strtotime($date." -1 week"));
    }else if($week_month == 'week' && $nextPrev == "Next") {
        $newDate = date("Y-m-d", strtotime($date." +1 week"));
    }
    $newYear = getYear($newDate);

    if($year!=$newYear){
        if($_SESSION['warehouse_employee'] == 'employee'){
            $targets = getEmployeeAttributes($_SESSION['apiEmpl_Wareh'],"week");
        }else{
            $targets = getWarehouseAttributes($_SESSION['apiEmpl_Wareh'],"week");
        }
        $prevNextTargets = rangeTargets($newDate,$targets);
        $prevNextTargets = singleWeekTarget($prevNextTargets);
    }else{
        $prevNextTargets = singleWeekTarget($rangeTargets,$newDate);
    }
    return $prevNextTargets;
}


//gets next or previous day target
function getPreviousNextDay($date,$nextPrev){

    $week_month = $_SESSION['week_month'];
    $rangeTargets = $_SESSION['showTargets'];
    $year = getYear($date);
    $month = getMonth($date);

    if($week_month == 'day' && $nextPrev == "Previous"){
        $newDate = date("Y-m-d", strtotime($date." -1 day"));
    }else if($week_month == 'day' && $nextPrev == "Next") {
        $newDate = date("Y-m-d", strtotime($date." +1 day"));
    }
    $newYear = getYear($newDate);
    $newMonth = getMonth($newDate);

//
//    echo $date;
//    echo "<br>";
//    echo "newdate";
//    echo "<br>";
//    echo $newDate;
//    echo "<br>";

    if($year != $newYear){

        echo "aastad ei klapi";
        if($_SESSION['warehouse_employee'] == 'employee'){
            $targets = getEmployeeAttributes($_SESSION['apiEmpl_Wareh'],"day");
        }else{
            $targets = getWarehouseAttributes($_SESSION['apiEmpl_Wareh'],"day");
        }
        $prevNextTargets = rangeTargets($newDate,$targets);
        $prevNextTargets = singleDayTarget($prevNextTargets);
    }elseif($month != $newMonth) {

        echo "kuud ei klapi";
        if($_SESSION['warehouse_employee'] == 'employee'){
            $targets = getEmployeeAttributes($_SESSION['apiEmpl_Wareh'],"day");
        }else{
            $targets = getWarehouseAttributes($_SESSION['apiEmpl_Wareh'],"day");
        }
        $prevNextTargets = rangeTargets($newDate,$targets);
        $prevNextTargets = singleDayTarget($prevNextTargets);
    }else {
        $prevNextTargets = singleDayTarget($rangeTargets,$newDate);
    }
    return $prevNextTargets;
}


//gets asked week target
function singleWeekTarget($rangeTargets,$newDate=null){

    $date = $rangeTargets[0]['date'];
    $year = getYear($date);

    if($newDate != null){
        $week = getWeek($newDate);
        $date = $newDate;
    }else{
        $week = getWeek($date);
    }

    $targets = array();
    $objects =  $_SESSION['empl_Wareh'];

    foreach($objects as $v){
        $targets[$v['id']] = array("id"=>$v['id'], "name"=>$v['name'],"target"=>"", "date" => $date,"year"=>$year);
    }

    foreach($rangeTargets as $target){

        $t =json_decode($target['target'],true);
        if(array_key_exists($week, $t)){
            if($t == null)$t[$week] = 0;
            $targets[$target['id']]=array("id"=>$target['id'],"name"=>$target['name'],"target"=>$t[$week],"date" => $date,"year"=>$year,);
        }
    }
    return $targets;
}







//shows all warehouse targets
function getWarehouseAttributes($output,$week_month){

    $employee_warehouse = $_SESSION['empl_Wareh'];

    for ($i = 0; $i < count($employee_warehouse); $i++) {

        for ($j = 0; $j < count($employee_warehouse); $j++) {
            $e_wID = $employee_warehouse[$j]['id'];

            if ($output["records"][$i]["warehouseID"] ==  $e_wID) {
                $attributes = $output["records"][$i]["attributes"];

                for ($k = 0; $k < count($attributes); $k++) {
                    //weeks
                    if (strpos($attributes[$k]['attributeName'], 'weeks') !== false && $week_month == 'week') {
                        $name = $output["records"][$i]["name"];
                        $yearNumber = substr($attributes[$k]['attributeName'], 9);
                        $time = substr($attributes[$k]['attributeName'], 6, 2);
                        $warehouseId = $output["records"][$i]["warehouseID"];
                        $target = $attributes[$k]['attributeValue'];
                        $allTargets[] = array("id"=> $warehouseId,"name"=>$name,"year"=> $yearNumber,"week"=> $time, "value"=>$target, "selected"=>$week_month,"id"=>$warehouseId);
                    }
                    //months
                    if (strpos($attributes[$k]['attributeName'], 'months') !== false && $week_month == 'month') {
                        $yearNumber = substr($attributes[$k]['attributeName'], 6);
                        $warehouseName = $output["records"][$i]["name"];
                        $warehouseId = $output["records"][$i]["warehouseID"];
                        $target = $attributes[$k]['attributeValue'];
                        $allTargets[] = array("id"=> $warehouseId, "name"=>$warehouseName,"year"=> $yearNumber,"week"=> 0, "value"=>$target, "selected"=>$week_month,"id"=>$warehouseId);
                    }
                    //days
                    if (strpos($attributes[$k]['attributeName'], 'days') !== false && $week_month == 'day') {
                        $yearNumber = substr($attributes[$k]['attributeName'], 11);
                        $month = substr($attributes[$k]['attributeName'], 8,2);
                        $warehouseName = $output["records"][$i]["name"];
                        $warehouseId = $output["records"][$i]["warehouseID"];
                        $target = $attributes[$k]['attributeValue'];
                        $allTargets[] = array("id"=> $warehouseId, "name"=>$warehouseName,"year"=> $yearNumber,"week"=> $month, "value"=>$target, "selected"=>$week_month,"id"=>$warehouseId);
                    }
                }
            }
        }
    }
    return $allTargets;
}


//update single month targets
function updateTarget($input,$inputDate,$wareh_empl,$api){

    $rangeTargets = $_SESSION['tempTargets'] ;
    $year = getYear($inputDate);
    $month = getMonth($inputDate);
    $updatedIDS = array();

    foreach($rangeTargets as $value){

        $v = json_decode($value['value'],true);

        //if previously entered
        if($value['year'] == $year && $value['selected'] == "month" && $v[$month] != $input[$value['id']] ){

            $targets = json_decode($value['value'],true);
            $targets[$month] = $input[$value['id']];

            if($wareh_empl == "warehouse") {
                $param = array("warehouseID" => $value['id'], "attributeName1" => 'months' . $year, "attributeType1" => "text", "attributeValue1" => json_encode($targets));
                $output = $api->sendRequest("saveWarehouse", $param);
            }else{
                $param = array("employeeID" => $value['id'], "attributeName1" => 'months' . $year, "attributeType1" => "text", "attributeValue1" => json_encode($targets));
                $output = $api->sendRequest("saveEmployee", $param);
            }
            $updatedIDS[] = $value['id'];
            $output = json_decode($output, true);
            apiErrorCheck($output);

        }elseif($value['year'] == $year && $value['selected'] == "month" ){
            $updatedIDS[] = $value['id'];
        }
    }

    $ids = array();
    if(count($_SESSION['empl_Wareh'])>count($updatedIDS) ){
        foreach($_SESSION['empl_Wareh'] as $id){
            $ids[] = $id['id'];
        }
        $test = array_diff($ids,$updatedIDS);
    }

    //if targets are not inserted before
    foreach($test as $value){
        echo $value;
        $attributeValue = array('01' => 0, '02' => 0, '03' => 0, '04' => 0, '05' => 0, '06' => 0, '07' => 0, '08' => 0, '09' => 0, '10' => 0, '11' => 0, '12' => 0);
        $attributeValue[$month] = $input[$value];
        if($wareh_empl == "warehouse") {
            $param = array("warehouseID" => $value, "attributeName1" => 'months' . $year, "attributeType1" => "text", "attributeValue1" => json_encode($attributeValue));
            $output = $api->sendRequest("saveWarehouse", $param);
        }else{
            $param = array("employeeID" => $value, "attributeName1" => 'months' . $year, "attributeType1" => "text", "attributeValue1" => json_encode($attributeValue));
            $output = $api->sendRequest("saveEmployee", $param);
        }

        $output = json_decode($output, true);
        apiErrorCheck($output);
    }
}







//
////find targettable showtargets
//function currentTargets($targets,$year,$month,$week,$api) {
//
//    $week_month = $_SESSION['week_month'];
//    $employ_wareh = $_SESSION['empl_Wareh'];
//
//    for($i=0;$i<count($targets);$i++){
//
//        $yearFromData = $targets[$i]['year'];
//        $name = $targets[$i]['name'];
//        $weekMonthNumber = $targets[$i]['week'];
//        $target = $targets[$i]['value'];
//        $target = json_decode($target, true);
//        $id = $targets[$i]['id'];
//
//        //month targets
//        if($week_month =='month'){
//            if($yearFromData == $year ) {
//                $monthTarget = $target[$month];
//                $showTargets[] = array("name"=> $name, "year"=> $yearFromData, "month_week" => $month, "target" =>$monthTarget, "selected" => $week_month, "id" =>$id);
//            }
//            //week targets
//        }else{
//            if($yearFromData ==$year && $weekMonthNumber == $week) {
//                $showTargets[] = array("name"=> $name, "year"=> $yearFromData, "month_week" => $month, "target" =>$target, "selected" => $week_month, "id" =>$id);
//            }
//        }
//    }
//
//    if(count($showTargets)<count($employ_wareh)){
//        for($i=0;$i<count($employ_wareh);$i++){
//            $name =  $employ_wareh[$i]['name'];
//
//            if($name ==null)continue;
//
//            $yearFromData = $year;
//            $target = 0.00;
//            if($week_month=='month'){
//
//                if(!containsName($showTargets, 0, $name)){
//                    $showTargets[] = array("name"=> $name, "year"=> $yearFromData, "month_week" => $month, "target" =>$monthTarget, "selected" => $week_month, "id" =>$id);
//                }
//            }else{
//                if(!containsName($showTargets, 0, $name)){
//                    $showTargets[] = array("name"=> $name, "year"=> $yearFromData, "month_week" => $week, "target" =>$target, "selected" => $week_month, "id" =>$id);
//                }
//            }
//        }
//    }
//    return $showTargets;
//}



//checks if name contains in array already or not
//function containsName($array, $key, $val) {
//    foreach ($array as $item)
//        if (isset($item[$key]) && $item[$key] == $val)
//            return true;
//    return false;
//}
