const REV = 6,
       BRUSHES = ["simple","arrow"],
       USER_AGENT = navigator.userAgent.toLowerCase();

	var hasStroke = false;

var SCREEN_WIDTH = window.innerWidth,
    SCREEN_HEIGHT = window.innerHeight,
    BRUSH_SIZE = 5,
    BRUSH_PRESSURE = 2,
    COLOR = [0, 0, 0],
    BACKGROUND_COLOR = [255, 255, 255],
    //STORAGE = window.localStorage,
	STORAGE = null,
    brush,
    saveTimeOut,
    wacom,
    i,
    mouseX = 0,
    mouseY = 0,
	menucontainer,
    container,
    foregroundColorSelector,
    backgroundColorSelector,
    menu,
    about,
    canvas,
    flattenCanvas,
    context,
    isFgColorSelectorVisible = false,
    isBgColorSelectorVisible = false,
    isAboutVisible = false,
    isMenuMouseOver = false,
    shiftKeyIsDown = false,
    altKeyIsDown = false,
	brushSizeTouchStart = 1,
	brushSizeTouchReference = 0.0;

init();

function init()
{
	var hash, palette, embed, localStorageImage;
	
	if (USER_AGENT.search("android") > -1 || USER_AGENT.search("iphone") > -1)
		BRUSH_SIZE = 2;	
		
	if (USER_AGENT.search("safari") > -1 && USER_AGENT.search("chrome") == -1) // Safari
		STORAGE = false;
	
	//document.body.style.backgroundRepeat = 'no-repeat';
	//document.body.style.backgroundPosition = 'center center';	
	
	menucontainer = document.getElementById("doodlemenu");
	container = document.getElementById("doodlediv");
	canvas = document.getElementById("doodle");

	SCREEN_WIDTH = canvas.width;
	SCREEN_HEIGHT = canvas.height
	canvas.style.cursor = 'crosshair';
	container.appendChild(canvas);
	
	flattenCanvas = document.createElement("canvas");
	flattenCanvas.width = SCREEN_WIDTH;
	flattenCanvas.height = SCREEN_HEIGHT;
	
	palette = new Palette();
	
	foregroundColorSelector = new ColorSelector(palette);
	foregroundColorSelector.addEventListener('change', onForegroundColorSelectorChange, false);
	// append to the middle of drawing canvas
	container.appendChild(foregroundColorSelector.container);

	//    no need a background selector, silly
	//backgroundColorSelector = new ColorSelector(palette);
	//backgroundColorSelector.addEventListener('change', onBackgroundColorSelectorChange, false);
	//menucontainer.appendChild(backgroundColorSelector.container);	
	
	var noScroll = function(event) {event.preventDefault()};
	
	menu = new Menu();
	menu.container.addEventListener('touchmove', noScroll, false);
	menu.foregroundColor.addEventListener('click', onMenuForegroundColor, false);
	//menu.backgroundColor.addEventListener('click', onMenuBackgroundColor, false);
	menu.selector.forEach(function (elem) {
		elem.addEventListener('change', function (ele) {
			return function() {
				onMenuSelectorChange(ele);
			};
		}(elem), false);
	});
	//menu.save.addEventListener('click', onMenuSave, false);
	menu.exportImage.addEventListener('click', onMenuExportImage, false);
	menu.clear.addEventListener('click', onMenuClear, false);
	//menu.about.addEventListener('click', onMenuAbout, false);
	menu.container.addEventListener('mouseover', onMenuMouseOver, false);
	menu.container.addEventListener('mouseout', onMenuMouseOut, false);
	menucontainer.appendChild(menu.container);
	
	//flattenCanvas.height = canvas.height = SCREEN_HEIGHT - menu.container.offsetHeight;
	// menu is now detached from canvas
	flattenCanvas.height = canvas.height = SCREEN_HEIGHT;
	//canvas.style.position = 'absolute';
	//canvas.style.top = menu.container.offsetHeight+'px';
	context = canvas.getContext("2d");

	
	/* DISABLE LOCAL STORAGE
	if (STORAGE)
	{
		if (localStorage.canvas)
		{
			localStorageImage = new Image();
		
			localStorageImage.addEventListener("load", function(event)
			{
				localStorageImage.removeEventListener(event.type, arguments.callee, false);
				context.drawImage(localStorageImage,0,0);
			}, false);
			
			localStorageImage.src = localStorage.canvas;			
		}
		
		if (localStorage.brush_color_red)
		{
			COLOR[0] = localStorage.brush_color_red;
			COLOR[1] = localStorage.brush_color_green;
			COLOR[2] = localStorage.brush_color_blue;
		}

		if (localStorage.background_color_red)
		{
			BACKGROUND_COLOR[0] = localStorage.background_color_red;
			BACKGROUND_COLOR[1] = localStorage.background_color_green;
			BACKGROUND_COLOR[2] = localStorage.background_color_blue;
		}
	}*/

	foregroundColorSelector.setColor( COLOR );
	//backgroundColorSelector.setColor( BACKGROUND_COLOR );
	
	if (window.location.hash)
	{
		hash = window.location.hash.substr(1,window.location.hash.length);

		for (i = 0; i < BRUSHES.length; i++)
		{
			if (hash == BRUSHES[i])
			{
				brush = eval("new " + BRUSHES[i] + "(context)");
				setSelectedIndex(menu.selector, i);
				break;
			}
		}
	}

	if (!brush)
	{
		brush = eval("new " + BRUSHES[0] + "(context)");
	}
	
	about = new About();
	container.appendChild(about.container);
	
	//window.addEventListener('mousemove', onWindowMouseMove, false);
	//window.addEventListener('resize', onWindowResize, false);
	//window.addEventListener('keydown', onWindowKeyDown, false);
	//window.addEventListener('keyup', onWindowKeyUp, false);
	window.addEventListener('blur', onWindowBlur, false);
	
	document.addEventListener('mousedown', onDocumentMouseDown, false);
	document.addEventListener('mouseout', onDocumentMouseOut, false);
	
	document.addEventListener("dragenter", onDocumentDragEnter, false);  
	document.addEventListener("dragover", onDocumentDragOver, false);
	document.addEventListener("drop", onDocumentDrop, false);  
	
	canvas.addEventListener('mousedown', onCanvasMouseDown, false);
	//canvas.addEventListener('touchstart', onCanvasTouchStart, false);
	
	onWindowResize(null);
	
	context.lineCap = 'round';
	
	
	//setBackgroundPng("http://www.outdooralabama.com/photos/images/07Scenic2.jpg");
}


