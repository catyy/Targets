<?php
session_start();
error_reporting(E_ALL);
include "targetFuncsions.php";

//if submit all targets
if(isset($_POST['submitAddTargets']) ){

    //selected years
    if (isset($_POST['years'])) {
        $_SESSION['years'] = $_POST['years'];
    }
    if($_POST['whoseTargets'] == 'employee'){
        $_SESSION['warehouse_employee'] = 'employee';
        $_SESSION['apiEmpl_Wareh'] = apigetEmployees($api);
        $_SESSION['empl_Wareh'] = getEmployees($api);

    }elseif($_POST['whoseTargets'] == 'warehouse'){
        $_SESSION['warehouse_employee'] = 'warehouse';
        $_SESSION['apiEmpl_Wareh'] = apigetWarehouses($api);
        $_SESSION['empl_Wareh'] = getWarehouses($api);
    }
    targets($api);
}


//save ALL inserted employee targets
function targets($api){

    $years = $_SESSION['years'];

    //all months target field
    if ($_POST['months'] != null && $years != null) {
        $months = array('01' => $_POST['months'], '02' => $_POST['months'], '03' => $_POST['months'], '04' => $_POST['months'], '05' => $_POST['months'], '06' => $_POST['months'], '07' => $_POST['months'], '08' => $_POST['months'], '09' => $_POST['months'], '10' => $_POST['months'], '11' => $_POST['months'], '12' => $_POST['months']);

        //same month targets selected years
        for ($i = 0; $i < count($years); $i++) {
            $attributeName = "months".$years[$i];
            $attributeValue = json_encode($months);
            saveTarget($attributeName,$attributeValue,$api,null,null);
        }
    }


    //all weeks target field
    if ($_POST['weeks'] != null && $years != null) {
        $weeksTarget = $_POST['weeks'];

        //19 weeks arrays (one year week targets are in 3 separated attributes)
        $toWeek = 19;
        $from = 1;
        for ($i = $from; $i < $toWeek; $i++) {
            $i = sprintf('%02d', $i);
            $weeks1[$i] = $weeksTarget;
        }

        addAllWeekTargets($years,$from,$weeks1,$api);

        $from = 19;
        $toWeek = 37;
        for ($i = $from; $i < $toWeek; $i++) {
            $i = sprintf('%02d', $i);
            $weeks2[$i] = $weeksTarget;
        }

        addAllWeekTargets($years,$from,$weeks2,$api);

        $from = 37;
        $toWeek = 54;
        for ($i = $from; $i < $toWeek; $i++) {
            $i = sprintf('%02d', $i);
            $weeks3[$i] = $weeksTarget;
        }
        addAllWeekTargets($years,$from,$weeks3,$api);
    }



    //check single months
    if(array_filter($_POST['month']) && $years != null){

        //loops entered targets to array by months
        $key =1;
        foreach($_POST['month'] as $value){
            $changedMonths[sprintf('%02d', $key)] = $value;
            $key++;
        }
        //copys new targets to old attributes
        saveSingleMonthTargets($api,$years,$changedMonths);
    }



    //week single fields
    if(array_filter($_POST['week']) && $years != null) {

        $key =1;
        foreach($_POST['week'] as $value){
            $changedWeeks[sprintf('%02d', $key)] = $value;
            $key++;
        }

        //all attributes with these years
        $allWeeks = weekMaker($years,$api);

        $week1 =false;
        $week2 =false;
        $week3 =false;

        for($i=1;$i<=count($changedWeeks);$i++) {
            $i = sprintf('%02d', $i);

            if($i<19 && $changedWeeks[$i] !=null  && !$week1){
                saveSingleWeekTargets($api,$years,$changedWeeks,1,18,$_SESSION['apiEmpl_Wareh'],$allWeeks);
                $week1 = true;
            }elseif($i>18 && $i<37 && $changedWeeks[$i] !=null  && !$week2){
                saveSingleWeekTargets($api,$years,$changedWeeks,19,36,$_SESSION['apiEmpl_Wareh'],$allWeeks);
                $week2 = true;
            }elseif($i>36 && $i<54 && $changedWeeks[$i] !=null  && !$week3){
                saveSingleWeekTargets($api,$years,$changedWeeks,37,53,$_SESSION['apiEmpl_Wareh'],$allWeeks);
                $week3 = true;
            }
        }
    }
}

