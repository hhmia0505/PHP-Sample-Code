<?php
    include('../config.php');

    // get submitted form information from dashboard.php
    $mapLocation = $_POST['mapLocation'];
    $mapValue = $_POST['mapValue']; // controls how many tuples shown on each page
    $mapTooltip = $_POST['mapTooltip']; // controls which page to show, got from JS function
    $titleNo = $_POST['titleNo']; // controls which sid to go

    //$mapLocation = "geocode";
    //$mapValue = "Eid";
    //$mapTooltip = ["AggrType"];
    //$titleNo = 15; // controls which sid to go

    // initial variables
    $sql_col = "";

    // columns needed to be selected are stored in variable $sql_col
    for($i=0; $i<count($mapTooltip); $i++){
        $sql_col .= ",".$mapTooltip[$i];
    }

    if($mapLocation == "geocode"){
        if($titleNo != 0){
            $sql = "SELECT Dname,Value mapLocation,".$mapValue." mapValue".$sql_col." FROM colfusion_temporary WHERE sid = ".$titleNo." AND Dname = 'lat' or Dname = 'long'";
        } else {
            $sql = "SELECT Dname,Value mapLocation,".$mapValue." mapValue".$sql_col." FROM colfusion_temporary WHERE Dname = 'lat' or Dname = 'long'";
        }
        //$sql = "SELECT Dname,Value mapLocation,".$mapValue." mapValue".$sql_col." FROM colfusion_temporary WHERE Dname = 'lat' or Dname = 'long'";
        $rst = $db->get_results($sql);
        foreach ($rst as $r) { // get all results to array temp
            if($r->Dname == "lat"){
                $temp["Latitude"] = $r->mapLocation;
            }
            if($r->Dname == "long"){
                $temp["Longitude"] = $r->mapLocation;
                $temp["mapValue"] = $r->mapValue;
                for($i=0 ; $i<count($mapTooltip) ; $i++){
                    $temp[$mapTooltip[$i]] = $r->$mapTooltip[$i];
                }
                $json[] = $temp;
            }
        }
    }
    else{
        if($titleNo != 0){
            $sql = "SELECT ".$mapLocation." mapLocation,AVG(".$mapValue.") mapValue".$sql_col
            ." FROM colfusion_temporary WHERE sid = ".$titleNo." AND Dname <> 'lat' and Dname <> 'long'"
            ." GROUP BY ".$mapLocation.$sql_col;
        } else {
            $sql = "SELECT ".$mapLocation." mapLocation,AVG(".$mapValue.") mapValue".$sql_col
            ." FROM colfusion_temporary WHERE Dname <> 'lat' and Dname <> 'long'"
            ." GROUP BY ".$mapLocation.$sql_col;
        }
        /*$sql = "SELECT ".$mapLocation." mapLocation,AVG(".$mapValue.") mapValue".$sql_col
            ." FROM colfusion_temporary WHERE Dname <> 'lat' and Dname <> 'long'"
            ." GROUP BY ".$mapLocation.$sql_col;*/
        $rst = $db->get_results($sql);
        foreach ($rst as $r) { // get all results to array temp
            $temp["mapLocation"] = $r->mapLocation;
            $temp["mapValue"] = $r->mapValue;
            for($i=0 ; $i<count($mapTooltip) ; $i++){
                $temp[$mapTooltip[$i]] = $r->$mapTooltip[$i];
            }
            $json[] = $temp;
        }
        /*$json[] = $sql;
        $json[] = $mapLocation;
        $json[] = $mapValue;
        $json[] = $sql_col;*/
    }


    $json = json_encode($json);
    echo $json;
?>