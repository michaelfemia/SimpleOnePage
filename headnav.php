<? if($editingPage==true){include('mgmt/editingHead.php');}?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900|Bree+Serif|Noto+Serif:400italic' rel='stylesheet' type='text/css'>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta content="<? print $searchDescription;?>" name="description"/>
	<meta name="keywords" content="<? print $searchKeywords; ?>"/>
	<meta name="viewport" content="width=device-width"/>
	<meta name="author" content="<? print $authors_name; ?>"/>
	<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
	<meta name="og:title" content="<? print $domain_title;?>"/>
	<meta name="og:description" content="<? print  $searchDescription;?>"/>
	<meta name="og:type" content="article"/>
	<meta name="og:image" content="<? print $socialMediaThumbIMG;?>"/>
	<meta property="og:url" content="<? echo htmlentities(curPageURL());?>"/>
	<meta name="twitter:card" content="summary_large_image"/>
	<meta name="twitter:site" content="@<? print $twitterHandle; ?>"/>
	<meta name="twitter:creator" content="@ <? print $twitterHandle; ?>"/>
	<meta name="twitter:title" content="<? print $domain_title;?>"/>
	<meta name="twitter:description" content="<? print  $searchDescription;?>"/>
	<meta name="twitter:image:src" content="<? print $socialMediaThumbIMG;?>"/>
	<meta name="twitter:url" content="<? echo htmlentities(curPageURL());?>"/>    
	<link rel="stylesheet" href="<? print $html_root.'/css/primary.css';?>" />
	<? 
	#########################################
	########        EXTRA CSS        ########
	#########################################
	//Inline Style
	if(strlen($extraStyle)>20){
		print '<style>'.$extraStyle.'</style>';
	}
	//Entire Extra Style Sheet: Syntax: file.css ***File must be in /css/
	if(count($extraStyleSheet)>0){
		foreach($extraStyleSheet as $link){
			print '<link rel="stylesheet" href="/'.$link.'" />';
		}
	}
	?>
	
	
	<script type="text/javascript">
	<? print 'var websiteDomain="'.$jsDomain.'";';?>		
	</script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>		
	<?
	$JSScripts=array('js/TweenMax.min.js','js/ScrollMagic.min.js','js/animation.gsap.js','js/Primary.js');
	if(count($extraJSScripts)>0){
		foreach($extraJSScripts as $add){
			$JSScripts[]=$add;
		}
	}
	if($chartPage=="1"){$JSScripts[]='js/Chart.min.js';}
	foreach($JSScripts as $script){
		print '<script type="text/javascript" ';
		print 'src="'.$html_root.'/'.$script.'"></script>'."\n";
	}
	if($editingPage==true){
			print'<link rel="stylesheet" href="css/editor.css" />';
			print'<link rel="stylesheet" href="mgmt/dropzone.css" />';
			print'<script type="text/javascript" src="js/editor.js"></script>';
			print'<script src="mgmt/dropzone.js"></script>';
			print'<script>var editingPage=true;</script>';
		}
		if($loginPage==true){
			print'<link href="../css/admin.css" rel="stylesheet" type="text/css" />';
			print'<link rel="stylesheet" href="../css/editor.css" />';
			print'<script type="text/JavaScript" src="js/sha512.js"></script> ';
			print'<script type="text/JavaScript" src="js/forms.js"></script>';			
		}
	?>
	<title><? print $domain_title;?></title>	
</head>
<!--############################################################################
################################################################################
#############################################################################-->
<body>	
<!--GOOGLE ANALYTICS-->
<? 
if($editingPage==true){}else{print $googleAnalytics;} 
print '<div id="htmlWrapper">';
print '<div id="navWrapper">';
	if($loginPage==true){}
	else{	
		//HOME BUTTON & NAV MENU GRID BUTTON
		print '<div id="navMenuDropdown">';
		print 	'<button type="button">';
		print 		'<img src="'.$html_root.'/img/icons/menulines.png"  height="30" width="30" alt="Show Navigation"/>';
		print 	'</button>';
		print 		'<div><a href="javascript:void(0)">Menu</a></div>';
		print '</div><!--#NAVBUTTONS-->';

		//CENTER PAGETITLE
		print '<div itemscope itemtype="http://schema.org/Organization" id="topSiteName">';
			print '<span itemprop="name">';
				print '<a itemprop="url" href="'; 
				if($content['pageid']!==0){print $html_root;}
				if($content['pageid']==0){print "#topofpage";}
				print'">';
		print '</a>';
		print '		</span>';
		print '</div>';
				
		//HEADERBOX: PAGE TITLE OR SITE TITLE
		$tagline="";
		print '<div id="headerbox" class="headerhome">';
		print '<span id="headerboxTagline">'.$tagline.'</span>';
		print '<span id="headerboxSiteName" style="display:none;">'.$domain_title.'</span>';
		print '</div>';

		
		if($editingPage==true){print'<a href="login/?logout=1">Log-Out</a>';}
		print '</span>';
		
		//SCROLL PROGRESS INDICATOR
		print'<div id="pageprogress"></div>';
}
print '</div><!--NAVIGATION BAR-->';
	
