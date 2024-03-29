google.load('visualization', '1.0', {'packages':['corechart']});
//google.setOnLoadCallback(drawColumn);

var columndata;

function loadColumns() {
	columns = document.getElementsByName("columnDivs");
	gadgetIDs = new Array();
    for(columnNum=0 ; columnNum<columns.length ; columnNum++){
        theColumn = columns[columnNum];
		gadgetIDs.push(theColumn.id);
        drawColumn(2,gadgetIDs[columnNum]);
    }
}

/****************
type
1: initial creation of column chart
2: reload column chart from database
3: edit existing column chart
*****************/
function drawColumn(type, vid){
	gadgetID = vid;
	titleNo = $('#titleNo').val();
	if(type == 1) { //initial creation of column chart
		createNewColumn();
		chartColumn = $('input:radio[name="chartColumn"]:checked').val();
		columnType = $('input:radio[name="columnType"]:checked').val();	
		settings = chartColumn + ";" + columnType + ";";
		$('#setting' + gadgetID).val(settings);	
	}
	else if(type == 2) { //reload column chart from database
		settings = $('#setting' + gadgetID).val();
		var n = settings.split(";");
		chartColumn = n[0];
		columnType = n[1];
	}
	else if(type == 3) { //edit existing column chart
		chartColumn = $('input:radio[name="chartColumnEdit"]:checked').val();
		columnType = $('input:radio[name="columnTypeEdit"]:checked').val();	
		settings = chartColumn + ";" + columnType + ";";
		$('#setting' + gadgetID).val(settings);		
	}

	$.ajax({
		type: 'POST',
		url: "getColumnchart.php",
		data: {'chartColumn':chartColumn,'columnType':columnType,'titleNo':titleNo},
		success: function(JSON_Response){

			var JSONResponse = JSON_Response;
			//document.getElementById('pieResult').innerHTML = JSON.stringify(JSONResponse);
			data = new google.visualization.DataTable();
			data.addColumn('string','Location');
			/*
			if(columnType == 'Count'){
				data.addColumn('number','Amount');
			}
			else{
				data.addColumn('number','Value');
			}
			*/
			data.addColumn('number',columnType);
			for(i=0 ; JSONResponse[i]!=null ; i++){
				data.addRow();
				data.setCell(i,0,String(JSONResponse[i].Location));
				if(columnType == 'Count'){
					data.setCell(i,1,parseInt(String(JSONResponse[i].Amount)));
				}
				else{
					data.setCell(i,1,parseFloat(String(JSONResponse[i].Value)));
				} 
			}
			columndata = data;
			//alert(gadgetID);
			$("#columnResult" + gadgetID).height($("#" + gadgetID).height() - $(".gadget-header").height() - 20);			
			generateColumn(gadgetID);
		},
		dataType: 'json',
		async:false
	});
    //$("#" + gadgetID).resize(function() {
	$("div[name='columnDivs']").resize(function() {
		tempid = $(this).attr("id");
        $("#columnResult" + tempid).height($("#" + tempid).height() - $(".gadget-header").height() - 20);
        generateColumn(tempid);
    });		
	
	
	if(type == 1) {
		$('#addColumn').modal('hide');
	}
	else if(type ==3) {
		$('#editColumn').modal('hide');
	}
}

function generateColumn(a) {
	var options = {
		title: 'Column Chart for ' + chartColumn,
		//hAxis: {title: 'Location', titleTextStyle: {color: 'red'}},
		width: "100%",
		height:"90%"
	};
	var chart = new google.visualization.ColumnChart(document.getElementById('columnResult'+a));
	chart.draw(columndata,options);
}

function createNewColumn() {
	var d = new Date();
	var ranNum = 1 + Math.floor(Math.random() * 100);
	gadgetID = d.getTime() + ranNum + "";
	
	var gadget = "<div name='columnDivs' id='"+gadgetID+"' class='gadget' style='top: 20px; left:20px; width:500px; height:400px' type='column'>";
	gadget += "<div class='gadget-header'>column chart " + gadgetID;
	gadget += "<div class='gadget-close'><i class='icon-remove'></i></div>";
	gadget += "<div class='gadget-edit edit-column edit-new-column'><a href='#editColumn' data-toggle='modal'><i class='icon-edit'></i></a></div></div>";
	gadget += "<input type='hidden' id='setting"+gadgetID+"' value='' />";
	gadget += "<div class='gadget-content'>";
	gadget += "<div id='columnResult"+gadgetID+"' style='width:100%'></div>";
	gadget += "</div></div>";

	$("#columnResult"+gadgetID).height($("#"+gadgetID).height() - $(".gadget-header").height() - 20);
	
	$('.chart-area').append(gadget);
	$( ".gadget" )
		.draggable({ handle: ".gadget-header" })
		.resizable();
	
	$(".gadget-close").click(function() {	
		$(this).parent().parent().hide();
	})
	$('.edit-column').click(function() {
		//get ID of the gadget user selects to edit
		editGadgetID = $(this).parent().parent().attr('id');
		//old settings of gadget
		var oldSettings = $('#setting'+editGadgetID).val(); 
		var n = oldSettings.split(";");
	
		var oldColumn = n[1];// column
		$("input:radio[name='chartColumnEdit']").each(function(j){
			if($(this).val() == oldColumn) {
				$(this).attr('checked', true);
			}
		});
		
		var oldType = n[2]; //aggregation type
		$("input:radio[name='columnTypeEdit']").each(function(j){
			if($(this).val() == oldType) {
				$(this).attr('checked', true);
			}
		});				
	});	

	//edit motion save
	$('#editColumnSave').click(function() {
		//alert(editGadgetID);
		drawColumn(3,editGadgetID);
	});
	
	/*
	$(".edit-new-column").click(function() {
		var newColumnDiv = $(this).parent().parent();
		$(this).parent().parent().find(".edit-column").trigger("click");
	})*/
}