// WINDOW

function onWindowMouseMove( event )
{
	mouseX = event.clientX;
	mouseY = event.clientY;
}

function onWindowResize()
{
	//SCREEN_WIDTH = window.innerWidth;
	//SCREEN_HEIGHT = window.innerHeight;
	
	//menu.container.style.left='0px';
	//menu.container.style.width=SCREEN_WIDTH+'px';
	
	//about.container.style.left = ((SCREEN_WIDTH - about.container.offsetWidth) / 2) + 'px';
	//about.container.style.top = ((SCREEN_HEIGHT - about.container.offsetHeight) / 2) + 'px';
}

function onWindowKeyDown( event )
{
	if (shiftKeyIsDown)
		return;
		
	switch(event.keyCode)
	{
		/*case 16: // Shift
			shiftKeyIsDown = true;
			foregroundColorSelector.container.style.left = mouseX - 125 + 'px';
			foregroundColorSelector.container.style.top = mouseY - 125 + 'px';
			foregroundColorSelector.container.style.visibility = 'visible';
			break;*/
			
		case 18: // Alt
			///altKeyIsDown = true;
			break;
			
		case 68: // d
			//if(BRUSH_SIZE > 1) BRUSH_SIZE --;
			break;
		
		case 70: // f
			//BRUSH_SIZE ++;
			break;			
	}
}

