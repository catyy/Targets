<?php
session_start();
error_reporting(E_ALL);
include("conf.php");



if( isset($_POST['saveTarget']) &&   $_SESSION['sessionWho'] == 'employee' ) {
    echo "<br>&nbsp; Targets saved!";
    saveSingleEmployeeTarget($api);
}

if(isset($_POST['saveTarget']) &&    $_SESSION['sessionWho'] == 'warehouse') {
    echo "<br> &nbsp; Targets saved!";
    saveWarehouseTarget($api);
}

//gets all employees data
function apigetEmployees($api){
    $gEmployeeTarget = $api->sendRequest("getEmployees",[]);
    $outputE = json_decode($gEmployeeTarget, true);
    return $outputE;
}

//gets all warehouses and data
function apigetWarehouses($api){
    $gWarehouseTarget = $api->sendRequest("getWarehouses",[]);
    $output = json_decode($gWarehouseTarget, true);
    return $output;
}

function apiErrorCheck($output){
    if($output['status']['errorCode'] != 0){
        die("Could not get data!! Error code: ".$output['status']['errorCode']);
    }
}


//find all empolyees
function getEmployees($api) {
    $resultEmployees = $api->sendRequest("getEmployees", []);
    $output = json_decode($resultEmployees, true);
    apiErrorCheck($output);
    $counter = count($output['records']);
    $employees = array();

    for($i=0;$i<$counter;$i++){
        $name = htmlspecialchars($output['records'][$i]["fullName"]);
        $nameID = htmlspecialchars($output['records'][$i]["id"]);
        $employees[] =array("name" =>$name,"id" =>$nameID);
    }
    return $employees;
}


//find warehouses
function getWarehouses($api) {
    $resultWarehouses = $api->sendRequest("getWarehouses",[]);
    $output = json_decode($resultWarehouses, true);
    apiErrorCheck($output);
    $counter = count($output['records']);
    $warehouses = array();

    for($i=0;$i<$counter;$i++){
        $name =htmlspecialchars($output['records'][$i]["name"]);
        $nameID =htmlspecialchars($output['records'][$i]["warehouseID"]);
        $warehouses[] =array("name" =>$name,"id" =>$nameID);
    }
    return $warehouses;
}




//find json month targets by id and year
function findTargetById($oldTargets,$id,$year){

    foreach($oldTargets as $values){
        if($values['year'] == $year && $values['id'] == $id ){
            return $values['value'];
        }
    }
    return null;
}



//Save employee targets
function saveSingleEmployeeTarget($api){

    $oldTargets = $_SESSION['sessionTargets'] ;

    //inserted targets
    foreach ($_POST['showTargets'] as $value) {
        $id[] = array("id" => $value);
    }

    $employeesNumber = count($id);
    $_SESSION['date']= $_POST['saveDate'];
    $date = $_SESSION['date'];
    $year = getYear($date);
    $month = getMonth($date);
    $week = getWeek($date);
    $selected_radio = $_SESSION['sessionSelected_radio'];

    //gets new targets from input box
    foreach ($_POST['saveTarget'] as $value) {
        $newTargets[] = array("target"=>$value);
    }

    //save month Target
    if($selected_radio == 'month') {
        for($i=0;$i<$employeesNumber;$i++) {

            $targets = findTargetById($oldTargets, $id[$i]['id'], $year);
            if($targets == null){
                $attributeValue = array('01' => 0, '02' => 0, '03' => 0, '04' => 0, '05' => 0, '06' => 0, '07' => 0, '08' => 0, '09' => 0, '10' => 0, '11' => 0, '12' => 0);
            }else{
                $attributeValue = json_decode($targets, true);
                $attributeValue[$month] = $newTargets[$i]["target"];
               // print_r(json_encode($attributeValue));
            }

            $param = array("employeeID" => $id[$i]["id"], "attributeName1" => 'months' . $year, "attributeType1" => "text", "attributeValue1" => json_encode($attributeValue));
            $output = $api->sendRequest("saveEmployee", $param);
            $output = json_decode($output, true);
            apiErrorCheck($output);
        }
    }

    //save week Target
    if($selected_radio == 'week') {
        for($i=0;$i<$employeesNumber;$i++){
            $param  = array("employeeID" => $id[$i][0],"attributeName1" => "'week'.$week','$year","attributeType1" => "int", "attributeValue1" => $targets[$i][0]);
            $saveEmployeeWeekTarget = $api->sendRequest("saveEmployee",$param);
            $output = json_decode($saveEmployeeWeekTarget, true);
            apiErrorCheck($output);
        }
    }
}


