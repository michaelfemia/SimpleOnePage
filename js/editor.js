	var openText=[];var openImage=[]; var openBlockName=[];
	var imageDirectory="img/";
	var triggerElement,currentlyEditing;	
	$(document).ready(function(){	
		//Edit block names
		$("span.navEditItem").click(function(){
			("span click function");
			//Reveal the editing input[text]
			showEditField($(this));
		
			//Get the ID of this block
			var blockEdit=$(this).attr('id');
			var thisBlock=blockEdit.replace('nav','');
			openBlockName.push(thisBlock);
			currentlyEditing='nav'+thisBlock;
			console.log(thisBlock+" pushed to openBlockName");
		});
		
		//ADD A NEW PAGE BLOCK
		$("#addNewBlock").click(function(){
			console.log("addNewBlockClicked");
			var newBlockName=$("#newBlockName").val();
			if(newBlockName.length>3){
				addNewBlock(newBlockName);
			}
			$("#newBlockName").val('');
		});
		
		//ADD NEW ELEMENTS
		$(".newBlockElement").click(function(){
			var thisBlockID=$(this).attr('value');
			var newType=$(this).siblings("select").children("option:selected").val();
			var postFormData=new FormData();
				postFormData.append("thisBlockID",thisBlockID);
				postFormData.append("newType",newType);
				postFormData.append("newBlockElement","newBlockElement");
			$.ajax({
				xhr: function(){
				   var xhr = new window.XMLHttpRequest();
				   return xhr;
				 },
				url : "js/AJAX.php",
				type: "POST",
				data : postFormData,
				dataType : 'json',
				processData: false,
				contentType: false,
				success: function(data, textStatus, jqXHR){
					var blockWrapper=$("#elementAdditionDiv"+thisBlockID);
					blockWrapper.before(data['editingNode']);
					if(typeof data['dropZoneID']!== 'undefined'){
						$("#"+data['dropZoneID']).dropzone();
					}
				}
			});
		});
		
		//OPEN AND CLOSE EDITING FIELDS
		$(document).mouseup(function(event){
			var clickTarget=event.target.id;
			var targetType=event.target.nodeName;
			if((currentlyEditing!==undefined)&&(currentlyEditing.length>0)){
				var editor="edit"+currentlyEditing;
				if(editor!==clickTarget){
					processChangedInputs();
					currentlyEditing="";
					if(targetType=="H2" || targetType=="P"){
						if(clickTarget.length>0){
							showEditField($("#"+clickTarget));
						}
					}
				}
			}
			if(clickTarget.length>0){
				if($("#homepageTitleWrap,#specialNewsWrap,.editingNode,#contactMeta,#topLogo").find("#"+clickTarget).length>0){
					showEditField($("#"+clickTarget));
				}
			}
		});
		
		toggleEditSection("h1");
		toggleEditSection("h3.htmlType");
	});
	$(document).keypress(function(e){
		if(e.which == 13) {
			processChangedInputs();		
		}
	});
	
	//Process Changes
	function processChangedInputs(){
		var blockParent="no";
		if(openText.length>0){
			openText.forEach(function(entry) {
				if($('#contentWrapper').has("#"+entry).length>0){
					blockParent="block";
					console.log(entry+" is blockChild? "+blockParent);
				}
				if($('.imageArticleLinkEdit').has("#"+entry).length>0){
					blockParent="galleryImage";
				}
				if($('.table').has("#"+entry).length>0){
					blockParent="table";
				}
				modifyContent(entry,"text",blockParent);
			});
			openText.length=0;
		}	
		if(openImage.length>0){
			openImage.forEach(function(entry) {
				var blockParent="no";
				if($('#contentWrapper').has("#"+entry).length>0){
					blockParent="block";
					console.log(entry+" is blockChild? "+blockParent);
				}
				modifyContent(entry,"image",blockParent);
			});
			openImage.length=0;
		}
		if(openBlockName.length>0){
			openBlockName.forEach(function(entry) {
				updateBlockName(entry);
			});
			openBlockName.length=0;
		}
	}
	function showEditField(trigger){
		//Click to reveal editing worksheets for 
		//individual content blocks
		var element=trigger.attr('id');
		if($("#"+element).hasClass("cms")){}
		else{
			//If there is a corresponding edit node, display it.
			var editField="#edit"+element;
			if($(editField).length>0){
				$(editField).show();
				trigger.hide();
				
				//Page block/nav element editing is handled separately
				if(!$("#"+element).hasClass("navEditItem")){
					if($.inArray(element, openText)!==-1){}
					else if(element !== undefined){
						//Add this ID to the editing cue
						openText.push(element);
						currentlyEditing=element;
					}
				}
			}
		}
	}
	function simpleAJAX(postValues){
		var postFormData=new FormData();
		for(var index in postValues) {
		  postFormData.append(index,postValues[index]);
		}
		$.ajax({
			xhr: function(){
			   var xhr = new window.XMLHttpRequest();
			   return xhr;
			 },
			url : "js/AJAX.php",
			type: "POST",
			data : postFormData,
			dataType : 'json',
			processData: false,
			contentType: false,
			success: function(data, textStatus, jqXHR){}
		});
	}
	//TOGGLE NODES
	function toggleEditSection(triggerClass){		
		$(triggerClass).click(function(){
			var trigger=$(this);
			if(trigger.hasClass("closed")){
				trigger.removeClass("closed");
				trigger.siblings().each(function(){
					if($(this).hasClass("cms")){}
					else{$(this).show();}
				});
			}
			else{
				trigger.addClass("closed");
				trigger.siblings().hide();
			}
		});
	}
	
	
	//Insert New Elements
	function addNewBlock(newBlockName){				
		//Create an ID-- lowercase with letters only.
		var lc= newBlockName.toLowerCase();
		var newBlockDivID=lc.replace(/[^a-zA-Z]+/g, '');
		
		//Establish the rank 
		if($(".navAdjustment").length>0){
			var lastBlockRank=$(".navAdjustment:last").attr('id');
			var number=parseInt(lastBlockRank.replace("adjust",''));
			var newBlockRankID=number+1;
		}
		//If there are no other blocks, this is Block #1
		if($(".navAdjustment").length<1){
			var newBlockRankID=1;
		}
		
		//AJAX Call
		var postFormData=new FormData();
			postFormData.append("addNewBlock","addNewBlock");
			postFormData.append("blockName",newBlockName);
			postFormData.append("blockLink",newBlockDivID);
		$.ajax({
			xhr: function(){
			   var xhr = new window.XMLHttpRequest();
			   return xhr;
			 },
			url : "js/AJAX.php",
			type: "POST",
			data : postFormData,
			dataType : 'json',
			processData: false,
			contentType: false,
			success: function(data, textStatus, jqXHR){
				//Update the ID of the DIV, just to be safe.
				var thisBlockID=data['thisBlockID'];
				
				//CREATE A NEW NAVIGATION EDITING NODE
				var newBlockNavEditNode='<div id="blockAdjust'+thisBlockID+'">';
					newBlockNavEditNode+='<button class="deleteBlock" onclick="deleteBlock('+"'"+thisBlockID+"','"+newBlockDivID+"'"+')"><img src="img/icons/trash.png"></button>';
					newBlockNavEditNode+='<button class="rankAdjust" onClick="navRankAdjust('+"'"+thisBlockID+"','"+newBlockDivID+"','down'"+')">';
					newBlockNavEditNode+='<img src="img/icons/downarrow.png"></button>';
					newBlockNavEditNode+='<button class="rankAdjust" onClick="navRankAdjust('+"'"+thisBlockID+"','"+newBlockDivID+"','up'"+')">';
					newBlockNavEditNode+='<img src="img/icons/uparrow.png"></button>';
					newBlockNavEditNode+='<input class="cms nav" id="editnav'+thisBlockID+'" type="text" value="'+newBlockName+'">';
					newBlockNavEditNode+='<span style="color:#ffffff;" id="nav'+thisBlockID+'" class="navEditItem">'+newBlockName+'</span>';
					newBlockNavEditNode+='</div>';
				
				//DIV TO ADJUST RANK -- POPULATED WITH OTHER EDIT OPTIONS (newBlockNavEditNode)
				$('<div/>', {
					class:'navAdjustment',
					id: "adjust"+newBlockRankID,
					html:newBlockNavEditNode
				}).insertBefore('#newBlockAdditionContainer');
					
				//CREATE A NEW PAGE BLOCK
				$('<div/>', {
					id:'block'+thisBlockID,
					class:'pageBlock',
				}).appendTo('#contentWrapper');

				//Populate with Editing Nodes
				$('<div/>', {
					id:newBlockDivID,
					class:'department',
					html:data['editingNode']
				}).appendTo('#block'+thisBlockID);
					
				//Travel to the new section
				$("html, body").animate({scrollTop: $('#'+newBlockDivID).offset().top-navOffset }, 500);
				$("#navelements").fadeOut();
			}
		});
	}
	function uploadImage(a){
		var thumbnailContainer=$("#previewImg_"+a);
		var imageFile=$('#file'+a)[0].files[0];
		var imageFilename=imageFile.name;	
		uploadDirectory="img";
		//Reveal the progress bar and status
		$("#progressBar_"+a).show();
		
		
		//Declare which upload field the PHP script should deal with.
		var postFormData=new FormData();
			postFormData.append("file_"+a,imageFile);
			postFormData.append("uploadField","file_"+a);
			postFormData.append("homepageLead","homepageLead");	
			
		$.ajax({
			xhr: function(){
			   var xhr = new window.XMLHttpRequest();
			   //Upload progress
			   xhr.upload.addEventListener("progress", function(evt){
			   if (evt.lengthComputable) {
					 var percentComplete = parseInt(100*(evt.loaded / evt.total));
					 $("#status_"+a).text(percentComplete + "%");
					 $("#progressBar_"+a).attr("value",percentComplete);
				 }
			   }, false);
			 //Download progress
			   xhr.addEventListener("progress", function(evt){
				 if (evt.lengthComputable) {
				   var percentComplete = evt.loaded / evt.total;
				 }
			   }, false);
			   return xhr;
			 },
			url : "js/AJAX.php",
			type: "POST",
			data : postFormData,
			dataType : 'json',
			processData: false,
			contentType: false,
			error: function (jqXHR, textStatus, errorThrown){		
			},
			success: function(data, textStatus, jqXHR)
			{
				$("#progressBar_"+a).hide();	
				var newThumbnail=data['imgencodedname'];
		
				//Show the image
				var size=320;
				if(a=="homepageLeadImage"){size="1440";}
				var newFilepath=uploadDirectory+'/'+size+'/'+newThumbnail;
				$("#"+a).css('background-image','url('+newFilepath+')');
				$("#"+a).css('background-size','cover');
				$("#"+a).show();	
				$("#edit"+a).val(newThumbnail);
				
				var blockParent="no";
				if($('#contentWrapper').has("#"+a).length>0){
					blockParent="block";
					console.log(entry+" is blockChild? "+blockParent);
				}
				
				//Update the CMS
				modifyContent(a,"image",blockParent);
			}
		});
	}
	function addTableRow(table){
		var postFormData=new FormData();
			postFormData.append("newTableRow",table);
		$.ajax({
			xhr: function(){
			   var xhr = new window.XMLHttpRequest();
			   return xhr;
			 },
			url : "js/AJAX.php",
			type: "POST",
			data : postFormData,
			dataType : 'json',
			processData: false,
			contentType: false,
			success: function(data, textStatus, jqXHR){
				var editingButton=$("#newRow"+table);
				editingButton.before(data['newRow']);
			}
		});
	}
	
	//Delete
	function deleteBlock(block,containerID){
		var pageName=$("#nav"+block).text();
		var message="Are you sure you want to completely delete the entire ";
		message+=pageName+" section of the page?";
		if (confirm(message)) {
			console.log("Removing Block #"+block);
			
			//Remove the div from the nav bar
			$("#nav"+block).closest(".navAdjustment").remove();
			
			//Remove the HTML div from the page
			$("#block"+block).remove();
			
			//Update rankings for remaining items
			var remainingItems=$(".navAdjustment").length;
			var r=1;
			$(".navAdjustment").each(function(){
				$(this).attr('id','adjust'+r);
				r++;
			});
			var postValues={
			  "delete" : "block",
			  "blockID" : block
			};
			simpleAJAX(postValues);
		} else {}
	}
	function deleteElement(type,id,node){
		var editingNode=$("#"+node);
		var blockEditor=editingNode.closest(".pageBlock").attr('id');
		var blockID=blockEditor.replace('block','');
		if(type=="blockElement"){		
			var postValues={
			  "delete" : type,
			  "elementID" : id,
			  "blockID":blockID
			};
		}
		if($(editingNode).hasClass("imageGalleryEditor")){type="galleryElement";}
		if(type=="tableRow"||type=="galleryElement"){	
			var blockElementID=$("#"+node).closest(".editingNode").attr('id').replace('editingNode','');
			var postValues={
				"delete":type,
				"blockElementID":blockElementID,
				"elementID":id
			};
		}
		editingNode.remove();
		simpleAJAX(postValues);
	}
	
	//Update Value
	function modifyContent(id,type,block){
		var postFormData=new FormData();
		var editField=$("#edit"+id);
		var nameField=$("#"+id);
		var contentValue=editField.val();		
		editField.hide();
		if(type=="text"){
			nameField.text(contentValue);
			$("#"+id).text(contentValue);
			nameField.show();
		}
		if(block=="galleryImage"){
			var entryClasses=$("#"+id).closest("div.editor").children("div").attr('class').replace('Edit','');
			var split=entryClasses.split(" ");
			var entryClass=split[0];
			id=id.replace(entryClass,'');	
		}
		var postValues={
		  "block":block,
		  "cms":"cms",
		  "HTMLID":id,
		  "contentValue":contentValue
		};
		simpleAJAX(postValues);
	}
	function updateBlockName(entry){
		console.log("updateBlockName: "+entry);
		var blockID=entry;
		var blockName=$("#editnav"+blockID).val();
		$("#editnav"+blockID).hide();
		$("#nav"+blockID).text(blockName);
		$("#nav"+blockID).show();
		var postValues={
		  "updateBlockName" : "updateBlockName",
		  "blockID" : blockID,
		  "blockName":blockName
		};
		simpleAJAX(postValues);
	}
	
	//Reorder
	function navRankAdjust(blockID,pageBlockID,action){
		var triggerRank=$("#blockAdjust"+blockID).closest('div.navAdjustment');
		var triggerRankID=triggerRank.attr('id');
			triggerRank=parseInt(triggerRankID.replace('adjust',''));		
		var blockCount=$("#navelements").children(".navAdjustment").length;
		var blockContainer=pageBlockID; //HTML-ID of the content block 
		var target,proceed,targetContent;
	
		if((action=="up")&&(triggerRank>1)){
			target=triggerRank-1;proceed="yes";
			$("#adjust"+triggerRank).insertBefore($("#adjust"+target));
			targetContent=$("#block"+blockID).prev()
			$("#block"+blockID).insertBefore(targetContent);
		}
		if((action=="down")&&(triggerRank<blockCount)){
			target=triggerRank+1;proceed="yes";
			$("#adjust"+triggerRank).insertAfter($("#adjust"+target));
			targetContent=$("#block"+blockID).next()
			$("#block"+blockID).insertAfter(targetContent);
		}
		if(proceed=="yes"){
			$("#adjust"+target).attr('id',"a"+target);
			$("#adjust"+triggerRank).attr('id',"adjust"+target);
			$("#a"+target).attr('id','adjust'+triggerRank);
		}
		
		var postValues={
		  "reorder" : "navRank",
		  "direction" : action,
		  "triggerRank":blockID
		};
		simpleAJAX(postValues);
	}
	function shift(type,node,direction){
		var trigger,siblingClass,parentBlock,blockElement;
		trigger="#editingNode"+node;
		siblingClass="editingNode";
		parentBlock=$(trigger).closest(".pageBlock").attr('id');
		blockElement=node;
		if(type!=="blockElement"){
			trigger="#"+node;
			parentBlock=$(trigger).closest("div."+type).attr('id');		
			if($(trigger).hasClass("tableRowEdit")){
				siblingClass="tableRowEdit";
				parentBlock=$(trigger).closest(".table").attr('id');
				blockElement=parentBlock.replace('table','');
			}
			if($(trigger).hasClass("imageGalleryEditor")){
				siblingClass=type+"Edit";
				parentBlock=$(trigger).closest("div."+type).attr('id');
				blockElement=parentBlock.replace(type,'');
				type="galleryElement";
			}
			
		}
		var triggerRank=$("#"+parentBlock).find("div."+siblingClass).index($(trigger));
		
		var numberSiblings=$("#"+parentBlock).find("div."+siblingClass).length;
		if(((triggerRank>0) && (direction=="up"))||((triggerRank<numberSiblings)&&(direction=="down"))){
			if(direction=="up"){
				var target=triggerRank-1;
				var targetID=$("#"+parentBlock).find("div."+siblingClass+":eq("+target+")").attr('id');
				$(trigger).insertBefore($("#"+targetID));
			}
			if(direction=="down"){
				var target=triggerRank+1;
				var targetID=$("#"+parentBlock).find("div."+siblingClass+":eq("+target+")").attr('id');
				$(trigger).insertAfter($("#"+targetID));
			}
			triggerRank=triggerRank+1;
			var postValues={
			  "reorder" : type,
			  "direction" : direction,
			  "triggerRank":triggerRank,
			  "blockElement":blockElement
			};
			simpleAJAX(postValues);
		}
	}
	
	//Other UI
	