function onWindowKeyUp( event )
{
	switch(event.keyCode)
	{
		/*case 16: // Shift
			shiftKeyIsDown = false;
			foregroundColorSelector.container.style.visibility = 'hidden';			
			break;*/
			
		case 18: // Alt
			altKeyIsDown = false;
			break;

		case 82: // r
			//brush.destroy();
			//brush = eval("new " + BRUSHES[getSelectedIndex(menu.selector)] + "(context)");
			break;
		case 66: // b
			//document.body.style.backgroundImage = null;
			break;
	}
	
	//context.lineCap = BRUSH_SIZE == 1 ? 'butt' : 'round';	
}

function onWindowBlur( event )
{
	shiftKeyIsDown = false;
	altKeyIsDown = false;
}


// DOCUMENT

function  isEventInColorSelector(cx, cy) {
  if (!isFgColorSelectorVisible && !isBgColorSelectorVisible) {
		return false;
	}
	
	var xLoc = 0,
		yLoc = 0;
	/*
	if (isFgColorSelectorVisible) {
		xLoc = foregroundColorSelector.container.offsetLeft + 250;
		yLoc = foregroundColorSelector.container.offsetTop;
	} else {
		xLoc = backgroundColorSelector.container.offsetLeft + 250;
		yLoc = backgroundColorSelector.container.offsetTop;
	}*/
	
	//xLoc = cx - xLoc;
	//yLoc = cy - yLoc;
  
  xLoc = cx - foregroundColorSelector.container.offsetLeft;
  yLoc = cy - foregroundColorSelector.container.offsetTop;
  
	//return (xLoc >= 0 && xLoc <= 150 &&
	//	    yLoc >= 0 && yLoc <= 250);
  
  return (xLoc >= 0 && xLoc <= 250 &&
		    yLoc >= 0 && yLoc <= 250);
}

function onDocumentMouseDown( event )
{
  var X = event.clientX - canvas.offsetLeft - canvas.parentNode.offsetLeft - canvas.parentNode.parentNode.offsetLeft;
  var Y = event.clientY - canvas.offsetTop - canvas.parentNode.offsetTop - canvas.parentNode.parentNode.offsetTop;
 
  //alert(X + " " + Y);
 
  //if (!isMenuMouseOver && !isEventInColorSelector(event.clientX, event.clientY)) {
  if (!isMenuMouseOver && !isEventInColorSelector(X, Y)) {
		event.preventDefault();
		
		if(isFgColorSelectorVisible) cleanPopUps();
	}
}

function onDocumentMouseOut( event )
{
	onCanvasMouseUp();
}

function onDocumentDragEnter( event )
{
	event.stopPropagation();
	event.preventDefault();
}

function onDocumentDragOver( event )
{
	event.stopPropagation();
	event.preventDefault();
}

function onDocumentDrop( event )
{
	event.stopPropagation();  
	event.preventDefault();
	
	var file = event.dataTransfer.files[0];
	
	if (file.type.match(/image.*/))
	{
		/*
		 * TODO: This seems to work on Chromium. But not on Firefox.
		 * Better wait for proper FileAPI?
		 */

		var fileString = event.dataTransfer.getData('text').split("\n");
		document.body.style.backgroundImage = 'url(' + fileString[0] + ')';
	}	
}


// COLOR SELECTORS

function onForegroundColorSelectorChange( event )
{
	COLOR = foregroundColorSelector.getColor();
	menu.setForegroundColor( COLOR );
	
	if (STORAGE)
	{
		localStorage.brush_color_red = COLOR[0];
		localStorage.brush_color_green = COLOR[1];
		localStorage.brush_color_blue = COLOR[2];		
	}
}

function onBackgroundColorSelectorChange( event )
{
	BACKGROUND_COLOR = backgroundColorSelector.getColor();
	
	menu.setBackgroundColor( BACKGROUND_COLOR );
	
	document.body.style.backgroundColor = 'rgb(' + BACKGROUND_COLOR[0] + ', ' + BACKGROUND_COLOR[1] + ', ' + BACKGROUND_COLOR[2] + ')';
	
	if (STORAGE)
	{
		localStorage.background_color_red = BACKGROUND_COLOR[0];
		localStorage.background_color_green = BACKGROUND_COLOR[1];
		localStorage.background_color_blue = BACKGROUND_COLOR[2];				
	}
}


