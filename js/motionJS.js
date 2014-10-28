google.load('visualization', '1', {'packages':['motionchart']});
//google.setOnLoadCallback(drawMotion);

var motiondata;

function loadMotions() {
	motions = document.getElementsByName("motionDivs");
	gadgetIDs = new Array();
    for(motionNum=0 ; motionNum<motions.length ; motionNum++){
        theMotion = motions[motionNum];
        gadgetIDs.push(theMotion.id);
        drawMotion(2,gadgetIDs[motionNum]);
    }
}
/****************
type
1: initial creation of motion chart
2: reload motion chart from database
3: edit existing motion
*****************/
function drawMotion(type, vid) {
	gadgetID = vid;
	titleNo = $('#titleNo').val();  
	if(type == 1){ //initial creation of motion chart
		createNewMotion();
		column = $('input:radio[name="motionColumn[]"]:checked').val();
		if(column == "") {
			column = "Households";
		}
		settings = column;

		$('#setting' + gadgetID).val(settings);	

	}//end of type 1
	else if(type == 2) {//reload motion chart from database
		settings = $('#setting' + gadgetID).val();
		column = settings;
	}//end of type 2
	else if(type == 3) {//edit existing motion chart
		column = $('input:radio[name="motionColumnEdit[]"]:checked').val();
		settings = column;
		$('#setting' + gadgetID).val(settings);
	}

	data = new google.visualization.DataTable();
	data.addColumn('string', 'Location');
	data.addColumn('number', 'Date');
	data.addColumn('number', column);
	$.ajax({
		type: 'POST',
		url: "getMotion.php",
		data: {'titleNo':titleNo},
		success: function(JSON_Response){
			var JSONResponse = JSON_Response;//start , end , dname , location , avg value			
			rowNum = 0;
			for(i=0; JSONResponse[i]!=null; i++){
				if(JSONResponse[i].Dname == column) {
					data.addRow();
					data.setCell(rowNum,0,String(JSONResponse[i].Location));
					startDate = String(JSONResponse[i].Start);
					year = startDate.substring(0,4);
					data.setCell(rowNum,1,parseInt(year));
					//data.setCell(i,2,"haha");
					data.setCell(rowNum,2,parseFloat(JSONResponse[i].Value));
					rowNum++;
				}		  
			}
			$("#motionResult" + gadgetID).height($("#" + gadgetID).height() - $(".gadget-header").height() - 20);
			motiondata = data;
			generateMotion(gadgetID);
		},
		dataType:'json',
		async:false
	});
	
	$("div[name='motionDivs']").resize(function() {
		tempid = $(this).attr("id")
		$("#motionResult" + tempid).height($("#" + tempid).height() - $(".gadget-header").height() - 20);
		generateMotion(tempid);
	})
	
	if(type == 1) {
		$('#addMotion').modal('hide');		
	}
	else if(type == 3){
		$('#editMotion').modal('hide');		
	}
}

function generateMotion(a) {
	var chart = new google.visualization.MotionChart(document.getElementById('motionResult'+gadgetID));
	//chart.draw(data, {width: 600, height:300});	
	chart.draw(motiondata, {width: "100%", height:"100%"});
}
   
function createNewMotion() {
	var d = new Date();
	var ranNum = 1 + Math.floor(Math.random() * 100);
	gadgetID = d.getTime() + ranNum + "";

	var gadget = "<div name='motionDivs' class='gadget' id='" + gadgetID + "' style='top: 30px; left:30px; width:500px; height:320px' type='motion'>";
	gadget += "<div class='gadget-header'>Motion Chart " + gadgetID;
    gadget += "<div class='gadget-close'><i class='icon-remove'></i></div>"
	gadget += "<div class='gadget-edit edit-motion'><a href='#editMotion' data-toggle='modal'><i class='icon-edit'></i></a></div></div>";
	gadget += "<input type='hidden' id='setting" + gadgetID + "' value='' />";
	gadget += "<div class='gadget-content'>";
	gadget += "<div id='motionResult" + gadgetID + "' style='width:100%'></div></div></div>";

	$('.chart-area').append(gadget);
	$( ".gadget" )
		.draggable({ handle: ".gadget-header" })
		.resizable();
	
	$(".gadget-close").click(function() {	
		$(this).parent().parent().hide();
	})	

}