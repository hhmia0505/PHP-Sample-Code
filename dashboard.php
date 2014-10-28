<?php 
	//include('getColumns.php');
	include('../config.php');	
	include('../'.mnminclude.'html1.php');
	include('../'.mnminclude.'link.php');
	include('../'.mnminclude.'tags.php');
	include('../'.mnminclude.'user.php');
	include('../'.mnminclude.'smartyvariables.php');
	
	/****************
	  force login
	****************/
	global $current_user;
	if($current_user->authenticated != TRUE) {
		echo "not log in";
		echo $_SERVER['REQUEST_URI'];
		header("Location: " . $my_base_url . $my_pligg_base . "/login.php?return=" . $_SERVER['REQUEST_URI']);
	}

	/***********************	
	  get dataset title No
	***********************/
	$titleNum = $_GET['title'];
	$userid = $current_user->user_id;
	$sqlVis = "SELECT * FROM colfusion_visualization WHERE userid = '" . $userid . "' ";
	if($titleNum) {
		$sqlVis .= " AND titleno = '" . $titleNum . "' ";
	}
	else {
		$titleNum = 0;
	}
	$rstVis = $db->get_results($sqlVis);
	$visGadgets = array();
	foreach($rstVis as $row)
	{	
		$visGadgets[] = $row;
	}

	/***********************	
	  get sql style and excel style table columns
	***********************/	
	//sql style table columns
	$sql_columns = array();
	$column_types = array();

	//excel style table columns
	$excel_columns = array();

	//columns for charts like pie, combo
	$chart_columns = array();

	//get excel style columns
	$sql = "SELECT DISTINCT dname FROM colfusion_temporary ";
	if($titleNum) {
		$sql .= " WHERE sid = '" . $titleNum . "' ";
	}
	$rst = $db->get_results($sql);
	foreach($rst as $row)
	{
		$excel_columns[] = $row->dname;
		$chart_columns[] = $row->dname;
	}

	//select sql style columns
	$sql = "SELECT column_name, column_type FROM information_schema.columns WHERE table_name = 'colfusion_temporary' and column_name not in ('tid', 'sid', 'eid') ";	
	$rst = $db->get_results($sql);
	foreach($rst as $row)
	{
		$sql_columns[] = $row->column_name;
		$sql_column_types[] = $row->column_type;
		if($row->column_name != 'Dname' && $row->column_name != 'Value' && substr($row->column_type,0,7) == 'varchar'){
			$chart_columns[] = $row->column_name;
		}
	}