// MENU

function onMenuForegroundColor()
{
	if(isFgColorSelectorVisible) {
		cleanPopUps();
	} else {

		cleanPopUps();
		
		foregroundColorSelector.show();
		foregroundColorSelector.container.style.left = ((SCREEN_WIDTH - foregroundColorSelector.container.offsetWidth) / 2) + 'px';
		foregroundColorSelector.container.style.top = ((SCREEN_HEIGHT - foregroundColorSelector.container.offsetHeight) / 2) + 'px';

		isFgColorSelectorVisible = true;
	}
}

function onMenuBackgroundColor()
{
	cleanPopUps();

	backgroundColorSelector.show();
	backgroundColorSelector.container.style.left = ((SCREEN_WIDTH - backgroundColorSelector.container.offsetWidth) / 2) + 'px';
	backgroundColorSelector.container.style.top = ((SCREEN_HEIGHT - backgroundColorSelector.container.offsetHeight) / 2) + 'px';

	isBgColorSelectorVisible = true;
}

function onMenuSelectorChange(ele)
{
	console.log(ele, ele.checked, getSelectedIndex(menu.selector));
	if (BRUSHES[getSelectedIndex(menu.selector)] == "") {
		return;
	}
	brush.destroy();
	brush = eval("new " + BRUSHES[getSelectedIndex(menu.selector)] + "(context)");

	window.location.hash = BRUSHES[getSelectedIndex(menu.selector)];
}

function onMenuMouseOver()
{
	isMenuMouseOver = true;
}

function onMenuMouseOut()
{
	isMenuMouseOver = false;
}

function onMenuSave()
{
	saveToLocalStorage();
}


function canvasToImage(canvas, backgroundColor) {
	//cache height and width		
	var w = canvas.width;
	var h = canvas.height;
	var data;
	var context = canvas.getContext("2d");
	if(backgroundColor) {
		//get the current ImageData for the canvas.
		data = context.getImageData(0, 0, w, h);
		//store the current globalCompositeOperation
		var compositeOperation = context.globalCompositeOperation;
		//set to draw behind current content
		context.globalCompositeOperation = "destination-over";
		//set background color
		context.fillStyle = backgroundColor;
		//draw background / rect on entire canvas
		context.fillRect(0,0,w,h);
	}
	//get the image data from the canvas
	var imageData = this.canvas.toDataURL("image/png");
	if(backgroundColor) {
		//clear the canvas
		context.clearRect (0,0,w,h);
		//restore it with original / cached ImageData
		context.putImageData(data, 0,0);		
		//reset the globalCompositeOperation to what it was
		context.globalCompositeOperation = compositeOperation;
	}
	//return the Base64 encoded data url string
	return imageData;
}

function onMenuExportImage() {
	if (currentImageUrl == '') {
		return;
	}

	// window.open(canvas.toDataURL('image/png'),'mywindow');
	flatten();
	//window.open(flattenCanvas.toDataURL('image/png'),'mywindow');
	
	var archived = document.createElement('div');
	archived.style.width = '240px';
	archived.style.height = '160px';
	archived.style.background = document.getElementById('doodlebg').style.background;
	archived.style.backgroundSize = 'cover';
	archived.style.margin = '20px auto';
	
	archived.setAttribute('data-url', currentImageUrl);
	
	var overlay = document.createElement('div');
	overlay.style.width = '240px';
	overlay.style.height = '160px';
	overlay.style.background = 'url(' + canvasToImage(flattenCanvas, 'rgba(255, 0, 0, 0)') + ')';
	overlay.style.backgroundSize = 'cover';
	overlay.style.margin = '20px auto';
	
	archived.appendChild(overlay);
	
	archived.onclick = function (elem) {
		return function () {
			restore(elem);
		};
	}(archived);
	
	var screenShots = document.querySelector('#screen_shots div');
	if (screenShots.childElementCount < 1) {
		screenShots.appendChild(archived);
	} else {
		screenShots.insertBefore(archived, screenShots.firstElementChild);
	}

	//done_doodling();
	
	disable_doodles();
	document.getElementById('doodle').getContext('2d').clearRect(0, 0, 480, 320);
	document.getElementById('doodlebg').style.background = '';

	currentImageUrl = '';
	
	close_remote_doodle();
}

