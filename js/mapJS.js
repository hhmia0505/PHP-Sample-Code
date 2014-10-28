google.load('visualization', '1', {'packages': ['geochart']});
google.setOnLoadCallback(drawMarkersMap);

var JMapToSend
var dataTable;

mapLocation = "";
mapValue = "";
tooltipStr = "";

gadgetID = 0;

var mapdata;

/****************
type
1: initial creation of geo chart
2: reload geo chart from database
*****************/

function loadMap(type,vid){
    gadgetID = vid;
    titleNo = $("#titleNo").val();
    if(type == 1){
        createNewMap();
        settingStr = "";
        mapTooltip = new Array();

        mapLocation = $('input:radio[name="maplocation"]:checked').val(); //geocode or address
        mapValue = $("#mapValueColumn").val(); // value to decide tooltips' size on map
        if(mapLocation == "address"){
            mapLocation = $("#geoAdd").val();
        }
        $.each($("input[name='mapTooltip[]']:checked"), function(){
            mapTooltip.push($(this).val()); // columns to show in tooltip
            settingStr += "," + $(this).val();
        });

        settingStr = settingStr.substring(1) + ";";
        $('#setting' + gadgetID).val(mapLocation + ";" + mapValue + ";" + settingStr);

        JMapToSend = {'mapLocation':mapLocation,'mapValue':mapValue,'mapTooltip[]':mapTooltip,'titleNo':titleNo};

        if(mapLocation == "geocode"){
            geocodeMap();
        } else{
            addressMap();
        }
        $('#addMap').modal('hide');
    }
    if(type == 2){
        gadgetID = vid;
        settings = $('#setting' + gadgetID).val();
        mapTooltip = new Array();

        var n = settings.split(";");
        mapLocation = n[0];
        mapValue = n[1];
        mapTooltip = n[2].split(",");

        JMapToSend = {'mapLocation':mapLocation,'mapValue':mapValue,'mapTooltip[]':mapTooltip};
        //alert(JSON.stringify(JMapToSend));
        if(mapLocation == "geocode"){
            geocodeMap();
        } else{
            addressMap();
        }
    }
    if(type == 3){
        settingStr = "";
        mapTooltip = new Array();

        mapLocation = $('input:radio[name="maplocation3"]:checked').val(); //geocode or address
        mapValue = $("#mapValueColumn3").val(); // value to decide tooltips' size on map
        if(mapLocation == "address"){
            mapLocation = $("#geoAdd3").val();
        }
        $.each($("input[name='mapTooltip3[]']:checked"), function(){
            mapTooltip.push($(this).val()); // columns to show in tooltip
            settingStr += "," + $(this).val();
        });

        settingStr = settingStr.substring(1) + ";";
        $('#setting' + gadgetID).val(mapLocation + ";" + mapValue + ";" + settingStr);

        JMapToSend = {'mapLocation':mapLocation,'mapValue':mapValue,'mapTooltip[]':mapTooltip};

        if(mapLocation == "geocode"){
            geocodeMap();
        } else{
            addressMap();
        }
        $('#editMap').modal('hide');
    }
    
    //$("#" + gadgetID).resize(function() {
	$("div[name='mapDivs']").resize(function() {
		tempid = $(this).attr("id");
        $("#mapResult" + tempid).height($("#" + tempid).height() - $(".gadget-header").height()-20);
        generateMap(tempid);
    });
}

function geocodeMap(){
    $.ajax({type:'POST', url:"getMap.php", data:JMapToSend, success:function(JMap){

        dataTable = new google.visualization.DataTable();

        dataTable.addColumn('number',"Latitude");
        dataTable.addColumn('number',"Longitude");
        dataTable.addColumn('number',mapValue);
        if(mapTooltip.length != 0){
            dataTable.addColumn({type:'string', role:'tooltip','p': {'html': true}});
        }
        for(i=0 ; JMap[i]!=null ; i++){
            dataTable.addRow();
            dataTable.setCell(i,0,parseFloat(JMap[i].Latitude));
            dataTable.setCell(i,1,parseFloat(JMap[i].Longitude));
            dataTable.setCell(i,2,parseInt(JMap[i].mapValue));
            if(mapTooltip.length != 0){
                for(j=0 ; j<mapTooltip.length ; j++){
                    if(j == 0){
                        tooltipStr = "";
                    }
                    tooltipStr += mapTooltip[j] + ": " + JMap[i][mapTooltip[j]] + "; ";
                }
                dataTable.setCell(i,3,tooltipStr);
            }
        }
		
		mapdata = dataTable;

        generateMap(gadgetID);
    }, dataType:'json', async:false});
}

