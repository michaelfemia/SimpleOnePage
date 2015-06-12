<?
include('global_functions.php'); 
include('headnav.php');
?>
<div id="homepageLeadWrap">						
	<div id="homepageLeadImage">	
	</div><!--#homepageLeadImage-->
</div><!--#homepageLeadWrap"-->

<div id="contentWrapper">	
<? retrieveBlocks('0'); ?>
</div><!--#contentWrapper"-->

<div id="contact">
	<div class="department" style="padding:0;">
		<div id="contactVisits">
			<div id="contactMeta">
				<h2 class="blockHeadline" style="width:100%;text-align:left;color:#89CEDE;">Contact</h2>
				<p class="blockText" style="width:100%"></p>
				<p class="blockText" style="width:100%"></p>
				<p class="blockText" style="width:100%"><b style="color:#DC4E00">Hours:</b></p>
				<p class="blockText" style="width:100%"><b style="color:#DC4E00">Phone: </b></p>
				<p class="blockText" style="width:100%"><a href="mailto:info@HollandPatentCoop.com"></a></p>
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