function clearDoodle() {
	document.getElementById('doodle').getContext('2d').clearRect(0, 0, 480, 320);
	document.getElementById('doodlebg').style.background = '';
}

function restore(element) {
	enable_doodles();
	clearDoodle();
	currentImageUrl = element.getAttribute('data-url');
	document.getElementById('doodlebg').style.background = element.style.background;
	var img = new Image();
	var url = element.querySelector('div').style.background;
	var dataUrl = url.substr(4, url.length - 6);
	
	var context = document.getElementById('doodle').getContext('2d');
	img.src = dataUrl;
	/*
	//get the current ImageData for the canvas.
	data = context.getImageData(0, 0, w, h);
	//store the current globalCompositeOperation
	var compositeOperation = context.globalCompositeOperation;
	//set to draw behind current content
	context.globalCompositeOperation = "destination-over";
	//set background color
	context.fillStyle = 'rgba(255,0,0,0)';
	//draw background / rect on entire canvas
	context.fillRect(0,0,w,h);
	*/
	context.drawImage(img, 0, 0);

	done_doodling();
}

function onMenuClear()
{
	if (!confirm("Are you sure?"))
		return;
		
	context.clearRect(0, 0, SCREEN_WIDTH, SCREEN_HEIGHT);

	saveToLocalStorage();

	brush.destroy();
	brush = eval("new " + BRUSHES[getSelectedIndex(menu.selector)] + "(context)");

	done_doodling();
}

function onMenuAbout()
{
	cleanPopUps();

	isAboutVisible = true;
	about.show();
}


// CANVAS



function onCanvasMouseDown( event )
{
	var data, position;

	clearTimeout(saveTimeOut);
	cleanPopUps();
	
	var X = event.clientX - canvas.offsetLeft - canvas.parentNode.offsetLeft - canvas.parentNode.parentNode.offsetLeft;
  	var Y = event.clientY - canvas.offsetTop - canvas.parentNode.offsetTop - canvas.parentNode.parentNode.offsetTop;
  	//var Y = event.clientY;
  	
	if (altKeyIsDown)
	{
		flatten();
		
		data = flattenCanvas.getContext("2d").getImageData(0, 0, flattenCanvas.width, flattenCanvas.height).data;
		// position = (event.clientX + (event.clientY * canvas.width)) * 4;
  	position = (X + (Y * canvas.width)) * 4;

		foregroundColorSelector.setColor( [ data[position], data[position + 1], data[position + 2] ] );
		
		return;
	}
	
	//BRUSH_PRESSURE = wacom && wacom.isWacom ? wacom.pressure : 1;
	BRUSH_PRESSURE = 100;
	
  //brush.strokeStart( event.clientX - canvas.offsetLeft, event.clientY - canvas.offsetTop );
  	brush.strokeStart( X - canvas.offsetLeft, Y - canvas.offsetTop );
	hasStroke = true;

	window.addEventListener('mousemove', onCanvasMouseMove, false);
	window.addEventListener('mouseup', onCanvasMouseUp, false);
}

function onCanvasMouseMove( event )
{
	//BRUSH_PRESSURE = wacom && wacom.isWacom ? wacom.pressure : 1;
	BRUSH_PRESSURE = 100;
	
  var X = event.clientX - canvas.offsetLeft - canvas.parentNode.offsetLeft - canvas.parentNode.parentNode.offsetLeft;
  var Y = event.clientY - canvas.offsetTop - canvas.parentNode.offsetTop - canvas.parentNode.parentNode.offsetTop;
  //var Y = event.clientY;
  
  
	//brush.stroke( event.clientX - canvas.offsetLeft, event.clientY - canvas.offsetTop );
  brush.stroke( X - canvas.offsetLeft, Y - canvas.offsetTop );
}

