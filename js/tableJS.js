google.load('visualization', '1', {packages: ['table']});

var JSONResponse;
currentPage = 0;
prePage = 0;
nextPage = 0;
perPage = 0;
totalPage = 0;
color = "blue";
display = "sqlStyle";
titleNo = 15;
var dataTable;
gadgetID = 0;

function loadTables(){
    tables = document.getElementsByName("tableDivs");
    gadgetIDs = new Array();
    for(tableNum=0 ; tableNum<tables.length ; tableNum++){
        theTable = tables[tableNum];
        gadgetIDs.push(theTable.id);
        loadData(2,gadgetIDs[tableNum]);
    }
}

/****************
type
1: initial creation of table
2: reload table from database
3: edit existing table
*****************/
function loadData(type, vid){
    gadgetID = vid;
    titleNo = $("#titleNo").val();
    // alert("load" + gadgetID);
	if(type == 1) {//initial creation of table
		// alert("add new table");
        createNewTable();
        settingStr = "";
		tablePages = $('input:radio[name="page"]:checked').val(); // number of turples per page
		color = $('input:radio[name="color"]:checked').val(); // color of the new table
		//titleNo = $('input:hidden[name="titleNo"]').val(); // sid in temporary table in database
		tableColumns = new Array();
		currentPage = $('input:hidden[name="currentPage"]').val();
		display = $('input:radio[name="display"]:checked').val();
        if(display == "sqlStyle"){
            $.each($("input[name='sqlColumn[]']:checked"), function(){
                tableColumns.push($(this).val()); // columns to show
                settingStr += "," + $(this).val();
            });
        }
        else{
            $.each($("input[name='excelColumn[]']:checked"), function(){
                tableColumns.push($(this).val()); // columns to show
                settingStr += "," + $(this).val();
            });
        }
        settingStr = settingStr.substring(1) + ";";
        settingStr += tablePages + ";";
        settingStr += color + ";";
        settingStr += display + ";";
        $('#setting' + gadgetID).val(settingStr);
        // alert($('#setting' + gadgetID).val());
        // alert(tableColumns);
        // get json from getTable.php
        if(display == "sqlStyle"){
            sqlTable("initial");
        }
        if(display == "excelStyle"){
            excelTable("initial");
        }
		$('#addTable').modal('hide');
	}
	else if(type == 2) {//reload created table
		// alert("reload created table");
		gadgetID = vid;
		settings = $('#setting' + gadgetID).val();
		// alert(settings);
		var n = settings.split(";");
		columns = n[0].split(",");
		tableColumns = new Array();
		for(i=0;i<columns.length;i++) {
			tableColumns.push(columns[i]);
		}
		tablePages = n[1];// number of turples per page
		color = n[2];// color of the new table
		display = n[3];// sql style table or excel style table
		currentPage = $('input:hidden[name="currentPage"]').val();
        // get json from getTable.php
        if(display == "sqlStyle"){
            sqlTable("initial");
        }
        if(display == "excelStyle"){
            excelTable("initial");
        }
		$('#addTable').modal('hide');
	}
	else if(type == 3) {//edit table
        settingStr = "";
		tablePages = $('input:radio[name="pageEdit"]:checked').val(); // number of turples per page
		color = $('input:radio[name="colorEdit"]:checked').val(); // color of the new table
		//titleNo = $('input:hidden[name="titleNo"]').val(); // sid in temporary table in database
		tableColumns = new Array();
		currentPage = $('input:hidden[name="currentPage"]').val();
		display = $('input:radio[name="displayEdit"]:checked').val();
		if(display == "sqlStyle"){		
			$.each($("input[name='sqlcolumnEdit[]']:checked"), function(){
				tableColumns.push($(this).val()); // columns to show
				settingStr += "," + $(this).val();
			});
		}
		else {
			$.each($("input[name='excelcolumnEdit[]']:checked"), function(){
				tableColumns.push($(this).val()); // columns to show
				settingStr += "," + $(this).val();
			});		
		}
        settingStr = settingStr.substring(1) + ";";
        settingStr += tablePages + ";";
        settingStr += color + ";";
        settingStr += display + ";";
        $('#setting' + gadgetID).val(settingStr);
        // alert($('#setting' + gadgetID).val());
        // alert(tableColumns);
        // get json from getTable.php
        if(display == "sqlStyle"){
            sqlTable("initial");
        }
        if(display == "excelStyle"){
            excelTable("initial");
        }
		$('#editTable').modal('hide');
	}
    
}

