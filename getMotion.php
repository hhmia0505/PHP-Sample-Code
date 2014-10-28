<?php
include('../config.php');

$titleNo = $_POST['titleNo'];

if($titleNo!=0){
	$sql = "SELECT  `Start` ,  `End` ,  `Dname` ,  `Location` , AVG(  `value` ) AS Value "
		. "FROM  `colfusion_temporary` "
		. "WHERE  `start` IS NOT NULL "
		. "AND  `end` IS NOT NULL "
		. "AND  `location` IS NOT NULL "
		. "AND sid = " .$titleNo. " "
		. "	GROUP BY  `Start` ,  `End` ,  `Dname` ,  `Location` ";

}
else {
	$sql = "SELECT  `Start` ,  `End` ,  `Dname` ,  `Location` , AVG(  `value` ) AS Value "
		. "FROM  `colfusion_temporary` "
		. "WHERE  `start` IS NOT NULL "
		. "AND  `end` IS NOT NULL "
		. "AND  `location` IS NOT NULL "
		. "	GROUP BY  `Start` ,  `End` ,  `Dname` ,  `Location` ";
}

$rst = $db->get_results($sql);
$rows = array();
foreach ($rst as $r) {
	$rows[] = $r;
}
echo json_encode($rows);

?>