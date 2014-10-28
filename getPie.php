<?php
    include('../config.php');

      $pieColumn = $_POST['pieColumn'];
      $pieType = $_POST['pieType'];
      $titleNo = $_POST['titleNo'];
      //$pieColumn = "Nonfamily Female Households";
      //$pieType = "Count";
      if($titleNo!=0){
      switch($pieType){
        case Count:
        $sql = "SELECT DISTINCT Location, COUNT(Location) AS 'Amount'
        From colfusion_temporary
        Where Dname = '" .$pieColumn. "' " .
        "AND sid = " .$titleNo. " " .
        "Group by Location";
        break;

        case Sum:
        $sql = "SELECT DISTINCT Location, SUM(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$pieColumn."' " .
        "AND sid = " .$titleNo. " " .
        "Group by Location";
        break;

        case Avg:
        $sql = "SELECT DISTINCT Location, AVG(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$pieColumn."' " .
        "AND sid = " .$titleNo. " " .
        "Group by Location";
        break;
        
        case Min:
        $sql = "SELECT DISTINCT Location, MIN(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$pieColumn."' " .
        "AND sid = " .$titleNo. " " .
        "Group by Location";
        break;

        case Max:
        $sql = "SELECT DISTINCT Location, MAX(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$pieColumn."' " .
        "AND sid = " .$titleNo. " " .
        "Group by Location";
        break;
      }
  }
  else {
    switch($pieType){
        case Count:
        $sql = "SELECT DISTINCT Location, COUNT(Location) AS 'Amount'
        From colfusion_temporary
        Where Dname = '" .$pieColumn. "' " .
        "Group by Location";
        break;

        case Sum:
        $sql = "SELECT DISTINCT Location, SUM(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$pieColumn."' " .
        "Group by Location";
        break;

        case Avg:
        $sql = "SELECT DISTINCT Location, AVG(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$pieColumn."' " .
        "Group by Location";
        break;
        
        case Min:
        $sql = "SELECT DISTINCT Location, MIN(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$pieColumn."' " .
        "Group by Location";
        break;

        case Max:
        $sql = "SELECT DISTINCT Location, MAX(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$pieColumn."' " .
        "Group by Location";
        break;
  }
    }  

    $rst = $db->get_results($sql);
    $rows = array();
    foreach ($rst as $r) { // get all results to array temp
        $rows[] = $r;
    }
    echo json_encode($rows);
    
?>