//save warehouse targets
function saveWarehouseTarget($api){

    foreach ($_POST['showTargets'] as $value) {
        $id[] = array($value);
    }
    $warehouseNumber = count($id);
    $_SESSION['date']= $_POST['saveDate'];
    $date = $_SESSION['date'];

    $year = getYear($date);
    $month = getMonth($date);
    $week = getWeek($date);
    $selected_radio = $_SESSION['sessionSelected_radio'];

    //gets new targets
    foreach ($_POST['saveTarget'] as $value) {
        $targets[] = array($value);
    }

    //save month Target
    if($selected_radio == 'month') {
        for($i=0;$i<$warehouseNumber;$i++){
            $param  = array("warehouseID" => $id[$i][0],"attributeName1" => "$month','$year","attributeType1" => "int","attributeValue1" => $targets[$i][0]);
            $saveWarehouseMonthTarget = $api->sendRequest("saveWarehouse",$param);
            $output = json_decode($saveWarehouseMonthTarget, true);
            apiErrorCheck($output);
        }
    }

    //save week Target
    if($selected_radio == 'week') {
        for($i=0;$i<$warehouseNumber;$i++){
            $param  = array("warehouseID" => $id[$i][0],"attributeName1" => "'week'.$week','$year","attributeType1" => "int", "attributeValue1" => $targets[$i][0]);
            $saveWarehouseWeekTarget = $api->sendRequest("saveWarehouse",$param);
            $output = json_decode($saveWarehouseWeekTarget, true);
            apiErrorCheck($output);
        }
    }
}

//gets week start and end date
function getStartAndEndDate($week, $year) {
    $dto = new DateTime();
    $ret['week_start'] = $dto->setISODate($year, $week)->format('Y-m-d');
    $ret['week_end'] = $dto->modify('+6 days')->format('Y-m-d');
    return $ret;
}


//find day
function  getDay($var_dateDay){
    $date = new DateTime($var_dateDay);
    $day = $date->format("d");
    return $day;
}

//find week
function getWeek($var_dateWeek){
    $date = new DateTime($var_dateWeek);
    $week = $date->format("W");
    return $week;
}

//find month
function getMonth($var_dateMonth){
    $date = new DateTime($var_dateMonth);
    $month = $date->format("m");
    return $month;
}

//find year
function getYear($var_dateYear){
    $date = new DateTime($var_dateYear);
    $year = $date->format("Y");
    return $year;
}

//make array of selected warehouses/employees with ids and names
function w_e_arrayMaker($all_W_E,$selected_W_E){

    $results = array();
    foreach($all_W_E as $value){
        if(in_array($value['id'],$selected_W_E)){
            $results[$value['id']] = array("id"=>$value['id'], "name"=> $value['name'] );
        }
    }
    return $results;
}

function readReportFile($data,$selected_W_E){

    $saleReportArray = array();
    $newArray = array_slice($data, 1, -1);

    foreach ($newArray as $value) {

        $split = explode(',', $value[0],6);
        $split = str_ireplace('"', '', $split);
        $saleReportArray[$split[1]] = array("id"=>$split[1], "name"=>$selected_W_E[$split[1]]['name'],"sold"=>$split[3],"net_sale"=>$split[4]);
    }
    return $saleReportArray;
}


//gets report and data from it
function getEmployeeSaleReport($d_w_m, $date, $selected_W_E,$api){

    echo "report func";
    $year = getYear($date);
    $month = getMonth($date);
    $week = getWeek($date);
    //$day = getDay($date);

    if($d_w_m == 'week'){
        echo "week";
        $weekDates = getStartAndEndDate($week, $year);
        $params  = array('reportType' => "SALES_BY_CASHIER", "dateStart" => $weekDates['week_start'],"dateEnd" => $weekDates['week_end']);

    }elseif($d_w_m == 'day') {
        echo "paev";
        $params  = array('reportType' => "SALES_BY_CASHIER", "dateStart" => $date,"dateEnd" => $date);

    }else{

        $dayNumber = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $params  = array('reportType' => "SALES_BY_CASHIER", "dateStart" => $year."-".$month."-01","dateEnd" => $year."-".$month."-".$dayNumber);
    }

    $resultReport = $api->sendRequest("getSalesReport", $params);
    $output = json_decode($resultReport, true);
    apiErrorCheck($output);
    $fileName = htmlspecialchars($output['records'][0]['reportLink']);
    $csvFile = file($fileName);
    $data = [];

    foreach ($csvFile as $line) {
        $data[] = str_getcsv($line, "\n");
    }

    $saleReportArray = readReportFile($data,$selected_W_E);
    return $saleReportArray;
}



