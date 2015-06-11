<?
include('../global_functions.php');
$data=array();$data['debug']="";

//Homepage Lead Element CMS (banner image, title, special news, etc)
if(($_POST['cms']=="cms") && ($_POST['block']=="no") ){
	
	$table="tblHomepageManagement";
	$fields=array("fldValue"=>$_POST['contentValue']);
	$condition="fldHTMLID='".$_POST['HTMLID']."'";
	updateQuery($table,$fields,$condition);

	$tileCheck=strpos($_POST['HTMLID'],'tile');
	$nameCheck=strpos($_POST['HTMLID'],'Name');
	if(($tileCheck!==false) && ($nameCheck!==false)){
		$titleField=str_replace("Name","Link",$_POST['HTMLID']);
		$sql2="SELECT fldPageLink FROM tblPageBlocks ";
		$sql2.="WHERE fldPageName='".$_POST['contentValue']."'";
		$r2=$mysqli->query($sql2);
		while($result2=mysqli_fetch_assoc($r2)){
			$fields=array("fldValue"=>"#".$result2['fldPageLink']);
			$condition="fldHTMLID='".$titleField."'";
			updateQuery($table,$fields,$condition);
		}
	}
	$sql="SELECT fldHTMLID,fldValue FROM tblHomepageManagement";
	$r=$mysqli->query($sql);
	$placefillers=array();$newValues=array();
	while($result=mysqli_fetch_assoc($r)){
		$placefillers[]="{".$result['fldHTMLID']."}";
		$newValues[]=$result['fldValue'];
	}
	$template=file_get_contents("../template.php");
	$rewrite=str_replace($placefillers,$newValues,$template);		
	$newpage=stripslashes($rewrite);
	$html_file="../index.php";
	$fp = fopen($html_file,"w"); 
	fwrite($fp, $newpage); 
	fclose($fp);
}

//Image upload handler for Homepage Lead Elements
if(isset($_POST['uploadField'],$_POST['homepageLead'])){
	$field=$_POST['uploadField'];
	$path='../'.$imageDirectory.'/';
	$data['debug']="Upload Field:".$_POST['uploadField'];
	imagickAdaptive($field,$path);
}

//Block text CMS (subheaders, paragraph text)
if(isset($_POST['cms'],$_POST['block'])){
	$elementID=$_POST['HTMLID'];
	$newValue=$_POST['contentValue'];
	
	switch($_POST['block']){
			case "block":
				//What kind of element is it?
				$sql="SELECT pkTypeID,fldTypeName ";
				$sql.="FROM tblBlockElementTypes";	
				$q=$mysqli->query($sql);
				while($r=mysqli_fetch_assoc($q)){
					if(strpos($elementID,$r['fldTypeName'])!==false){
						$type=$r['pkTypeID'];
						$replace=array('ID',$r['fldTypeName']);
						$blockElementID=str_replace($replace,"",$elementID);
						
						if($type==10){
							$hash=unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$newValue.".php"));
							$data['newVimeoThumb']=$hash[0]['thumbnail_large'];
						}
						
						$data['blockUpdateQuery1']=updateQuery("tblBlockElements",array("fldValue"=>$newValue),"pkBlockElementID='".$blockElementID."'");
					}
				}
				break;
			case "table":
				$delimiters=array("row","column");
				$replace=str_replace($delimiters,"-",$elementID);
				$array=explode('-',$replace);
				$fields=array("fldValue"=>$_POST['contentValue']);
				$condition="fldColumn='".$array[2]."' AND fkRowID='".$array[1]."'";
				$data['cellUpdateQuery']=updateQuery("tblTableCells",$fields,$condition);
				break;
			case "galleryImage":
				//Is it a title or a caption?
				if(strpos($elementID,'Title')!==false){$fields=array("fldCaption"=>$_POST['contentValue']);};
				if(strpos($elementID,'Link')!==false){$fields=array("fldLink"=>$_POST['contentValue']);};
				$imageID=str_replace(array("Title","Link"),'',$elementID);
				$condition="fkImageID='".$imageID."'";
				$data['imageMetaUpdateQuery']=updateQuery("tblGalleryBlocks",$fields,$condition);
				break;
	}
}

//Add New Block
if((isset($_POST['addNewBlock'])) && (isset($_POST['blockName']))){
	$newRank=getMax("tblPageBlocks","fldNavRank","1")+1;
	$table="tblPageBlocks";
	$fields=array("fldPageName","fldPageLink","fldNavRank");
	$values=array(htmlentities($_POST['blockName'], ENT_QUOTES),$_POST['blockLink'],$newRank);
	$thisBlockID=insertQuery($table,$fields,$values);
	
	//Default Template: One of each basic element
	$table="tblBlockElements";
	$fields=array("fkBlock","fkElementType","fldPosition","fldValue");
	$values=array($thisBlockID,"1","1",htmlentities($_POST['blockName'], ENT_QUOTES));
	$element['1']=insertQuery($table,$fields,$values);
	$values=array($thisBlockID,"2","2","Description");
	$element['2']=insertQuery($table,$fields,$values);
	$node="";$data['newElements']="";
	foreach($element as $elementID){
		$node.=retrieveBlockElements($elementID,'1');	
	}
	$node.=newElementAdditionButton($thisBlockID);
	$data['editingNode']=$node;
	$data['thisBlockID']=$thisBlockID;
}