function saveSingleMonthTargets($api,$years,$changedMonths){


    if($_SESSION['warehouse_employee'] == 'employee'){
        $output = $_SESSION['apiEmpl_Wareh'];
        $idName='employeeID';
        $warehouse_employee = $_SESSION['empl_Wareh'];
        $counter = count($warehouse_employee);

    }else if($_SESSION['warehouse_employee'] == 'warehouse'){
        $output = $_SESSION['apiEmpl_Wareh'];
        $idName='warehouseID';
        $warehouse_employee =$_SESSION['empl_Wareh'];
        $counter = count($warehouse_employee);
    }

    $checkYears = array();
    $counter = $counter*count($years);
    $count = 0;

    for($i=0;$i<count($output["records"]);$i++) {

        //loop over users attributes
        for($k=0;$k<count($output["records"][$i]['attributes']);$k++) {

            //loop over all selected years
            for ($j = 0; $j < count($years); $j++) { //each year

                $id =$output["records"][$i][$idName];

                if ($output["records"][$i]["attributes"][$k]['attributeName'] == 'months'.$years[$j]) {

                    $previousValues = $output["records"][$i]["attributes"][$k]['attributeValue'];
                    $previousValues = json_decode($previousValues, true);
                    $checkYears[] = array("id" => $id, "year"=>$years[$j]);

                    //copy new targets to old value array
                    foreach ($changedMonths as $key=>$value) {
                        if($value != null){
                            $previousValues[$key] = $value;
                        }
                    }
                    $previousValues = json_encode($previousValues, true);
                    $attributeName = "months".$years[$j];
                    $count++;
                    saveTarget($attributeName,$previousValues,$api,$id,$warehouse_employee);
                }
            }
        }
    }

    //if selected year months are not inserted before
    if($count<$counter){
        $count = 0;
        $allYears = array();
        $result = null;

        foreach($warehouse_employee as $value){
            for($i=0;$i<count($years);$i++) {
                $allYears[]= array("id" => $value['id'], "year"=>$years[$i]);
            }
        }
        $attributeValue = array();

        //makes month array values 0
        for($i=1;$i<=12;$i++){
            $i = sprintf('%02d', $i);
            $attributeValue[$i] = 0;
        }

        //sum arrays
        foreach ($changedMonths as $key=>$value) {
            if($value != null){
                $attributeValue[$key] = $value;
            }
        }

     /*   print "<pre>";
        print_r($checkYears);
        print "</pre>";
*/
        foreach ($allYears as $key) {
            if(!in_array($key,$checkYears)){
                $result[] = $key;
            }
        }

        $values = json_encode($attributeValue, true);
        foreach($result as $v){
            $attributeName = "months".$v['year'];
            saveTarget($attributeName,$values,$api,$v['id'],$warehouse_employee);
        }
    }
}



function weekMaker($years,$api){

    if($_SESSION['warehouse_employee'] == 'employee') {
        $employees_warehouses = $_SESSION['empl_Wareh'];
    }else{
        $employees_warehouses = $_SESSION['empl_Wareh'];
    }
    $from0 = sprintf('%02d', 1);
    $from1 = 19;
    $from2 = 38;

    //all week attributes
    for($i=0;$i<count($years);$i++) {
        for($j=0;$j<count($employees_warehouses);$j++) {
            $weeks[] = array("id" => $employees_warehouses[$j]['id'],"from"=>$from0,"year"=>$years[$i]);
            $weeks[] = array("id" => $employees_warehouses[$j]['id'],"from"=>$from1,"year"=>$years[$i]);
            $weeks[] = array("id" => $employees_warehouses[$j]['id'],"from"=>$from2,"year"=>$years[$i]);
        }
    }
    return $weeks;
}