function changePage(page,vid){
    gadgetID = vid;
    titleNo = $("#titleNo").val();
    // alert(vid);
    // alert($('#setting' + gadgetID).val());
    settings = $('#setting' + gadgetID).val();
    // alert(settings);
    var n = settings.split(";");
    columns = n[0].split(",");
    tableColumns = new Array();
    for(i=0 ; i<columns.length ; i++) {
        tableColumns.push(columns[i]);
    }
    tablePages = n[1];// number of turples per page
    color = n[2];// color of the new table
    display = n[3];// origin table or rotated table
    currentPage = $('#pages'+gadgetID).val();
    // alert(gadgetID);
    document.getElementById("prePage" + gadgetID).style.visibility = "visible";
    document.getElementById("nextPage" + gadgetID).style.visibility = "visible";
    if((page != "previous") && (page != "next")){
        currentPage = page;
    }
    if(page == "previous"){
        currentPage = parseInt(currentPage) - 1;
    }
    if(page == "next"){
        currentPage = parseInt(currentPage) + 1;
    }
    if(currentPage == '1')
        document.getElementById("prePage" + gadgetID).style.visibility = "hidden";
    if(currentPage == totalPage)
        document.getElementById("nextPage" + gadgetID).style.visibility = "hidden";
    document.getElementById("pages" + gadgetID).value = currentPage;
    // get json from getTable.php
    if(display == "sqlStyle"){
        sqlTable("change");
    }
    if(display == "excelStyle"){
        excelTable("change");
    }
}

function addPage(){
    //alert("add page" + gadgetID);
    document.getElementById("tableControl" + gadgetID).style.visibility = "visible";
    var pageSelect = document.getElementById("pages" + gadgetID);
    while (pageSelect.hasChildNodes()) {
        pageSelect.removeChild(pageSelect.lastChild);
    }
    for(n=1 ; n<=totalPage ; n++){
        var page = document.createElement("option");
        page.innerHTML = n;
        pageSelect.appendChild(page);
    }
    if(currentPage == '1'){
        document.getElementById("prePage" + gadgetID).style.visibility = "hidden";
    }
    if(currentPage == totalPage){
        document.getElementById("nextPage" + gadgetID).style.visibility = "hidden";
    }
}