function addressMap(){
    $.ajax({type:'POST', url:"getMap.php", data:JMapToSend, success:function(JMap){

        dataTable = new google.visualization.DataTable();

        dataTable.addColumn('string',"City");
        dataTable.addColumn('number',mapValue);
        if(mapTooltip.length != 0){
            dataTable.addColumn({type:'string', role:'tooltip','p': {'html': true}});
        }
        for(i=0 ; JMap[i]!=null ; i++){
            dataTable.addRow();
            dataTable.setCell(i,0,String(JMap[i].mapLocation));
            dataTable.setCell(i,1,parseFloat(JMap[i].mapValue));
            if(mapTooltip.length != 0){
                for(j=0 ; j<mapTooltip.length ; j++){
                    if(j == 0){
                        tooltipStr = "";
                    }
                    tooltipStr += mapTooltip[j] + ": " + JMap[i][mapTooltip[j]] + "; ";
                }
                dataTable.setCell(i,2,tooltipStr);
            }
        }
		mapdata = dataTable;
        generateMap(gadgetID);
    }, dataType:'json', async:false});
}

function generateMap(a){
    var options = {
        region:'world',
        displayMode:'markers',
        height:$("#mapResult" + a).height(),
        colorAxis:{colors:['red','purple']},
        tooltip:{isHtml:true} // Use the new HTML tooltip (default SVG one does not support HTML content).
    };
    new google.visualization.GeoChart(document.getElementById('mapResult' + a)).draw(mapdata, options);
}

function setMapValueSelect(type,mapType){
    if(type == 1){
        var select = document.getElementById("mapValueColumn");
    }
    if(type == 3){
        var select = document.getElementById("mapValueColumn3");
    }
    if(select.length != 0){
        for(i=select.length-1 ; i>-1 ; i--){
            select.remove(i);
        }
    }
    if(mapType == "geocode"){
        var option = document.createElement("option");
        option.text = "Eid";
        try{
            select.add(option,select.options[null]); // for IE earlier than version 8
        } catch (e){
            select.add(option,null);
        }
    }
    if(mapType == "address"){
        var option = document.createElement("option");
        option.text = "Value";
        try{
            select.add(option,select.options[null]); // for IE earlier than version 8
        } catch (e){
            select.add(option,null);
        }
    }
}

function createNewMap(){
    var d = new Date();
    var ranNum = 1 + Math.floor(Math.random() * 100);
    gadgetID = d.getTime() + ranNum + "";
    
    var gadget = "<div name='mapDivs' class='gadget' id='" + gadgetID + "' style='top: 20px; left:0px; width:400px; height: 300px' type='map'>"
            + "<div class='gadget-header'>Geo Chart " + gadgetID
            + "<div class='gadget-close'><i class='icon-remove'></i></div>"
            + "<div class='gadget-edit edit-map'><a href='#editMap' data-toggle='modal'><i class='icon-edit'></i></a></div> </div>"
            + "<input type='hidden' id='setting" + gadgetID + "' value='' />"
            + "<div class='gadget-content'>"
            + "<div id='mapResult" + gadgetID + "' style='width:100%;'></div></div></div>";
    $('.chart-area').append(gadget);
    $( ".gadget" )
        .draggable({ handle: ".gadget-header" })
        .resizable();
    $(".gadget-close").click(function() {   
        $(this).parent().parent().hide();
    })

    $("#mapResult" + gadgetID).height($("#" + gadgetID).height() - $(".gadget-header").height());

    //edit geo chart
    $('.edit-map').click(function() {
        //get ID of the gadget user selects to edit
        editGadgetID = $(this).parent().parent().attr('id');
        //alert(editGadgetID);
        /*var oldSettings = $('#setting'+editGadgetID).val(); //old settings of gadget
        oldColumn = oldSettings;
        $("input:radio[name='motionColumnEdit[]']").each(function(j){
            if($(this).val() == oldColumn) {
                $(this).attr('checked', true);
            }
        });*/
    });

    //edit motion save
    $('#editMapSave').click(function() {
        // alert(editGadgetID);
        loadMap(3,editGadgetID);
    });
}