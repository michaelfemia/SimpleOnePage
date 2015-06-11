<?
include('global_functions.php'); 
include('headnav.php');
?>
<div id="homepageLeadWrap">						
	<div id="homepageLeadImage" style="background-image:url('img/1440/{homepageLeadImage}');">
		<!--<div id="homepageTitleWrap">
			<h2 id="homepageTagline">{homepageTagline}</h2>
			<h1 id="homepageTitle">{homepageTitle}</h1>		
			<div id="hoursAndLocation">
				<h3 id="homepageLocation">{homepageLocation}</h3>
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
				<p class="blockText" style="width:100%">{contactAddress1}</p>
				<p class="blockText" style="width:100%">{contactAddress2}</p>
				<p class="blockText" style="width:100%"><b style="color:#DC4E00">Hours:</b>{contactHours}</p>
				<p class="blockText" style="width:100%"><b style="color:#DC4E00">Phone: </b><a href="{contactPhone}">{contactPhone}</a></p>
				<p class="blockText" style="width:100%"><a href="mailto:{contactEmail}">{contactEmail}</a></p>
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