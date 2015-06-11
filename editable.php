<? 
$editingPage=true;
include('global_functions.php');

	//editingHead.php is included in headnav.php
include('headnav.php'); 

print '<div id="contentWrapper">';
	retrieveBlocks('1');
print '</div><!--#contentWrapper-->';
?>
</div><!--#contentWrapper"-->
<div id="contact">
	<div class="department" style="padding:0;"
		<div id="contactVisits">
			<div id="contactMeta" style="max-width:330px;">
				<h2 class="blockHeadline" style="text-align:left;color:#89CEDE;">Contact Us</h2>
				<p id="contactAddress1" class="blockText" style="width:100%"><? print $currentValue['contactAddress1'];?></p>
				<? print $updateFields['contactAddress1'];?>
				<p id="contactAddress2" class="blockText" style="width:100%"><? print $currentValue['contactAddress2'];?></p>
				<? print $updateFields['contactAddress2'];?>
				<p class="blockText" style="width:100%"><b style="color:#DC4E00">Hours:</b><span id="contactHours"><? print $currentValue['contactHours'];?></span></p>
				<? print $updateFields['contactHours'];?>
				<p class="blockText" style="width:100%"><b style="color:#DC4E00">Phone: </b><span id="contactPhone"><? print $currentValue['contactPhone'];?></span></p>
				<? print $updateFields['contactPhone'];?>
				<p id="contactEmail" class="blockText" style="width:100%"><? print $currentValue['contactEmail'];?></p>
				<? print $updateFields['contactEmail'];?>
			</div><!--#contactMeta-->
		</div><!--#contactVisits-->
	</div><!--.department-->
</div><!--#contact-->


<? 
$dir='img/1920';
$files=scandir($dir);
$i=1;
foreach($files as $file){
	if(strlen($file)>5){
		//Is it registered in tblImages? 
		$sql="SELECT tblImages.pkImageID ";
		$sql.="FROM tblImages ";
		$sql.="WHERE tblImages.fldImageName='".$file."' ";
		$q=$mysqli->query($sql);
		$registered=$q->num_rows;
		$result=mysqli_fetch_assoc($q);			
		$imageID=$result['pkImageID'];
		
		//Delete it if it isn't registered.
		if($registered<1){deleteImage($file);}
		
		//If it's registered, is it used in a block or lead element?
		$used="Not Used";
		if($registered>0){
			$blockCheck=simpleResultCount("tblGalleryBlocks","fkBlockElementID","fkImageID='".$imageID."'");
			$leadCheck=simpleResultCount("tblHomepageManagement","fldHTMLID","fldValue='".$file."'");
			if($leadCheck<1 && $blockCheck<1){
				deleteImage($file);
			}
			if($leadCheck>0 || $blockCheck>0){
				//print '<div id="imageBrowser'.$i.'" class="imageBrowser" style="background-image:url('.$dir.'/'.$file.');">';
				//print '</div>';
				$i++;
			}
		}	
	}
}
?>
</div><!--#imageBrowser-->		

</div><!--#htmlWrapper"-->