//shows all employee targets (employees-all data,moth/week,months from conf file)
function getEmployeeAttributes($output,$week_month,$e_w=null){


    if($e_w == null){
        $employee_warehouse = $_SESSION['empl_Wareh'];
    }else{
        $employee_warehouse = $e_w;
    }

    for ($i = 0; $i < count($employee_warehouse); $i++) {

        for ($j = 0; $j < count($employee_warehouse); $j++) {
            $e_wID = $employee_warehouse[$j]['id'];

            if ($output["records"][$i]["employeeID"] ==  $e_wID) {
                $attributes = $output["records"][$i]["attributes"];

                for ($k = 0; $k < count($attributes); $k++) {
                    //weeks
                    if (strpos($attributes[$k]['attributeName'], 'weeks') !== false && $week_month == 'week') {
                        $name = $output["records"][$i]["fullName"];
                        $yearNumber = substr($attributes[$k]['attributeName'], 9);
                        $time = substr($attributes[$k]['attributeName'], 6, 2);
                        $employeeId = $output["records"][$i]["employeeID"];
                        $target = $attributes[$k]['attributeValue'];
                        $allTargets[] = array("id"=> $employeeId,"name"=>$name,"year"=> $yearNumber,"week"=> $time, "value"=>$target, "selected"=>$week_month,"id"=>$employeeId);
                    }
                    //months
                    if (strpos($attributes[$k]['attributeName'], 'months') !== false && $week_month == 'month') {
                        $yearNumber = substr($attributes[$k]['attributeName'], 6);
                        $employeeName = $output["records"][$i]["fullName"];
                        $employeeId = $output["records"][$i]["employeeID"];
                        $target = $attributes[$k]['attributeValue'];
                        $allTargets[] = array("id"=> $employeeId, "name"=>$employeeName,"year"=> $yearNumber,"week"=> 0, "value"=>$target, "selected"=>$week_month,"id"=>$employeeId);
                    }
                    //days
                    if (strpos($attributes[$k]['attributeName'], 'days') !== false && $week_month == 'day') {
                        $yearNumber = substr($attributes[$k]['attributeName'], 11);
                        $month = substr($attributes[$k]['attributeName'], 8,2);
                        $employeeName = $output["records"][$i]["fullName"];
                        $employeeId = $output["records"][$i]["employeeID"];
                        $target = $attributes[$k]['attributeValue'];
                        $allTargets[] = array("id"=> $employeeId, "name"=>$employeeName,"year"=> $yearNumber,"month"=> $month, "value"=>$target, "selected"=>$week_month,"id"=>$employeeId);
                    }
                }
            }
        }
    }
//    echo "all this year targets";
//    print "<pre>";
//    print_r($allTargets);
//    print "</pre>";

    return $allTargets;
}


//gets asked month target
function singleMonthTarget($showTargets, $date = null,$selected_W_E=null){

    $month = getMonth($date);
    $year = getYear($date);
    $targets = array();

    if($date == null){
        $date = $showTargets[0]['date'];
        $targets = array();

        foreach($showTargets as $target){

            $t =json_decode($target['target'],true);
            if($t == null)$t[$month] = 0;
            $targets[]=array("id"=>$target['id'],"name"=>$target['name'],"target"=>$t[$month],"date" => $date,"year" => $year);
        }
    }else{

        print "<pre>";
        print_r($selected_W_E);
        print "</pre>";

        foreach($showTargets as $target){


            if($target['year'] == $year && array_key_exists($target['id'],$selected_W_E)){

                $t =json_decode($target['value'],true);
                print "<pre>";
                print_r($t[$month]);
                print "</pre>";


                if($t == null)$t[$month] = 0;
                $targets[]=array("id"=>$target['id'],"name"=>$target['name'],"target"=>$t[$month],"date" => $date,"year" => $year);
            }
        }
    }
    return $targets;
}

//join report data and targets
function joinSaleTarget($sortData,$showReport){

    $result = array();
    $percent = array();

    foreach($sortData as $data){
        if(array_key_exists($data['id'],$showReport)){
            $sold = $showReport[$data['id']]['sold'];
            $net_sale = $showReport[$data['id']]['net_sale'];
        }else{
            $sold = 0;
            $net_sale = 0;
        }

        $name = htmlspecialchars($data['name'],UTF-8);
        $percent = countPercent($net_sale,$data['target']);
        $result[] = array("id"=>$data['id'],"name"=>$name,"sold"=>$sold,"net_sale"=>$net_sale,"target" => $data['target'],"date" => $data['date'],"year" => $data['year'],"percent"=>$percent[0],"percentBar"=>$percent[1]);
    }

    return $result;
}