?>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Visualization | Col*Fusion</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- styles and functions for dashboard-->
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/bootstrap-responsive.css">
		<link rel="stylesheet" href="css/jquery.ui.all.css">	
		<link rel="stylesheet" href="css/dashboard.css">	
		<script type="text/javascript" src="js/jquery-1.9.1.js"></script>
		<script type="text/javascript" src="js/bootstrap.js"></script>	
		<script type="text/javascript" src="js/jquery-ui.js"></script>	
		<script type="text/javascript" src="js/jquery.ui.core.js"></script>
		<script type="text/javascript" src="js/jquery.ui.widget.js"></script>
		<script type="text/javascript" src="js/jquery.ui.mouse.js"></script>
		<script type="text/javascript" src="js/jquery.ui.resizable.js"></script>
		<script type="text/javascript" src="js/jquery.ui.draggable.js"></script>
	    <script type="text/javascript" src="js/dashboardJS.js"></script>		
		
		<!-- styles and functions for table -->
	    <link rel="stylesheet" href="css/googleTableCSS.css" />
	    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
	    <script type="text/javascript" src="js/tableJS.js"></script>

	    <!-- styles and functions for geo chart -->
	    <script type="text/javascript" src="js/mapJS.js"></script>

		<!-- styles and functions for pie chart -->
	    <script type="text/javascript" src="js/pieJS.js"></script>
		<script type="text/javascript" src="js/json2.js"></script>

		<!-- styles and functions for column chart -->
		<script type="text/javascript" src="js/columnJS.js"></script>

	    <!-- styles and functions for combo chart -->
		<script type="text/javascript" src="js/comboJS.js"></script>

		<!-- styles and functions for motion chart -->
		<script type="text/javascript" src="js/motionJS.js"></script>
	</head>
	<body onload="loadTables();loadMotions();loadColumns();loadPies();loadCombos();">
		<!-- Navigation bar -->
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<div class="brand">Col*Fusion Visualization</div>
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li class="dropdown">
								<a href="#visualization" class="dropdown-toggle" data-toggle="dropdown">Add <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="#addTable" data-toggle="modal">Add Table</a></li>
									<li><a href="#addPie" data-toggle="modal">Add Pie Chart</a></li>
									<li><a href="#addMotion" data-toggle="modal">Add Motion Chart</a></li>
									<li><a href="#addColumn" data-toggle="modal">Add Column Chart</a></li>
									<li><a href="#addCombo" data-toggle="modal">Add Combo Chart</a></li>
									<li><a href="#addMap" data-toggle="modal">Add Map</a></li>
								</ul>
							</li>			
							<li class="dropdown">
								<a href="#view" class="dropdown-toggle" data-toggle="dropdown">View <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<?php if(!$visGadgets){ ?>
									<li id="viewnone"><a data-toggle="modal">None</a></li>
									<?php }else{ foreach($visGadgets as $vis) { ?>
									<li class="view" id="view<?=$vis->vid?>">
										<a href="#" data-toggle="modal"><?=$vis->type?> <?=$vis->vid?></a>
									</li>
									<?php } } ?>
								</ul>
							</li>
						</ul>
						<button class="btn" name="save" id="save">Save</button>
						<input type="hidden" id="titleNo" value="<?php echo $titleNum ?>"/>
						<input type="hidden" id="userid" value="<?php echo $current_user->user_id;?>">
						<a class="pull-right btn" href="../index.php">Go Back</a>
						<div class="pull-right welcome">Welcome, <?php echo $current_user->user_login; ?></div>
					</div><!--/.nav-collapse -->
				</div>
			</div>
		</div>

		<!-- Add Table Modal -->
		<div id="addTable" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="tableAddModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h3 id="tableAddModalLabel">Add Table</h3>
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#columns" data-toggle="tab">Columns</a></li>
					<li><a href="#page" data-toggle="tab">Pages</a></li>
					<li><a href="#style" data-toggle="tab">Style</a></li>
				</ul>		 
				<div class="tab-content">
					<div class="tab-pane active" id="columns">
						<label class="tabContentTitle">Table Display</label>
						<label class="radio">
							<input type="radio" name="display" id="displayRadios2" value="excelStyle" checked>Excel Style Table
						</label>
						<div id="excelStyleColumns" class="columnSelection">
							<?php foreach($excel_columns as $excel_name) { ?>
							<label class="checkbox">
								<input type="checkbox" name="excelColumn[]" value="<?=$excel_name?>" /> <?php echo $excel_name; ?>
							</label>
							<?php } ?>
						</div>
						<label class="radio">
							<input type="radio" name="display" id="displayRadios1" value="sqlStyle">SQL Style Table
						</label>
						<div id="sqlStyleColumns" class="columnSelection" style="display:none;">
							<?php foreach($sql_columns as $name) { ?>
							<label class="checkbox">
								<input type="checkbox" name="sqlColumn[]" value="<?=$name?>" /> <?php echo $name; ?>
							</label>
							<?php } ?>
						</div>
					</div>
					<div class="tab-pane" id="page">
						<label class="tabContentTitle">Number of turples per page</label>
						<label class="radio"><input type="radio" name="page" id="optionsRadios1" value="20"> 20 per page</label>
						<label class="radio"><input type="radio" name="page" id="optionsRadios2" value="50" checked> 50 per page</label>
						<label class="radio"><input type="radio" name="page" id="optionsRadios3" value="100"> 100 per page</label>
					</div>
					<input type="hidden" name="currentPage" value="1" />
					<div class="tab-pane" id="style">
						<label class="tabContentTitle">Table Color-Themes</label>
						<label class="radio">
							<input type="radio" name="color" id="themeRadios1" value="blue" checked> Blue Theme
							<img src="img/tableThemes/blueTheme.png" alt="Blue Theme" />
						</label>
						<label class="radio">
							<input type="radio" name="color" id="themeRadios2" value="purple"> Purple Theme
							<img src="img/tableThemes/purpleTheme.png" alt="Purple Theme" />
						</label>
						<label class="radio">
							<input type="radio" name="color" id="themeRadios3" value="green"> Green Theme
							<img src="img/tableThemes/greenTheme.png" alt="Green Theme" />
						</label>
						<label class="radio">
							<input type="radio" name="color" id="themeRadios4" value="orange"> Orange Theme
							<img src="img/tableThemes/orangeTheme.png" alt="Orange Theme" />
						</label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="addTableSave" onclick="loadData(1,0)">Save changes</button>
			</div>
		</div>


		<!-- Edit Table Modal -->
		<div id="editTable" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="tableEditModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h3 id="tableEditModalLabel">Edit Table</h3>
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs">
					<li id="columnEditTab" class="active"><a href="#columnsEdit" data-toggle="tab">Columns</a></li>
					<li id="pageEditTab"><a href="#pageEdit" data-toggle="tab">Pages</a></li>
					<li id="styleEditTab"><a href="#styleEdit" data-toggle="tab">Style</a></li>
				</ul>		 
				<div class="tab-content">
					<div class="tab-pane active" id="columnsEdit">
						<label class="tabContentTitle">Table Display</label>
						<label class="radio">
							<input type="radio" name="displayEdit" id="displayEditRadios2" value="excelStyle"> Excel Style Table
						</label>
						<div id="excelStyleColumnsEdit" class="columnSelection" style="display:none;">
							<?php foreach($excel_columns as $excel_name) { ?>
							<label class="checkbox">
								<input type="checkbox" name="excelcolumnEdit[]" value="<?=$excel_name?>" /> <?php echo $excel_name; ?>
							</label>
							<?php } ?>
						</div>
						<label class="radio">
							<input type="radio" name="displayEdit" id="displayEditRadios1" value="sqlStyle"> SQL Style Table
						</label>
						<div id="sqlStyleColumnsEdit" class="columnSelection" style="display:none;">
							<?php foreach($sql_columns as $name) { ?>
							<label class="checkbox">
								<input type="checkbox" name="sqlcolumnEdit[]" value="<?=$name?>" /> <?php echo $name; ?>
							</label>
							<?php } ?>
						</div>
					</div>
					<div class="tab-pane" id="pageEdit">
						<label class="tabContentTitle">Number of turples per page</label>
						<label class="radio"><input type="radio" name="pageEdit" id="optionsEditRadios1" value="20"> 20 per page</label>
						<label class="radio"><input type="radio" name="pageEdit" id="optionsEditRadios2" value="50"> 50 per page</label>
						<label class="radio"><input type="radio" name="pageEdit" id="optionsEditRadios3" value="100"> 100 per page</label>			
					</div>
					<input type="hidden" name="currentPage" value="1" />
					<div class="tab-pane" id="styleEdit">
						<label class="tabContentTitle">Table color-themes</label>
						<label class="radio">
							<input type="radio" name="colorEdit" id="themeEditRadios1" value="blue"> Blue Theme
							<img src="img/tableThemes/blueTheme.png" alt="Blue Theme" />
						</label>
						<label class="radio">
							<input type="radio" name="colorEdit" id="themeEditRadios2" value="purple"> Purple Theme
							<img src="img/tableThemes/purpleTheme.png" alt="Purple Theme" />
						</label>
						<label class="radio">
							<input type="radio" name="colorEdit" id="themeEditRadios3" value="green"> Green Theme
							<img src="img/tableThemes/greenTheme.png" alt="Green Theme" />
						</label>
						<label class="radio">
							<input type="radio" name="colorEdit" id="themeEditRadios4" value="orange">
							Orange Theme
							<img src="img/tableThemes/orangeTheme.png" alt="Orange Theme" />
						</label><br />
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="editTableSave">Save changes</button>
			</div>
		</div>	

		<!-- Add Motion Chart Modal-->
		<div id="addMotion" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="motionAddModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h3 id="motionAddModalLabel">Add Motion Chart</h3>
			</div>
			<div class="modal-body">
				<label class="tabContentTitle">Select from following columns</label>
				<div id="motionColumns">
					<?php foreach($chart_columns as $col_name) { ?>
					<label class="radio">
						<input type="radio" name="motionColumn[]" value="<?=$col_name?>"> <?php echo $col_name; ?>
					</label>
					<?php } ?>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="addMotionSave" onclick="drawMotion(1,0)">Save changes</button>
			</div>
		</div>
		

		<!-- Edit Motion Chart Modal-->
		<div id="editMotion" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="motionEditModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h3 id="motionEditModalLabel">Edit Motion Chart</h3>
			</div>
			<div class="modal-body">
				<label class="tabContentTitle">Select from following columns</label>
				<div id="motionColumns">
					<?php foreach($chart_columns as $col_name) { ?>
					<label class="radio">
						<input type="radio" name="motionColumnEdit[]" value="<?=$col_name?>"> <?php echo $col_name; ?>
					</label>
					<?php } ?>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="editMotionSave">Save changes</button>
			</div>
		</div>

		
		<!-- Add Map Modal-->
		<div id="addMap" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="mapAddModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h3 id="mapAddModalLabel">Add Map</h3>
			</div>
			<div class="modal-body">
				<label class="tabContentTitle">Location</label>
				<label class="radio">
					<input type="radio" name="maplocation" value="geocode" onchange="setMapValueSelect(1,this.value)">Select Latitude and Longitude for Map
				</label>
				<!--<div id="geocode">
					<select id="geoLat">
						<option value="0">Select Latitude</option>
					  	<?php 
						foreach($excel_columns as $excel_name) { ?>
						  <option value="<?=$excel_name?>"><?php echo $excel_name; ?></option>			
						<?php } ?>
					  </select>
					  <select id="geoLong">
						<option value="0">Select Longitude</option>
					  	<?php 
						foreach($excel_columns as $excel_name) { ?>
						  <option value="<?=$excel_name?>"><?php echo $excel_name; ?></option>			
						<?php } ?>
					</select>
				</div>-->
				<label class="radio">
					<input type="radio" name="maplocation" value="address" onchange="setMapValueSelect(1,this.value)">Select Address for Map
				</label>
				<div id="address" style="display:none;">
					<select id="geoAdd">
						<option value="Location">Location</option>
						<option value="Location">Country</option>
					</select>
				</div><br />
				<!--<div id="address" style="display:none;">
					<select id="geoAdd">
						<option value="0">Select Address</option>
						<?php
						$i=0;
						foreach($sql_columns as $sql_name) { 
						  if(strpos($sql_column_types[$i], "char") !== false || strpos($sql_column_types[$i], "text") !== false ) {
						?>
						  <option value="<?=$sql_name?>"><?php echo $sql_name; ?></option>			
						<?php 				  
						  }	
						  $i++; 
						} ?>
					</select>
				</div><br />-->
				<label class="tabContentTitle">Value</label>
				<select id="mapValueColumn">
					<!--<?php foreach($sql_columns as $name) { ?>
					<option value="<?=$name?>"><?php echo $name; ?></option>
					<?php } ?>-->
				</select><br />
				<label class="tabContentTitle">Tooltip Fields</label>
				<?php foreach($sql_columns as $name) { ?>
				<label class="checkbox"><input type="checkbox" name="mapTooltip[]" value="<?=$name?>" /> <?php echo $name; ?></label>
				<?php } ?>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="addMapSave" onclick="loadMap(1,0)">Save changes</button>
			</div>
		</div>

		<!-- Edit Map Modal-->
		<div id="editMap" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="mapAddModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h3 id="mapAddModalLabel">Edit geo chart</h3>
			</div>
			<div class="modal-body">
				<label class="tabContentTitle">Location</label>
				<label class="radio">
					<input type="radio" name="maplocation3" value="geocode" onchange="setMapValueSelect(3,this.value)">Select Latitude and Longitude for Map
				</label>
				<!--<div id="geocode">
					<select id="geoLat">
						<option value="0">Select Latitude</option>
					  	<?php 
						foreach($excel_columns as $excel_name) { ?>
						  <option value="<?=$excel_name?>"><?php echo $excel_name; ?></option>			
						<?php } ?>
					  </select>
					  <select id="geoLong">
						<option value="0">Select Longitude</option>
					  	<?php 
						foreach($excel_columns as $excel_name) { ?>
						  <option value="<?=$excel_name?>"><?php echo $excel_name; ?></option>			
						<?php } ?>
					</select>
				</div>-->
				<label class="radio">
					<input type="radio" name="maplocation3" value="address" onchange="setMapValueSelect(3,this.value)">Select Address for Map
				</label>
				<div id="address" style="display:none;">
					<select id="geoAdd3">
						<option value="Location">Location</option>
						<option value="Location">Country</option>
					</select>
				</div><br />
				<!--<div id="address" style="display:none;">
					<select id="geoAdd">
						<option value="0">Select Address</option>
						<?php
						$i=0;
						foreach($sql_columns as $sql_name) { 
						  if(strpos($sql_column_types[$i], "char") !== false || strpos($sql_column_types[$i], "text") !== false ) {
						?>
						  <option value="<?=$sql_name?>"><?php echo $sql_name; ?></option>			
						<?php 				  
						  }	
						  $i++; 
						} ?>
					</select>
				</div><br />-->
				<label class="tabContentTitle">Value</label>
				<select id="mapValueColumn3">
					<!--<?php foreach($sql_columns as $name) { ?>
					<option value="<?=$name?>"><?php echo $name; ?></option>
					<?php } ?>-->
				</select><br />
				<label class="tabContentTitle">Tooltip Fields</label>
				<?php foreach($sql_columns as $name) { ?>
				<label class="checkbox"><input type="checkbox" name="mapTooltip3[]" value="<?=$name?>" /> <?php echo $name; ?></label>
				<?php } ?>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="editMapSave">Save changes</button>
			</div>
		</div>
		
		<!-- Add Pie Chart Modal -->
		<div id="addPie" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="pieAddModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h3 id="myPieLabel">Add Pie Chart</h3>
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#pcolumn" data-toggle="tab">Columns</a></li>		  
					<li><a href="#ptype" data-toggle="tab">Aggregation</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="pcolumn">
						<label class="tabContentTitle">Select from following columns</label>
						<?php foreach($chart_columns as $col_name) { ?>
						<label class="radio">
							<input type="radio" name="pieColumn" value="<?=$col_name?>"> <?php echo $col_name; ?>
						</label>
						<?php } ?>						
					</div>
					<div class="tab-pane" id="ptype">
						<label class="tabContentTitle">Select from following aggregation types</label>
						<label class="radio"><input type="radio" value="Count" name="pieType" checked> Count</label>
						<label class="radio"><input type="radio" value="Sum" name="pieType"/> Sum</label>
						<label class="radio"><input type="radio" value="Avg" name="pieType"/> Avg</label>
						<label class="radio"><input type="radio" value="Min" name="pieType"/> Min</label>
						<label class="radio"><input type="radio" value="Max" name="pieType"/> Max</label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="addPieSave" onclick="drawPie(1,0)">Save changes</button>
			</div>
		</div>


        <!-- Edit Pie Chart Modal -->
		<div id="editPie" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="pieEditModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h3 id="myPieLabel">Edit Pie Chart</h3>
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#pcolumnEdit" data-toggle="tab">Column</a></li>		  
					<li><a href="#ptypeEdit" data-toggle="tab">Aggregation</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="pcolumnEdit">
						<label class="tabContentTitle">Select from following columns</label>
						<?php foreach($chart_columns as $col_name) { ?>
						<label class="radio">
							<input type="radio" name="pieColumnEdit" value="<?=$col_name?>"> <?php echo $col_name; ?>
						</label>
						<?php } ?>
					</div>
					<div class="tab-pane" id="ptypeEdit">
						<label class="tabContentTitle">Select from following aggregation types</label>
						<label class="radio"><input type="radio" value="Count" name="pieTypeEdit" checked> Count</label>
						<label class="radio"><input type="radio" value="Sum" name="pieTypeEdit"/> Sum</label>
						<label class="radio"><input type="radio" value="Avg" name="pieTypeEdit"/> Avg</label>
						<label class="radio"><input type="radio" value="Min" name="pieTypeEdit"/> Min</label>
						<label class="radio"><input type="radio" value="Max" name="pieTypeEdit"/> Max</label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="editPieSave">Save changes</button>
			</div>
		</div>


		
        <!--Add Column Chart Modal -->
		<div id="addColumn" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="columnAddModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h3 id="columnAddModalLabel">Add Column Chart</h3>
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#column" data-toggle="tab">Columns</a></li>
					<li><a href="#ctype" data-toggle="tab">Aggregation</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="column">
						<label class="tabContentTitle">Select from following columns</label>
						<?php foreach($chart_columns as $col_name) { ?>
						<label class="radio">
							<input type="radio" name="chartColumn" value="<?=$col_name?>"> <?php echo $col_name; ?>
						</label>
						<?php } ?>
					</div>
					<div class="tab-pane" id="ctype">
						<label class="tabContentTitle">Select from following aggregation types</label>
						<label class="radio"><input type="radio" value="Count" name="columnType" checked>Count</label>
						<label class="radio"><input type="radio" value="Sum" name="columnType" /> Sum</label>
						<label class="radio"><input type="radio" value="Avg" name="columnType" /> Avg</label>
						<label class="radio"><input type="radio" value="Min" name="columnType" /> Min</label>
						<label class="radio"><input type="radio" value="Max" name="columnType" /> Max</label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="addColumnSave" onclick="drawColumn(1,0)">Save changes</button>
			</div>
		</div>

		<!-- Edit Column Chart Modal-->
		<div id="editColumn" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="columnEditModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h3 id="columnEditModalLabel">Edit Column Chart</h3>
			</div>
			<div class="modal-body">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#columnEdit" data-toggle="tab">Column</a></li>
					<li><a href="#ctypeEdit" data-toggle="tab">Aggregation</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="columnEdit">
						<label class="tabContentTitle">Select from following columns</label>
						<?php foreach($chart_columns as $col_name) { ?>
						<label class="radio">
							<input type="radio" name="chartColumnEdit" value="<?=$col_name?>"> <?php echo $col_name; ?>
						</label>
						<?php } ?>
					</div>
					<div class="tab-pane" id="ctypeEdit">
						<label class="tabContentTitle">Select from following aggregation types</label>
						<label class="radio"><input type="radio" value="Count" name="columnTypeEdit" checked>Count</label>
						<label class="radio"><input type="radio" value="Sum" name="columnTypeEdit" /> Sum</label>
						<label class="radio"><input type="radio" value="Avg" name="columnTypeEdit" /> Avg</label>
						<label class="radio"><input type="radio" value="Min" name="columnTypeEdit" /> Min</label>
						<label class="radio"><input type="radio" value="Max" name="columnTypeEdit" /> Max</label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="editColumnSave">Save changes</button>
			</div>
		</div>


		<!-- Add Combo Chart Modal-->
		<div id="addCombo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="comboAddModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h3 id="comboAddModalLabel">Add Combo Chart</h3>
			</div>
			<div class="modal-body">
				<div class="tab-content">
					<div class="tab-pane active" id="combocolumn">
						<label class="tabContentTitle">Select from following columns</label>
						<?php foreach($chart_columns as $col_name) { ?>
						<label class="radio">
							<input type="radio" name="comboColumn" value="<?=$col_name?>"> <?php echo $col_name; ?>
						</label>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="addComboSave" onclick="drawCombo(1,0)">Save changes</button>
			</div>
		</div>


		<!-- Edit Combo Chart Modal-->
		<div id="editCombo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="comboEditModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
				<h3 id="comboEditModalLabel">Edit Combo Chart</h3>
			</div>
			<div class="modal-body">
				<div class="tab-content">
					<div class="tab-pane active" id="combocolumnEdit">
						<label class="tabContentTitle">Select from following columns</label>
						<?php foreach($chart_columns as $col_name) { ?>
						<label class="radio">
							<input type="radio" name="comboColumnEdit" value="<?=$col_name?>"> <?php echo $col_name; ?>
						</label>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="editComboSave">Save changes</button>
			</div>
		</div>


		<!-- Chart Results -->
		<div class="container">
			<div class="chart-area">
				<!-- Divs for saved tables -->
				<?php foreach($visGadgets as $vis) {
					if($vis->type=='table'){
				?>
				<div name="tableDivs" class="gadget" id=<?=$vis->vid?> style="top: <?=$vis->top?>; left:<?=$vis->left?>; width:<?=$vis->width?>; height:<?=$vis->height?>" type="<?=$vis->type?>">
					<div class="gadget-header"><?php echo $vis->type." ".$vis->vid;?>
						<div class="gadget-close"><i class="icon-remove"></i></div>
						<div class="gadget-edit edit-table"><a href="#editTable" data-toggle="modal"><i class="icon-edit"></i></a></div>
					</div>
					<input type="hidden" id="setting<?=$vis->vid?>" value="<?=$vis->setting?>" />
					<div class="gadget-content">
						<div id="tableControl<?=$vis->vid?>" style="width:100%; visibility:hidden;">
							<select id="pages<?=$vis->vid?>" onchange="changePage(this.value,<?=$vis->vid?>)" style="width: 100px; margin-top: 10px;"></select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="button" id="prePage<?=$vis->vid?>" class="btn" value="Previous" onclick="changePage('previous',<?=$vis->vid?>)" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="button" id="nextPage<?=$vis->vid?>" class="btn" value="Next" onclick="changePage('next',<?=$vis->vid?>)" />
						</div>
						<div class="test2" id="tableResult<?=$vis->vid?>" style="width:100%;" onload="loadData(2,<?=$vis->vid?>);"></div>
					</div>
					<script>
						$("#tableResult<?=$vis->vid?>").height($("#<?=$vis->vid?>").height() - 100);
					</script>
				</div>
								
				<!-- Divs for saved geo charts -->
				<?php } else if($vis->type=='map'){ ?>
	    		<div name="mapDivs" class="gadget" id=<?=$vis->vid?> style="top: <?=$vis->top?>; left:<?=$vis->left?>; width:<?=$vis->width?>; height:<?=$vis->height?>" type="<?=$vis->type?>">			
	    			<div class="gadget-header">Geo Chart <?php echo $vis->vid; ?>
	    				<div class="gadget-close"><i class="icon-remove"></i></div>
	    				<div class="gadget-edit edit-map"><a href="#editMap" data-toggle="modal"><i class="icon-edit"></i></a></div>
	    			</div>
	    			<input type="hidden" id="setting<?=$vis->vid?>" value="<?=$vis->setting?>" />
					<div class="gadget-content">
						<div id="mapResult<?=$vis->vid?>" style="width:100%;"></div>
					</div>
					<script>
						loadMap(2,<?=$vis->vid?>);
						$("#mapResult<?=$vis->vid?>").height($("#<?=$vis->vid?>").height() - $(".gadget-header").height() - 20);
					</script>
				</div>
						
				<!-- Divs for saved motion charts -->
				<?php } else if($vis->type=='motion'){ ?>
				<div name="motionDivs" class="gadget" id="<?=$vis->vid?>" style="top: <?=$vis->top?>; left:<?=$vis->left?>; width:<?=$vis->width?>; height:<?=$vis->height?>" type="<?=$vis->type?>">
					<div class="gadget-header"><?php echo $vis->type." ".$vis->vid;?>
						<div class="gadget-close"><i class="icon-remove"></i></div>
						<div class="gadget-edit edit-motion"><a href="#editMotion" data-toggle="modal"><i class="icon-edit"></i></a></div>
					</div>
					<input type="hidden" id="setting<?=$vis->vid?>" value="<?=$vis->setting?>" />
					<div class="gadget-content">
						<div id="motionResult<?=$vis->vid?>" style="width:100%"></div>
					</div>				
				</div>
				
				<!-- Div for Column Chart Result -->
				<?php } else if($vis->type=='column'){ ?>
				<div name="columnDivs" id="<?=$vis->vid?>" class="gadget" style="top: <?=$vis->top?>; left:<?=$vis->left?>; width:<?=$vis->width?>; height:<?=$vis->height?>" type="<?=$vis->type?>">
					<div class="gadget-header"><?php echo $vis->type." chart ".$vis->vid;?>
						<div class="gadget-close"><i class="icon-remove"></i></div> 
						<div class="gadget-edit edit-column"><a href="#editColumn" data-toggle="modal"><i class="icon-edit"></i></a></div>	
					</div>
					<input type="hidden" id="setting<?=$vis->vid?>" value="<?=$vis->setting?>" />
					<div class="gadget-content">
						<div id="columnResult<?=$vis->vid?>" style="width:100%"></div>
					</div>
					<script>
						$("#columnResult<?=$vis->vid?>").height($("#<?=$vis->vid?>").height() - $(".gadget-header").height() - 20);
					</script>					
				</div>

				<!-- Divs for saved pie charts -->
				<?php } else if($vis->type=='pie'){ ?>
				<div name="pieDivs" class="gadget" id="<?=$vis->vid?>" style="top: <?=$vis->top?>; left:<?=$vis->left?>; width:<?=$vis->width?>; height:<?=$vis->height?>" type="<?=$vis->type?>">
					<div class="gadget-header"><?php echo $vis->type." ".$vis->vid;?>
						<div class="gadget-close"><i class="icon-remove"></i></div>
						<div class="gadget-edit edit-pie"><a href="#editPie" data-toggle="modal"><i class="icon-edit"></i></a></div>
					</div>
					<input type="hidden" id="setting<?=$vis->vid?>" value="<?=$vis->setting?>" />
					<div class="gadget-content">
						<div id="pieResult<?=$vis->vid?>" style="width:100%"></div>
						<script>
							$("#pieResult<?=$vis->vid?>").height($("#<?=$vis->vid?>").height() - 100);
						</script>
					</div>				
				</div>

				<!-- Divs for saved combo charts -->
				<?php } else if($vis->type=='combo'){ ?>
				<div name="comboDivs" class="gadget" id="<?=$vis->vid?>" style="top: <?=$vis->top?>; left:<?=$vis->left?>; width:<?=$vis->width?>; height:<?=$vis->height?>" type="<?=$vis->type?>">
					<div class="gadget-header"><?php echo $vis->type." ".$vis->vid;?>
						<div class="gadget-close"><i class="icon-remove"></i></div>
						<div class="gadget-edit edit-combo"><a href="#editCombo" data-toggle="modal"><i class="icon-edit"></i></a></div>
					</div>
					<input type="hidden" id="setting<?=$vis->vid?>" value="<?=$vis->setting?>" />
					<div class="gadget-content">
						<div id="comboResult<?=$vis->vid?>" style="width:100%"></div>
						<script>
							$("#comboResult<?=$vis->vid?>").height($("#<?=$vis->vid?>").height() - 100);
						</script>
					</div>				
				</div>				
				<?php }
					}//end of foreach
				?>

				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div> <!-- /container -->
	</body>
</html>