<?php
    include('../config.php');

    // get submitted form information from dashboard.php
    $column = $_POST['column'];
    $perPage = $_POST['page']; // controls how many tuples shown on each page
    $pageNo = $_POST['currentPage']; // controls which page to show, got from JS function
    $display = $_POST['display'];  // controls table will be sqlStyle or excelStyle
	$titleNo = $_POST['titleNo']; // controls which sid to go

    //$column = ['Households','Married Couples'];
    //$perPage = 50; // controls how many tuples shown on each page
    //$pageNo = 1; // controls which page to show, got from JS function
    //$display = 'excelStyle';  // controls table will be sqlStyle or excelStyle
    //$titleNo = 15; // controls which sid to go

    // initial variables
    $sql_col = "";
    $totalTuple = 0; // the amount of tuples in colfusion_temporary
    $totalPage = 0; // the amount of pages for the whole result, which is calculated by totalTuple/perPage
    $startPoint = 0;

    if(empty($column)){
    	echo("Please select the columns!");
    }else{
        for($i=0; $i < count($column); $i++){
            if(strcmp($display,"sqlStyle") == 0){
                $sql_col .= $column[$i];
                if(!empty($column[$i+1])){
                    $sql_col .= ",";
                }
            }
            if(strcmp($display,"excelStyle") == 0){
                $sql_col .= "Dname = '".$column[$i]."'";
                if(!empty($column[$i+1])){
                    $sql_col .= " OR ";
                }
            }
            /*$sql_col .= $column[$i];
            if(!empty($column[$i+1])){
                $sql_col .= ",";
            }*/
        }
    }
    // calculate the amount of tuples in colfusion_temporary
    //$sql_count = "SELECT COUNT(*) Total FROM colfusion_temporary where sid = '" . $titleNo . "'";
    if(strcmp($display,"sqlStyle") == 0){
        if($titleNo != 0){
            $sql_count = "SELECT COUNT(*) Total FROM colfusion_temporary WHERE sid = ".$titleNo;
        } else {
            $sql_count = "SELECT COUNT(*) Total FROM colfusion_temporary";
        }
        // $sql_count = "SELECT COUNT(*) Total FROM colfusion_temporary";
    }
    if(strcmp($display,"excelStyle") == 0){
        if($titleNo != 0){
            $sql_count = "SELECT COUNT(*) Total FROM colfusion_temporary WHERE sid = ".$titleNo." AND ".$sql_col;
        } else {
            $sql_count = "SELECT COUNT(*) Total FROM colfusion_temporary WHERE ".$sql_col;
        }
        //$sql_count = "SELECT COUNT(*) Total FROM colfusion_temporary WHERE ".$sql_col;
    }
    $rst_count = $db->get_results($sql_count);
    foreach ($rst_count as $rst_count) {
        $totalTuple = $rst_count->Total;
    }
    // calculate the amount of pages for the whole result
    if(strcmp($display,"sqlStyle") == 0){
        $totalPage = ceil($totalTuple / $perPage);
    }
    if(strcmp($display,"excelStyle") == 0){
        $perPage = $perPage * count($column);
        $totalPage = ceil($totalTuple / $perPage);
    }

    // add control data to json["Control"]
    $json_array["Control"]["perPage"] = $perPage;
    $json_array["Control"]["totalPage"] = $totalPage;
    $json_array["Control"]["pageNo"] = $pageNo;
    $json_array["Control"]["sql_col"] = $sql_col;

    // selecting results to show
    //$sql = "SELECT ".$sql_col." FROM colfusion_temporary where sid = " . $titleNo;
    $startPoint = ($pageNo - 1) * $perPage;
    if(strcmp($display,"sqlStyle") == 0){
        if($titleNo != 0){
            $sql = "SELECT ".$sql_col." FROM colfusion_temporary"." WHERE sid = ".$titleNo." LIMIT ".$startPoint.",".$perPage;
        } else {
            $sql = "SELECT ".$sql_col." FROM colfusion_temporary LIMIT ".$startPoint.",".$perPage;
        }
        // $sql = "SELECT ".$sql_col." FROM colfusion_temporary LIMIT ".$startPoint.",".$perPage;
    }
    if(strcmp($display,"excelStyle") == 0){
        if($titleNo != 0){
            $sql = "SELECT Dname,Start,End,Value,Location FROM colfusion_temporary WHERE sid = ".$titleNo." AND ".$sql_col." LIMIT ".$startPoint.",".$perPage;
        } else {
            $sql = "SELECT Dname,Start,End,Value,Location FROM colfusion_temporary WHERE ".$sql_col." LIMIT ".$startPoint.",".$perPage;
        }
        // $sql = "SELECT Dname,Start,End,Value,Location FROM colfusion_temporary WHERE ".$sql_col." LIMIT ".$startPoint.",".$perPage;
    }
    // $sql = "SELECT ".$sql_col." FROM colfusion_temporary";
    $rst = $db->get_results($sql);
    foreach ($rst as $r) { // get all results to array temp
        $json_array["Page".$pageNo][] = $r;
    }

    $json = json_encode($json_array);
    echo $json;
?>