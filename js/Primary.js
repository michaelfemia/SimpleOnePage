//Debug: console log visibility
var consoleSplitter = "---------------------------------------------";var consoleSpace = "          ";

//Starting values for sizing
var windowHeight, titleSmallHeight, imageHeight, containerHeight, slideshowHeight, slideshowImageCount, pinDur;
var windowWidth = $(window).width();
var startingSize = $(window).width() * $(window).height();
var navHeight = 71;var navOffset=65;
if($(window).width()<=620){navOffset=30;}

//Menu toggle logic
var fired=0;
var tagHidden=false;

//Parallax controller
var controller = new ScrollMagic.Controller({globalSceneOptions: {triggerHook: "onEnter", duration: "200%"}});
$(document).ready(function() {
    console.log(consoleSplitter + "docReady Fired");
    htmlHousekeeping();
    navHandling();
});
$(window).load(function() {
    console.log(consoleSplitter + "windowLoad fired");
	blockElementSizing();
	if(($(window).width()>=720)&&(typeof editingPage== 'undefined')){SMParallax();}
});

(function($) {
    var resizeTimer;
    function resizeFunction() {
        blockElementSizing();
    };
    $(window).resize(function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(resizeFunction, 300);
    });
})(jQuery);

$(window).scroll(function() {
    var scrollHeight = $(window).scrollTop();
    if($("#homepageTitle").length>0){
		var headerOffset=$("#homepageTitle").offset();
			headerOffset=headerOffset.top;
		if ((scrollHeight > headerOffset)&&(tagHidden==false)) {
			$("#headerboxTagline").hide();
			$("#headerboxSiteName").fadeIn();
			tagHidden=true;
		}
		if ((scrollHeight <= headerOffset)&&(tagHidden==true)) {
			$("#headerboxSiteName").hide();
			$("#headerboxTagline").fadeIn();
			tagHidden=false;
		}
    }
});
function navHandling(){
	$("#navMenuDropdown").click(function() {
        if((fired==1)&&(typeof editingPage!= 'undefined')){
        	$("#navelements").hide();
        	fired=0;
        }
        else{
        	$("#navelements").show();
        	console.log("Show Nav");
        	fired=0;
        	if(typeof editingPage!= 'undefined'){
        		fired=1;
        	}
        }
    });
    if(typeof editingPage!= 'undefined'){}
    else{
		$(".navelement").each(function() {
			$(this).click(function() {
				$("#navelements").hide();
				fired=1;
			});
		});
		$(document).mouseup(function(e){
			console.log("mouse");
			var container = $("#navelements");
			var button = $("#navMenuDropdown");
			fired=0;
			if(container.is(":visible")){
				container.fadeOut();
				fired=1;
			}
		});
	}
    $(".tileLink,.bodyNavElement").each(function(){
    	var href=$(this).attr('href').replace('#','');
    	$(this).click(function(){
    		animateNavigation(href,navOffset);
    	});
    	var paragraph=$(this).children().children('p');
    	var readMore=paragraph.text()+'<span class="readMore"> more>></span>';
    	paragraph.html(readMore);
    });
}
function readMore(target,trigger){
	var triggerHide=$("#"+trigger);
	var divOpen=$("#"+target);
	triggerHide.remove();
	divOpen.removeAttr('style');
}
function htmlHousekeeping() {
    //Animate scroll to homepage elements    	
	console.log("Nav Offset="+navOffset+"px");
	$("#navelements a").click(function(){
		var navTarget1=$(this).children("div.navelement").attr('id');
		var navTarget=navTarget1.replace('nav','');
		if($("#"+navTarget).length>0){
			animateNavigation(navTarget,navOffset);
		}
	});
}
function animateNavigation(target,offset){
	$("html, body").animate({scrollTop: $('#'+target).offset().top-offset }, 500);
}
function blockElementSizing(){
	console.log("blockElementSizing fired");
	var maxHeight = -1;
   
   var maxHeight=-1;
   $(".homepageNavTileText").children('p').each(function(){
   		$(this).removeAttr('style');
   		maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
    });
   $(".homepageNavTileText").children('p').each(function() {
		$(this).height(maxHeight+'px');
   
   });
   maxHeight=-1;
   //Equal Vertical Padding On Gallery Images
	$(".galleryStrip").each(function(){
		maxHeight=-1;
		var gallery=$(this);
		gallery.children().children("img.gallery").each(function(){
			maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
		});
		gallery.children().children("img.gallery").each(function(){
			var difference=maxHeight-$(this).height();
			var padding=difference/2;
			$(this).css('padding-top',padding+'px');
			$(this).css('padding-bottom',padding+'px');
		});
	});
	$("iframe").each(function() {
        var iWidth = $(this).width() * 0.8;
        var iHeight = iWidth * 0.625;
        if ($(this).parent("div#titleImage").length) {
            iHeight = $(window).height() - 40;
        }
        $(this).css("height", iHeight + "px");
        $(this).children().css("padding", "0px");
    });
}
function newGalleryEditNode(node,blockElementID,add){
	//Is a single image element being updated?
	if(add==false){
		$('#new'+blockElementID).siblings("div").html(node);
	}
	//Or is a gallery being expanded?
	if(add==true){
		$('#new'+blockElementID).before(node);
	}	
}
function SMParallax(){	
	$(".parallaxParent").each(function(){
		var thisParID=$(this).attr('id');
		var smTween="#"+thisParID+" > div ";
		new ScrollMagic.Scene({triggerElement: "#"+thisParID})
		.setTween(smTween, {y: "80%", ease: Linear.easeNone})
		.addTo(controller);
	});
}