var async_send_doodle;

function try_send_doodle() {
	if (ready_to_send && currentImageUrl != '') {
		ready_to_send = false;
		done_doodling();
	} else {
		clearTimeout(async_send_doodle);
		async_send_doodle = setTimeout(try_send_doodle, 1000, true);
	}
}

function onCanvasMouseUp()
{
	if(hasStroke) {

		brush.strokeEnd();
	
		clearTimeout(async_send_doodle);
		async_send_doodle = setTimeout(try_send_doodle, 1000, true);
		hasStroke = false;
	}

	window.removeEventListener('mousemove', onCanvasMouseMove, false);
	window.removeEventListener('mouseup', onCanvasMouseUp, false);
	
	if (STORAGE)
	{
		clearTimeout(saveTimeOut);
		saveTimeOut = setTimeout(saveToLocalStorage, 2000, true);
	}
}


//

function showFGColorPickerAtLocation(loc) {
	foregroundColorSelector.show();
	foregroundColorSelector.container.style.left = (loc[0] - (foregroundColorSelector.container.offsetWidth / 2)) + 'px';
	foregroundColorSelector.container.style.top = (loc[1] - (foregroundColorSelector.container.offsetHeight / 2)) + 'px';

	isFgColorSelectorVisible = true;
}

function averageTouchPositions(touches) {
	var touchLength = touches.length;
	var average = [0,0];
	
	for (var i = 0; i < event.touches.length; ++i) {
		var touch = event.touches[i];
		average[0] += touch.pageX;
		average[1] += touch.pageY;
	}
	average[0] = average[0] / touches.length;
	average[1] = average[1] / touches.length;
	
	return average;
}

function distance(a, b) {
	var dx=a.pageX-b.pageX;
	var dy=a.pageY-b.pageY;
	return Math.sqrt(dx*dx + dy*dy);
}

function onCanvasTouchStart( event )
{
	clearTimeout(saveTimeOut);
	cleanPopUps();		

	if(event.touches.length == 1)
	{
		// draw
		event.preventDefault();
		
		brush.strokeStart( event.touches[0].pageX - canvas.offsetLeft, event.touches[0].pageY - canvas.offsetTop );
		
		window.addEventListener('touchmove', onCanvasTouchMove, false);
		window.addEventListener('touchend', onCanvasTouchEnd, false);
	}
	else if (event.touches.length == 2)
	{
		// brush size
		event.preventDefault();
		
		brushSizeTouchReference = distance(event.touches[0], event.touches[1]);
		brushSizeTouchStart = BRUSH_SIZE;
		
		window.addEventListener('touchmove', onBrushSizeTouchMove, false);
		window.addEventListener('touchend', onBrushSizeTouchEnd, false);
	}
	else if (event.touches.length == 3)
	{
		// foreground color
		event.preventDefault();
		
		var loc = averageTouchPositions(event.touches);
		showFGColorPickerAtLocation(loc);
		
		window.addEventListener('touchmove', onFGColorPickerTouchMove, false);
		window.addEventListener('touchend', onFGColorPickerTouchEnd, false);
	}
	else if (event.touches.length == 4)
	{
		// reset brush
		event.preventDefault();
		window.addEventListener('touchend', onResetBrushTouchEnd, false);
	}
}

function onCanvasTouchMove( event )
{
	if(event.touches.length == 1)
	{
		event.preventDefault();
		brush.stroke( event.touches[0].pageX - canvas.offsetLeft, event.touches[0].pageY - canvas.offsetTop );
	}
}

