<?php
    include('../config.php');

      $chartColumn = $_POST['chartColumn'];
      $columnType = $_POST['columnType'];
      $titleNo = $_POST['titleNo'];
      //$chartColumn = "Nonfamily Female Households";
      //$columnType = "Sum";
      if($titleNo!=0){
      switch($columnType){
        case Count:
        $sql = "SELECT DISTINCT Location, COUNT(Location) AS 'Amount'
        From colfusion_temporary
        Where Dname = '" .$chartColumn. "' " . 
		"AND  `location` IS NOT NULL " .
        "AND sid = " .$titleNo. " " .
        "Group by Location";
        break;

        case Sum:
        $sql = "SELECT DISTINCT Location, AVG(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$chartColumn."' " .
		"AND  `location` IS NOT NULL " .
        "AND sid = " .$titleNo. " " .
        "Group by Location";
        break;

        case Avg:
        $sql = "SELECT DISTINCT Location, AVG(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$chartColumn."' " .
		"AND  `location` IS NOT NULL " .
        "AND sid = " .$titleNo. " " .
        "Group by Location";
        break;
        
        case Min:
        $sql = "SELECT DISTINCT Location, MIN(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$chartColumn."' " .
		"AND  `location` IS NOT NULL " .
        "AND sid = " .$titleNo. " " .
        "Group by Location";
        break;

        case Max:
        $sql = "SELECT DISTINCT Location, MAX(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$chartColumn."' " .
		"AND  `location` IS NOT NULL " .
        "AND sid = " .$titleNo. " " .
        "Group by Location";
        break;
      } 
  }
  else {
      switch($columnType){
        case Count:
        $sql = "SELECT DISTINCT Location, COUNT(Location) AS 'Amount'
        From colfusion_temporary
        Where Dname = '" .$chartColumn. "' " . 
        "AND  `location` IS NOT NULL " .
        "Group by Location";
        break;

        case Sum:
        $sql = "SELECT DISTINCT Location, AVG(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$chartColumn."' " .
        "AND  `location` IS NOT NULL " .
        "Group by Location";
        break;

        case Avg:
        $sql = "SELECT DISTINCT Location, AVG(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$chartColumn."' " .
        "AND  `location` IS NOT NULL " .
        "Group by Location";
        break;
        
        case Min:
        $sql = "SELECT DISTINCT Location, MIN(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$chartColumn."' " .
        "AND  `location` IS NOT NULL " .
        "Group by Location";
        break;

        case Max:
        $sql = "SELECT DISTINCT Location, MAX(CAST(Value AS DECIMAL(10,1))) AS 'Value'
        From colfusion_temporary
        Where Dname = '" .$chartColumn."' " .
        "AND  `location` IS NOT NULL " .
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