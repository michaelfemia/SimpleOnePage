<?
include('global_functions.php'); 
include('headnav.php');
?>
<div id="homepageLeadWrap">						
	<div id="homepageLeadImage" style="background-image:url('img/1440/mikerotarybarnjpg1429277521.jpg');">
		<!--<div id="homepageTitleWrap">
			<h2 id="homepageTagline">Boots    •    Clothing    •    Farm & Garden   •    Pet & Livestock Products</h2>
			<h1 id="homepageTitle">Holland Patent Farmers Co-op</h1>		
			<div id="hoursAndLocation">
				<h3 id="homepageLocation">Located on Depot St. in Holland Patent  (315)-865-5281</h3>
				<span style="color:#FFF;" id="locationHoursSplitter">&nbsp;|&nbsp;</span>
				<h3 id="homepageHours">{homepageHours}</h3>
			</div>
		</div>-->		
	</div><!--#homepageLeadImage-->
</div><!--#homepageLeadWrap"-->

<div id="contentWrapper">	
<? retrieveBlocks('0'); ?>
</div><!--#contentWrapper"-->
<div id="contact">
	<div class="department" style="padding:0;">
		<div id="contactVisits">
			<div id="contactMeta">
				<h2 class="blockHeadline" style="width:100%;text-align:left;color:#89CEDE;">Contact Us</h2>
				<p class="blockText" style="width:100%">9560 Depot Street</p>
				<p class="blockText" style="width:100%">Holland Patent, NY 13354</p>
				<p class="blockText" style="width:100%"><b style="color:#DC4E00">Hours:</b>Mon-Fri 9-5, Sat 8-3</p>
				<p class="blockText" style="width:100%"><b style="color:#DC4E00">Phone: </b><a href="(315) 865-5281">(315) 865-5281</a></p>
				<p class="blockText" style="width:100%"><a href="mailto:info@HollandPatentCoop.com">info@HollandPatentCoop.com</a></p>
			</div>
		</div>
	</div>
</div>			
</div><!--#htmlWrapper"-->
<script type="text/javascript">
	// Add a script element as a child of the body
	function downloadJSAtOnload() {
	var element = document.createElement("script");
	element.src = "js/WindowResize.min.js";
	document.body.appendChild(element);
	}

	// Check for browser support of event handling capability
	if (window.addEventListener)
	window.addEventListener("load", downloadJSAtOnload, false);
	else if (window.attachEvent)
	window.attachEvent("onload", downloadJSAtOnload);
	else window.onload = downloadJSAtOnload;
</script>
</body>
</html>