function saveSingleWeekTargets($api,$years,$changedWeeks,$from,$to,$output,$allWeeks){

    $checkYears = array();
    $enteredID = array();

    for($i=0;$i<count($output["records"]);$i++) {

        //loop over users attributes
        for($k=0;$k<count($output["records"][$i]['attributes']);$k++) {

            if($_SESSION['warehouse_employee'] == 'employee'){
                $id = $output["records"][$i]['employeeID'];
            }else if($_SESSION['warehouse_employee'] == 'warehouse'){
                $id = $output["records"][$i]['warehouseID'];
            }


            //loop over all selected years
            for ($j = 0; $j < count($years); $j++) {
                $from = sprintf('%02d', $from);
                //if attributename match
                if ($output["records"][$i]["attributes"][$k]['attributeName'] == 'weeks_'.$from.'_'.$years[$j]) {
                    $previousValues = $output["records"][$i]["attributes"][$k]['attributeValue'];
                    $previousValues = json_decode($previousValues, true);

                    //copy new targets to old value array
                    for($l=$from;$l<=$to;$l++){
                        $l = sprintf('%02d', $l);
                        $value = $changedWeeks[$l];

                        if($value != null){
                            $previousValues[$l] = $value;
                        }
                    }
                    $previousValues = json_encode($previousValues, true);
                    $attributeName = 'weeks_'.$from.'_'.$years[$j];
                    saveTarget($attributeName,$previousValues,$api,$id,null);
                    $enteredID[] = array("id" => $id,"from"=>$from,"year"=>$years[$j]);
                }
            }
        }
    }



    if(count($enteredID) != count($output["records"])*3){
        if($_SESSION['warehouse_employee'] == 'employee'){
            $employees_warehouses = $_SESSION['empl_Wareh'];

        }else if($_SESSION['warehouse_employee'] == 'warehouse'){;
            $employees_warehouses = $_SESSION['empl_Wareh'];
        }
        $weeksValues = array();

        //week values for saving
        for($i=$from;$i<=$to;$i++) {
            $i = sprintf('%02d', $i);
            if($changedWeeks[$i] != null){
                $weeksValues[$i] = $changedWeeks[$i];
            }else{
                $weeksValues[$i] = 0;
            }
        }

        foreach($allWeeks as $val) {

            if (!in_array($val, $enteredID)) {
                $checkYears[] = $val;
            }
        }

        foreach($checkYears as $value){
            $i = sprintf('%02d', $value["from"]);
            if($i == $from){
                $values = json_encode($weeksValues);
                $attributeName = 'weeks_'.$i.'_'.$value["year"];
                saveTarget($attributeName,$values,$api,$value['id'],$employees_warehouses);
            }
        }
    }
    return;
}




function addAllWeekTargets($years,$from,$week,$api){

    $from = sprintf('%02d', $from);
    for ($i = 0; $i < count($years); $i++) {
        $attributeName = "weeks_','$from,'_',$years[$i]";
        $attributeValue = json_encode($week);
        saveTarget($attributeName,$attributeValue,$api,null,null);
    }
}




//save targets
function saveTarget($attributeName,$attributeValue,$api,$id,$employee_warehouse){

    if ($_SESSION['warehouse_employee'] == 'employee') {

        if($id != null){
            $param = array("employeeID" => $id, "attributeName1" => $attributeName, "attributeType1" => "text", "attributeValue1" => $attributeValue);
            $output = $api->sendRequest("saveEmployee", $param);
            $output = json_decode($output, true);
            apiErrorCheck($output);
        }else{
            if($employee_warehouse == null){
                $employee_warehouse = $_SESSION['empl_Wareh'];
            }
            foreach ($employee_warehouse as $value) {
                $param = array("employeeID" => $value['id'], "attributeName1" => $attributeName, "attributeType1" => "text", "attributeValue1" => $attributeValue);
                $output = $api->sendRequest("saveEmployee", $param);
                $output = json_decode($output, true);
                apiErrorCheck($output);
            }
        }
    }elseif($_SESSION['warehouse_employee'] == 'warehouse') {
        if($employee_warehouse == null){
            $employee_warehouse = $_SESSION['empl_Wareh'];
        }

        foreach ($employee_warehouse as $value) {
            $param = array("warehouseID" => $value['id'], "attributeName1" => $attributeName, "attributeType1" => "text", "attributeValue1" => $attributeValue);
            $output = $api->sendRequest("saveWarehouse", $param);
            $output = json_decode($output, true);
            apiErrorCheck($output);
        }
    }
    return;

}