function onCanvasTouchEnd( event )
{
	if(event.touches.length == 0)
	{
		event.preventDefault();
		
		brush.strokeEnd();

		window.removeEventListener('touchmove', onCanvasTouchMove, false);
		window.removeEventListener('touchend', onCanvasTouchEnd, false);
		
		if (STORAGE)
		{
			clearTimeout(saveTimeOut);
			saveTimeOut = setTimeout(saveToLocalStorage, 2000, true);
		}
	}
}

function onResetBrushTouchEnd( event )
{
	if (event.touches.length == 0)
	{
		event.preventDefault();
		brush.destroy();
		brush = eval("new " + BRUSHES[getSelectedIndex(menu.selector)] + "(context)");
		window.removeEventListener('touchend', onResetBrushTouchEnd, false);
		
		if (STORAGE)
		{
			clearTimeout(saveTimeOut);
			saveTimeOut = setTimeout(saveToLocalStorage, 2000, true);
		}
	}
}

function onFGColorPickerTouchMove( event )
{
	if (event.touches.length == 3)
	{
		event.preventDefault();
		var loc = averageTouchPositions(event.touches);
		foregroundColorSelector.container.style.left = (loc[0] - (foregroundColorSelector.container.offsetWidth / 2)) + 'px';
		foregroundColorSelector.container.style.top = (loc[1] - (foregroundColorSelector.container.offsetHeight / 2)) + 'px';
	}
}

function onFGColorPickerTouchEnd( event )
{
	if (event.touches.length == 0)
	{
		event.preventDefault();
		
		window.removeEventListener('touchmove', onFGColorPickerTouchMove, false);
		window.removeEventListener('touchend', onFGColorPickerTouchEnd, false);
		
		if (STORAGE)
		{
			clearTimeout(saveTimeOut);
			saveTimeOut = setTimeout(saveToLocalStorage, 2000, true);
		}
	}
}

function onBrushSizeTouchMove( event )
{
	if (event.touches.length == 2)
	{
		event.preventDefault();
		
		var size = brushSizeTouchStart + (distance(event.touches[0], event.touches[1]) - brushSizeTouchReference) / 4;
		BRUSH_SIZE = Math.max(Math.min(Math.floor(size), 320), 1);
	}
}

function onBrushSizeTouchEnd( event )
{
	if (event.touches.length == 0)
	{
		event.preventDefault();
		
		window.removeEventListener('touchmove', onBrushSizeTouchMove, false);
		window.removeEventListener('touchend', onBrushSizeTouchEnd, false);
		
		if (STORAGE)
		{
			clearTimeout(saveTimeOut);
			saveTimeOut = setTimeout(saveToLocalStorage, 2000, true);
		}
	}
}

//

function saveToLocalStorage()
{
	localStorage.canvas = canvas.toDataURL('image/png');
}

function flatten()
{
	var context = flattenCanvas.getContext("2d");
	
	context.fillStyle = 'rgb(' + BACKGROUND_COLOR[0] + ', ' + BACKGROUND_COLOR[1] + ', ' + BACKGROUND_COLOR[2] + ')';
	context.fillRect(0, 0, canvas.width, canvas.height);
	context.drawImage(canvas, 0, 0);
}

function cleanPopUps()
{
	if (isFgColorSelectorVisible)
	{
    foregroundColorSelector.hide();
		isFgColorSelectorVisible = false;
	}
		
	if (isBgColorSelectorVisible)
	{
		backgroundColorSelector.hide();
		isBgColorSelectorVisible = false;
	}
	
	if (isAboutVisible)
	{
		about.hide();
		isAboutVisible = false;
	}
}

function getSelectedIndex(selector) {
	selector = document.querySelectorAll("input.brush_selector");
	for (var i = 0; i < selector.length; i++) {
		if (selector[i].checked) {
			return selector[i].value;
		}
	}
	return 0;
}

function setSelectedIndex(selector, index) {
	selector = document.querySelectorAll("input.brush_selector");
	for (var i = 0; i < selector.length; i++) {
		if (selector[i].value == index) {
			selector[i].checked = true;
		} else {
			selector[i].checked = false;
		}
	}
}