//Change Block Name
if(isset($_POST['updateBlockName'])){
	$fields=array("fldPageName"=>htmlentities($_POST['blockName'], ENT_QUOTES));
	$condition="pkPageBlockID='".$_POST['blockID']."'";
	updateQuery("tblPageBlocks",$fields,$condition);
}

//Add New Block Element
if(isset($_POST['newBlockElement'])){	
	$thisBlock=str_replace("block","",$_POST['thisBlockID']);
	$type=$_POST['newType'];
	$fields=array("fkBlock","fkElementType","fldPosition","fldValue");
	$position=getMax("tblBlockElements","fldPosition","fkBlock='".$thisBlock."'")+1;
	$values=array($thisBlock,$type,$position,"Text");
	$newBlockElement=insertQuery("tblBlockElements",$fields,$values);
	$data['editingNode']=retrieveBlockElements($newBlockElement,true);
}

//Dropzone Image Handler--Consolidate with global function
if (    ( isset($_POST['blockID']) )   &&   (   isset($_POST['dropGo']) )   &&   ( !empty($_FILES) )    ) {	
	$data['fired']="Dropzone image handler for block galleries.";
	$inFile=$_FILES['file']['tmp_name'];
	$source_folder="../img/";
	$image = new Imagick();
	$image->readImage($inFile);
	//$image->compositeimage($image, Imagick::COMPOSITE_OVER, 0, 0);
	$image->setImageFormat('jpg');
	
	$version = $image->getVersion();
	$profile = 'http://www.color.org/sRGB_IEC61966-2-1_no_black_scaling.icc';
	
	//SET COLOR PROFILE TO SRGB
	if ((is_array($version) === true) && (array_key_exists('versionString', $version) === true)){
		$version = preg_replace('~ImageMagick ([^-]*).*~', '$1', $version['versionString']);
		if (is_file(sprintf('/usr/share/ImageMagick-%s/config/sRGB.icm', $version)) === true){
			$profile = sprintf('/usr/share/ImageMagick-%s/config/sRGB.icm', $version);
		}
	}
	if (($srgb = file_get_contents($profile)) !== false){
		$image->profileImage('icc', $srgb);
		$image->setImageColorSpace(Imagick::COLORSPACE_SRGB);
	}
	
	//QUALITY & RESOLUTION
	$qual=70;
	$image->setImageCompression(Imagick::COMPRESSION_JPEG);
	$image->setImageCompressionQuality($qual);
	$image->setImageResolution(72,72);
	$image->resampleImage(72,72,imagick::FILTER_UNDEFINED,1);
	$image->resizeImage(1920,0,imagick::FILTER_UNDEFINED,1);
	
	//Should be converting any kind of file to an extension of .jpg
	$thisimagename=($_FILES['file']["name"]);
	$extension=pathinfo($thisimagename, PATHINFO_EXTENSION);
	$toencode= ereg_replace("[^A-Za-z0-9?!]", "",$thisimagename); 
	$lowercase=strtolower($toencode);
	$encoded=rawUrlEncode($lowercase).time().".".$extension;
	
	foreach($image_sizes as $size){
		$width=$size;
		$image->resizeImage($width,0,Imagick::FILTER_LANCZOS,1);
		$outFile=$source_folder.$size.'/'.$encoded;
		$image->writeImage($outFile);	
	}
	$image->destroy();
	
	//Insert into tblImages (Image library for the site)
	$thisImageID=insertQuery("tblImages",array("fldImageName"),array($encoded));
	$blockID=$_POST['blockID'];//Trigger gallery
	
	//Establish its rank
	$sql="SELECT MAX(fldRank) AS currentMax FROM tblGalleryBlocks ";
	$sql.="WHERE fkBlockElementID='".$blockID."'";
	
	$r=$mysqli->query($sql);
	if($r->num_rows>0){
		$max=mysqli_fetch_assoc($r);
		$rank=$max['currentMax']+1;
	}
	if($r->num_rows<1){$rank=1;}
	
	//Should there only be one image in this element?
	$elementTypeFK=singleValueSQL("tblBlockElements","fkElementType","pkBlockElementID='".$blockID."'");
	$contentType=singleValueSQL("tblBlockElementTypes","fldContentType","pkTypeID='".$elementTypeFK."'");
	if($contentType=="3"){
		deleteQuery("tblGalleryBlocks","fkBlockElementID='".$blockID."'");
		$data['removeCurrentImage']=true;
	}

	//Insert the image into the trigger gallery
	$table="tblGalleryBlocks";
	$fields=array("fkBlockElementID","fkImageID","fldRank","fldCaption","fldLink");
	$values=array($blockID,$thisImageID,$rank,"Caption","Link");
	insertQuery($table,$fields,$values);
	
	$data['imageName']=$encoded;
	$data['triggerBlock']=$blockID;
	
	$data['imageID']=$thisImageID;
	$data['editingNode']=formatImage($thisImageID,$blockID,true,"1");
}