//counts percent
function countPercent($sale,$target){

    $percent = $sale/$target*100;
// Limit to 100 percent (if more than shows 100)
    if ( $percent > 100 ) {
        $percent1 = 100;
    }else{
        $percent1 = $percent;
    }
    return array(number_format($percent, 2),$percent1);
}


//gets single day target
function singleDayTarget($rangeTargets,$newDate = null,$selected_W_E = null){

    print "<pre>";
    print_r($rangeTargets);
    print "</pre>";


    if($selected_W_E == null){
        $objects =  $_SESSION['empl_Wareh'];
    }else{
        $objects =  $selected_W_E;
    }

    if($newDate == null){
        $date = $rangeTargets[0]['date'];
    }else{
        $date = $newDate;
    }

    echo $date;
    $year = getYear($date);
    $day = getDay($date);
    $targets = array();

    foreach($objects as $v){
        $targets[$v['id']] = array("id"=>$v['id'], "name"=>$v['name'],"target"=>"", "date" => $date,"year"=>$year,"month"=>"");
    }

    print "<pre>";
    print_r($targets);
    print "</pre>";

    foreach($rangeTargets as $target){

        $t =json_decode($target['target'],true);

        if(array_key_exists($day, $t)){

            if($t == null)$t[$day] = 0;

            $targets[$target['id']] = array("id"=>$target['id'],"name"=>$target['name'],"target"=>$t[$day],"date" => $date,"year"=>$year,"month"=>$target['month']);

        }
    }

    echo "yhe p2eva targetid";
    print "<pre>";
    print_r($targets);
    print "</pre>";

    return $targets;
}


//filters atrributes right month targets
function filterMonth($attributes,$date){

    $month = getMonth($date);
    $result = array();
    foreach($attributes as $data){
        if($data['month'] == $month){
            $result[] = $data;
        }
    }
    return $result;
}






function getWarehouseSaleReport($selected_radio,$date,$months,$warehouse,$api){
    $year = getYear($date);
    $month = getMonth($date);
    $week = getWeek($date);

    //week
    if ($selected_radio == 'week') {
        $weekDates = getStartAndEndDate($week, $year);
        $params = array('reportType' => "SALES_BY_WAREHOUSE", "dateStart" => $weekDates['week_start'], "dateEnd" => $weekDates['week_end']);

        //month
    } else {
        $key = array_search($month, $months);
        $key = sprintf('%02d', $key + 1);
        $dayNumber = cal_days_in_month(CAL_GREGORIAN, $key, $year);
        $params = array('reportType' => "SALES_BY_WAREHOUSE", "dateStart" => $year . "-" . $key . "-01", "dateEnd" => $year . "-" . $key . "-" . $dayNumber);
    }

    $resultReport = $api->sendRequest("getSalesReport", $params);
    $output = json_decode($resultReport, true);
    apiErrorCheck($output);

    $fileName = htmlspecialchars($output['records'][0]['reportLink']);
    $csvFile = file($fileName);
    $data = [];

    foreach ($csvFile as $line) {
        $data[] = str_getcsv($line, "\n");
    }

    for ($i = 0; $i < count($warehouse); $i++) {

        if($warehouse[$i] == "")continue;

        $warehouseID = explode(',', $warehouse[$i]);
        $id = str_ireplace('"', '""', $warehouseID[0]);
        $id = (int)$id;

        for ($j = 0; $j < count($data); $j++) {
            $row = explode(',', $data[$j][0]);
            $num = str_ireplace('"', '', $row[1]);
            $int = (int)$num;

            if ($int ==  $id) {
                $warehouseName = htmlentities($warehouseID[1]);
                $warehouseNeto = str_ireplace('"', '',$row[4]);
                break;
            }else{
                $warehouseName =htmlentities($warehouseID[1]);
                $warehouseNeto = 0.00;
            }
        }
        $saleReportArray[] = array($id, $warehouseName, $warehouseNeto);
    }
    return $saleReportArray;
}




//finds id target
function  findTarget($attributes,$id,$date){

    $selected_radio = $_SESSION['sessSelected_radio'];
    $week = getWeek($date);
    $year = getYear($date);
    $month = getMonth($date);

    for ($i = 0; $i < count($attributes); $i++) {

        if($selected_radio == 'week'){
            if($attributes[$i][5] == $id && $attributes[$i][1] == $year && $attributes[$i][2] == $week && $attributes[$i][4] == $selected_radio){
                $target = $attributes[$i][3];
            }
        }else{
            if($attributes[$i][5] == $id && $attributes[$i][1] == $year && $attributes[$i][2] == $month && $attributes[$i][4] == $selected_radio){
              $target = $attributes[$i][3];
            }
        }
    }
    if($target == null)$target =0;

    return $target;
}