function generateTable(){
    // alert("generate" + gadgetID);
    var googleTable = {
        'headerRow': 'header-row',
        'tableRow': 'table-row',
        'oddTableRow': 'odd-table-row-' + color,
        'selectedTableRow': 'selected-table-row-' + color,
        'hoverTableRow': 'hover-table-row',
        'headerCell': 'header-cell-' + color,
        'tableCell': 'table-cell-' + color,
        'rowNumberCell': 'row-number-cell'};
    var options = {'showRowNumber': false, 'allowHtml': true, 'cssClassNames': googleTable};
    var table_data = new google.visualization.Table(document.getElementById('tableResult' + gadgetID));
    table_data.draw(dataTable, options);
	$("#" + gadgetID).resize(function() {
        $("#tableResult" + gadgetID).height($("#" + gadgetID).height() - 100);
        table_data.draw(dataTable, options);
    });
    // alert($('#setting' + gadgetID).val());
}
function sqlTable(eventType){
    $.ajax({type: 'POST',url: "getTable.php",data: {'column[]': tableColumns,'page':tablePages,'currentPage':currentPage,'display':display,'titleNo':titleNo},success: function(JSON_Response){
        totalPage = JSON_Response["Control"]["totalPage"];
        perPage = JSON_Response["Control"]["perPage"];
        currentPage = JSON_Response["Control"]["pageNo"];
        page = "Page" + currentPage;
        JSONResponse = JSON_Response[page];
        dataTable = new google.visualization.DataTable();
        for(each_name in JSONResponse[0]){
            dataTable.addColumn('string',each_name);
        }
        for(i=0 ; JSONResponse[i]!=null ; i++){
            dataTable.addRow();
            j = 0;
            for(each_name in JSONResponse[i]){
                dataTable.setCell(i,j,String(JSONResponse[i][each_name]));
                j++;
            }
        }
        // alert("dataTable" + gadgetID);
        if(eventType == "initial"){
            addPage();
        }
        generateTable();
    },dataType:'json',async:false});
}
function excelTable(eventType){
    $.ajax({type: 'POST',url: "getTable.php",data: {'column[]': tableColumns,'page':tablePages,'currentPage':currentPage,'display':display,'titleNo':titleNo},success: function(JSON_Response){
        totalPage = JSON_Response["Control"]["totalPage"];
        perPage = JSON_Response["Control"]["perPage"];
        currentPage = JSON_Response["Control"]["pageNo"];
        page = "Page" + currentPage;
        JSONResponse = JSON_Response[page];
        dataTable = new google.visualization.DataTable();
        dataTable.addColumn('string','Start Time');
        dataTable.addColumn('string','End Time');
        dataTable.addColumn('string','Location');
        for(i=0 ; tableColumns[i]!=null ; i++){
            dataTable.addColumn('string',tableColumns[i]);
        }
        rowNum = 0;
        for(i=0 ; JSONResponse[i]!=null ; i++){
            dataTable.addRow();
            for(j=0 ; j<tableColumns.length+3 ; j++){
                if(j == 0){
                    dataTable.setCell(rowNum,j,String(JSONResponse[i]["Start"]));
                    continue;
                }
                if(j == 1){
                    dataTable.setCell(rowNum,j,String(JSONResponse[i]["End"]));
                    continue;
                }
                if(j == 2){
                    dataTable.setCell(rowNum,j,String(JSONResponse[i]["Location"]));
                    continue;
                }
                if((j != 0) && (j != 1) && (j != 2)){
                    dataTable.setCell(rowNum,j,String(JSONResponse[i]["Value"]));
                    i++;
                    continue;
                }
                /*dataTable.setCell(rowNum,j,String(JSONResponse[i]["Value"]));
                i++;*/
            }
            i--;
            rowNum++;
        }
		//alert(""+totalPage+" "+perPage+" "+page+" "+JSONResponse.length);
        // alert("1");
        if(eventType == "initial"){
            addPage();
        }
        // alert("2");
        generateTable();
    },dataType:'json',async:false});
}
function createNewTable(){
	var d = new Date();
	var ranNum = 1 + Math.floor(Math.random() * 100);
	gadgetID = d.getTime() + ranNum + "";
	
	var gadget = "<div class='gadget' id='" + gadgetID + "' style='top: 20px; left:0px; width:400px; height: 300px' type='table'>";
	gadget += "<div class='gadget-header'>Table" + gadgetID;
	gadget += "<div class='gadget-close'><i class='icon-remove'></i></div>";
	gadget += "<div class='gadget-edit edit-table'><a href='#editTable' data-toggle='modal'><i class='icon-edit'></i></a></div> </div>";
    gadget += "<input type='hidden' id='setting" + gadgetID + "' value='' />";
	gadget += "<div class='gadget-content'> <div id='tableControl" + gadgetID + "' style='width:100%; visibility:hidden;'>";
	gadget += "<select id='pages" + gadgetID + "' onchange='changePage(this.value," + gadgetID + ")' style='width: 100px; margin-top: 10px;'></select> ";
	gadget += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
	gadget += "<input type='button' id='prePage" + gadgetID + "' class='btn' value='Previous' />";
    // onclick='changePage('previous'," + gadgetID +")'
	gadget += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	gadget += "<input type='button' id='nextPage" + gadgetID + "' class='btn' value='Next' /></div>";
    // onclick='changePage('next'," + gadgetID + ")'
	gadget += "<div class='test2' id='tableResult" + gadgetID + "' style='width:100%;'></div></div></div>";
    // alert(gadget);
	$('.chart-area').append(gadget);
	$( ".gadget" )
		.draggable({ handle: ".gadget-header" })
		.resizable();
	
	$(".gadget-close").click(function() {	
		$(this).parent().parent().hide();
	})
    document.getElementById("prePage" + gadgetID).onclick = function(){changePage('previous',this.id.substring(7))};
    document.getElementById("nextPage" + gadgetID).onclick = function(){changePage('next',this.id.substring(8))};
    $("#tableResult" + gadgetID).height($("#" + gadgetID).height() - 100);
}