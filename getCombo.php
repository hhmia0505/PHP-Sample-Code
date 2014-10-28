<?php
    include('../config.php');

      $comboColumn = $_POST['comboColumn'];
      $titleNo = $_POST['titleNo'];
      //$comboColumn = "Nonfamily Female Households";
      //$titleNo = 15;
      if($titleNo != 0){
      $sql = "SELECT DISTINCT Location, 
      AVG(CAST(Value AS DECIMAL(10,1))) AS 'AVG',MAX(Value) AS MAX,MIN(Value) AS 'MIN'
      From colfusion_temporary
      Where Dname = '" .$comboColumn. "' " .
      "AND sid = " .$titleNo. " " .
      "Group by Location";
     }
     else {
      $sql = "SELECT DISTINCT Location, 
      AVG(CAST(Value AS DECIMAL(10,1))) AS 'AVG',MAX(Value) AS MAX,MIN(Value) AS 'MIN'
      From colfusion_temporary
      Where Dname = '" .$comboColumn. "' " .
      "Group by Location";
     }


    $rst = $db->get_results($sql);
    $rows = array();
    foreach ($rst as $r) { // get all results to array temp
        $rows[] = $r;
    }
    echo json_encode($rows);
    
?>