//Remove Element & Reorder Remaining Elements
if(isset($_POST['delete'])){
	switch($_POST['delete']){
		case "block":
			$condition="fkBlock='".$_POST['blockID']."'";
			deleteQuery("tblBlockElements",$condition);
			$condition="pkPageBlockID='".$_POST['blockID']."'";
			deleteQuery("tblPageBlocks",$condition);
			reorderAfterDelete("pkPageBlockID","tblPageBlocks","fldNavRank","");
			break;
		case "blockElement":
			$elementID=$_POST['elementID'];
			$condition="pkBlockElementID='".$elementID."'";	
			$reorderConditional="fkBlock='".$_POST['blockID']."'";
			$type=singleValueSQL("tblBlockElements","fkElementType","pkBlockElementID='".$elementID."'");
			deleteQuery("tblBlockElements",$condition);
			if($type==4){
				$sql="SELECT pkRowID FROM tblTableRows WHERE fkBlockElementID='".$elementID."'";
				$q=$mysqli->query($sql);while($rows=mysqli_fetch_assoc($q)){
					deleteTableRow($rows['pkRowID']);
				}	
			}
			reorderAfterDelete("pkBlockElementID","tblBlockElements","fldPosition",$reorderConditional);
			break;
		case "galleryElement":
			$condition="fkBlockElementID='".$_POST['blockElementID']."' ";
			$condition.="AND fkImageID='".$_POST['elementID']."'";
			$reorderConditional="fkBlockElementID='".$_POST['blockElementID']."'";
			deleteQuery("tblGalleryBlocks",$condition);
			reorderAfterDelete("fkImageID","tblGalleryBlocks","fldRank",$reorderConditional);
			break;
		case "tableRow":
			$rowID=$_POST['elementID'];
			deleteTableRow($rowID);
			$reorderConditional="fkBlockElementID='".$tableID."'";
			reorderAfterDelete("pkRowID","tblTableRows","fldPosition",$reorderConditional);
			break;
		case "image":
			deleteImage($_POST['image']);
			break;
	}
}

//Reorder Items
if(isset($_POST['reorder'])){
	$triggerRank=$_POST['triggerRank'];
	$direction=$_POST['direction'];
	switch($_POST['reorder']){
		case "galleryElement":
			$rankField="fldRank";
			$table="tblGalleryBlocks";
			$conditional=" AND fkBlockElementID='".$_POST['blockElement']."'";
			break;
		case "navRank":
			$table="tblPageBlocks";
			$rankField="fldNavRank";
			$triggerRank=singleValueSQL($table,$rankField,"pkPageBlockID='".$_POST['triggerRank']."'");

			break;
		case "blockElement":
			$table="tblBlockElements";
			$pageBlock=singleValueSQL("tblBlockElements","fkBlock","pkBlockElementID='".$_POST['blockElement']."'");
			$rankField="fldPosition";
			$conditional=" AND fkBlock='".$pageBlock."'";
			break;
		case "tableRow":
			$table="tblTableRows";
			$rankField="fldPosition";
			$conditional=" AND fkBlockElementID='".$_POST['blockElement']."'";
			break;
	}
	
	//Get the lowest rank.
	$lowest=getMax($table,$rankField,"");$data['lowestRank']=$lowest;
	
	//Is re-ranking logical?
	if((($triggerRank<1)&&($direction=="up"))||(($triggerRank==$lowest)&&($direction=="down"))){}	
	else{
		if($direction=="up"){$target=$triggerRank-1;}
		if($direction=="down"){$target=$triggerRank+1;}
		
		//Temporary ID 
		$fields=array($rankField=>"0");
		$condition=$rankField."='".$target."'".$conditional;
		$data[]=updateQuery($table,$fields,$condition);
	
		//New ID for trigger
		$fields=array($rankField=>$target);
		$condition=$rankField."='".$triggerRank."' ".$conditional;
		$data[]=updateQuery($table,$fields,$condition);
		
		//New ID for target
		$fields=array($rankField=>$triggerRank);
		$condition=$rankField."='0' ".$conditional;
		$data[]=updateQuery($table,$fields,$condition);
	}
}

//New Table Row
if(isset($_POST['newTableRow'])){
	$tableElement=$_POST['newTableRow'];
	$table="tblTableRows";
	//Get Max Position
	$position=getMax($table,"fldPosition","fkBlockElementID='".$tableElement."'")+1;
	$fields=array("fkBlockElementID","fldPosition");
	$values=array($tableElement,$position);
	$newRow=insertQuery($table,$fields,$values);
	$i=1;$num=3;
	while($i<=$num){
		$fields=array("fkRowID","fldColumn","fldValue");
		$values=array($newRow,$i,"Value");
		insertQuery("tblTableCells",$fields,$values);
		$i++;
	}
	$data['newRow']=retrieveTableRow($newRow,$tableElement,$position,true);
}

//JSON Response
header('Content-Type: application/json');
echo json_encode($data);	
?>