//CMS NAV MENU: Add new page blocks, rearrange, etc.
print '<div id="navelements">';
$nav="";
if($editingPage==true){
	$sql="SELECT pkPageBlockID,fldNavRank,fldPageName,fldPageLink FROM tblPageBlocks ";
	$sql.="ORDER BY fldNavRank ASC";
	$r=$mysqli->query($sql);
	while($result=mysqli_fetch_assoc($r)){
		
		//Static container for rank
		$nav.='<div class="navAdjustment" id="adjust'.$result['fldNavRank'].'">'."\n";
			
			//Container to manipulate thisBlockID
			$nav.='<div id="blockAdjust'.$result['pkPageBlockID'].'">'."\n";
				
				//Delete this block
				$nav.='<button class="deleteBlock" onClick="deleteBlock('."'".$result['pkPageBlockID']."','".$result['fldPageLink']."'".')">';
				$nav.='<img src="img/icons/trash.png"></button>'."\n";
				
				//Promote or demote block ranking
				$nav.='<button class="rankAdjust" onClick="navRankAdjust('."'".$result['pkPageBlockID']."','".$result['fldPageLink']."','down'".')">';
				$nav.='<img src="img/icons/downarrow.png"></button>'."\n";
				$nav.='<button class="rankAdjust" onClick="navRankAdjust('."'".$result['pkPageBlockID']."','".$result['fldPageLink']."','up'".')">';
				$nav.='<img src="img/icons/uparrow.png"></button>'."\n";
				
				//Change block name.
				$nav.='<input class="cms nav" id="editnav'.$result['pkPageBlockID'].'" type="text" value="'.$result['fldPageName'].'">'."\n";
				$nav.='<span style="color:#ffffff;" id="nav'.$result['pkPageBlockID'].'" class="navEditItem">'.$result['fldPageName'].'</span>'."\n";
			$nav.='</div><!--.blockAdjust-->'."\n";
		$nav.='</div>'."\n\n";
		$i++;
	}
		$nav.='<div id="newBlockAdditionContainer">'."\n";
			$nav.='<button>';
				$nav.='<img id="addNewBlock" src="img/icons/plus.png">';
			$nav.='</button>'."\n";
			$nav.='<input type="text" value="" id="newBlockName">'."\n";
		$nav.='</div>'."\n";		 
}
	
//NO NAVIGATION OPTIONS DISPLAYED ON LOGIN PAGE
else if($loginPage==true){}	
	
//HOMEPAGE NAVIGATION
else{
	//SELECT CATEGORY NAMES & DESCRIPTIONS FOR NAV BAR
	$navElements=array();
	$sql="SELECT fldPageName,fldPageLink FROM tblPageBlocks ";
	$sql.="ORDER BY fldNavRank ASC";
	$r=$mysqli->query($sql);
	while($result=mysqli_fetch_assoc($r)){
		$navElements['#'.$result['fldPageLink']]=$result['fldPageName'];
	}
	$parameters=array();
	$parameters['thisURL']=curPageURL();
	foreach($navElements as $navElement=>$navHeading){
		$parameters['navElement']=$navElement;
		$parameters['navHeading']=$navHeading;
		$nav.=printNavElement($parameters);
	}
	$parameters['navElement']="#contact";
	$parameters['navHeading']="Contact Me";
	$nav.=printNavElement($parameters);

	if($pageType!=="CMS"){
	}
}
print $nav;
print'</div><!--NAVELEMENTS-->';


//TOP OF PAGE NAVIGATION
if($loginPage!==true){
	print '<div id="bodyNav">'."\n";
		print '<div id="topLogo">';
			print '<div id="siteName">'.singleValueSQL("tblHomepageManagement","fldValue","pkID='31'").'</div>';
			if($editingPage==true){print $updateFields['siteName'];}
			print '<div id="topTagline">'.singleValueSQL("tblHomepageManagement","fldValue","pkID='32'").'</div>';
			if($editingPage==true){print $updateFields['topTagline'];}
		print '</div>'."<!--#topLogo-->\n";
		print '	<div id="bodyNavElements"';
		if($editingPage==true){print 'style="display:none;"';}
		print '>'."\n";
			$sql="SELECT fldPageName,fldPageLink FROM tblPageBlocks ";
				$sql.="ORDER BY fldNavRank ASC ";
				$sql.="LIMIT 0,6";
				$r=$mysqli->query($sql);
				while($result=mysqli_fetch_assoc($r)){
					print '<a class="bodyNavElement" href="';
					$thisURL=curPageURL();
					if($thisURL!=$html_root){
						print $html_root;
					}
					print '#'.$result['fldPageLink'].'"><span>'.$result['fldPageName'].'</span></a>';
				}
		print '	</div>'."<!--#bodyNavElements-->\n";
	print '</div>'."<!--#bodyNav-->\n";
}