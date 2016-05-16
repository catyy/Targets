<?php
session_start();
error_reporting(E_ALL);
include 'addTargetsFunctions.php';

function saveUploadedDayTargets($data,$api) {

    foreach ($data as $key => $row) {
        $day[$key]  = $row[0];
        $month[$key] = $row[1];
        $year[$key] = $row[2];
        $target[$key] = $row[3];

        if(!isset($row[1]) and empty($row[0])){
            break;
        }

       if(!is_numeric($row[0] )){
            die('<br> File contains string or empty input.'.$row[0] );
        }
        if(!is_numeric($row[1])){
            die('<br> File contains string or empty input.'.$row[1] );
        }
        if(!is_numeric( $row[2] )){
            die('<br> File contains string or empty input.'.$row[2] );
        }
        if(!is_numeric($row[3])){
            die('<br> File contains string or empty input.'.$row[3] );

        }else if($row[0] >31 || $row[1] >12 || $row[2] >3000 || $row[0]<1 || $row[1]<1 || $row[2]<1900){
            die('<br> Day, month or year number is incorrect!');
        }
    }
    array_multisort($year, SORT_ASC, $month, SORT_ASC, $day, SORT_ASC, $data);

/*    print "<pre>";
    print_r($attributeValue);
    print "</pre>";
*/
    $lastMonth = null;
    $lastYear = null;
    $check = false;
    $attributeValue = null;

    for($i=0;$i<=count($data);$i++){
        $day  = $data[$i][0];
        if($i==null){
            continue;
        }
        $month = $data[$i][1];
        $year = $data[$i][2];
        $target = $data[$i][3];
        $j = sprintf('%02d', $day);
        if(!$check){
            $attributeValue = attributeArrayMaker($month,$year);
            $attributeValue[$j] = $target;
            $lastMonth = $month;
            $lastYear = $year;
            $check = true;
        }else if($lastMonth == $month && $lastYear == $year){
            $attributeValue[$j] = $target;
        }else{
            $check = false;
            $i--;
            sortDays($attributeValue,$lastMonth,$lastYear,$api);
        }
    }
}


function sortDays($attributeValue,$lastMonth,$lastYear,$api){

    $lastMonth = sprintf('%02d', $lastMonth);
    $attributeName1 = "days_01_".$lastMonth."_".$lastYear;
    $attributeName2 = "days_16_".$lastMonth."_".$lastYear;
    $attributeTemp1 = array();
    $attributeTemp2 = array();

    for($i=1;$i<16;$i++){
        $i = sprintf('%02d', $i);
        $attributeTemp1[$i] = $attributeValue[$i];
    }
    saveTarget($attributeName1,json_encode($attributeTemp1),$api,null,null);

    for($i=16;$i<=(count($attributeValue));$i++){
        $attributeTemp2[$i] = $attributeValue[$i];
    }
    saveTarget($attributeName2,json_encode($attributeTemp2),$api,null,null);
 }


function attributeArrayMaker($month,$year){

    $number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $attributeValue = array();

    for($i=1;$i<=$number;$i++){
        $i = sprintf('%02d', $i);
        $attributeValue[$i] = 0;
    }
    return $attributeValue;
}