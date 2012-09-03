<?php
    $this->Html->css(array('conversation'), 'stylesheet', array('inline' => false));
    // $this->Html->script("http://staging.tokbox.com/v0.91/js/TB.min.js", array('inline' => false));
    $this->Html->script("http://static.opentok.com/v0.91/js/TB.min.js", array('inline' => false));
?>
<style>/*
  #subscriber object {
	-webkit-transform-origin: 50% 50%;
	-webkit-transform: rotate(90deg) translateX(78px);
  }*/
</style>

<script type="text/javascript" src="//api.filepicker.io/v0/filepicker.js"></script>
<script type="text/javascript" charset="utf-8">
    var apiKey = '17077042';
    var session;
    var subscribers = {};
    var VIDEO_WIDTH = 480;
    var VIDEO_HEIGHT = 320;

    var publisher;

    var convoid = "<?php echo $conversation['Conversation']['id']?>";

    filepicker.setKey("A6XNHNwuZS5maKavKHrraz");

    
    TB.addEventListener("exception", exceptionHandler);
    if (TB.checkSystemRequirements() != TB.HAS_REQUIREMENTS) {
        alert("You don't have the minimum requirements to run this application." + "Please upgrade to the latest version of Flash.");
    }
    else {
        TB.setLogLevel(TB.DEBUG);
        session = TB.initSession("<?php echo $conversation['Conversation']['sessionID']?>");
        // w
        // Add event listeners
        session.addEventListener('sessionConnected', sessionConnectedHandler);
        session.addEventListener('streamCreated', streamCreatedHandler);
    }
    function sessionConnectedHandler(event) {
        console.log("session connected..finally!!!!!");
        // Subscribe to all streams currently in the Session
        for (var i = 0; i < event.streams.length; i++) {
			if (session.connection.connectionId != event.streams[i].connection.connectionId)
				addStream(event.streams[i]);
        }
        document.getElementById('disconnectedLink').style.display = "inline-block"; 
        console.log("session connected");

	// start publishing
	startPublishing();
    }
    function streamCreatedHandler(event) {
        // Subscribe to the newly created streams
        for (var i = 0; i < event.streams.length; i++) {
			if (session.connection.connectionId != event.streams[i].connection.connectionId)
				addStream(event.streams[i]);
        }
    }

    function connect() {
        console.log("clicked connect");
        session.connect(apiKey, "<?php echo $conversation['Conversation']['token2']?>" );
        console.log("connecting...");
    }

    function exceptionHandler(event) {
        if (event.code == 1013) {
            document.body.innerHTML = "This page is trying to connect a third client to an OpenTok peer-to-peer session. " + "Only two clients can connect to peer-to-peer sessions.";
        }
        else {
            alert("Exception: " + event.code + "::" + event.message);
        }
    }

	var lastSubscriber;
	
    function addStream(stream) {
	    var subscriberDiv = document.createElement('div');
        subscriberDiv.setAttribute('id', stream.streamId);
		document.getElementById("subscribers").appendChild(subscriberDiv);
        var subscriberProps = {width: VIDEO_WIDTH, height: VIDEO_HEIGHT};
        subscribers[stream.streamId] = session.subscribe(stream, subscriberDiv.id, subscriberProps);
		
		lastSubscriber = subscribers[stream.streamId];
		parse_init(stream.streamId);
    }

    function disconnect() {
        session.disconnect();
        document.getElementById('disconnectedLink').style.display = 'none';
		window.location = window.location.toString().replace("view", "setcompleted");
	}

	function startPublishing() {
		if (!publisher) {
                	var parentDiv = document.getElementById("mycamera");
	                var publisherDiv = document.createElement('div'); // Create a div for the publisher to replace
        	        publisherDiv.setAttribute('id', 'opentok_publisher');
                	parentDiv.appendChild(publisherDiv);

	                var publisherProps = new Object();
			publisherProps.publishVideo = false;
			publisherProps.publishAudio = true;

        	        publisher = TB.initPublisher(apiKey, publisherDiv.id, publisherProps);  // Pass the replacement div id and properties
                	session.publish(publisher);
	                //show('unpublishLink');
        	        //hide('publishLink');
	         }
	}



	document.body.style.overflow="hidden";

    window.onload = function() {
        connect();
    }
</script>

<script src="http://www.omghelp.net/test/js/omgparse.js" type="text/javascript" charset="utf-8"></script>
<script src="http://www.omghelp.net/test/js/base64-binary.js" type="text/javascript" charset="utf-8"></script>

<script>
	var currentImageUrl;

	function cap() {
		enable_doodles();
		var imgdata = lastSubscriber.getImgData();
		parse_push_png( imgdata , done_cap_send);
		setBackgroundBase64( imgdata );		
	}
					
	function setBackgroundPng(data) {
		bgCanvas = document.getElementById("doodlebg");
		//console.log(data);
		bgCanvas.style.background="url(" + data + ")";
	}

	function setBackgroundBase64(data) {
		setBackgroundPng("data:image/png;base64," + data.replace(/\n/g, ''));
	}
					
	// Done pushing file to server, notify client to load img
	function done_cap_send(img_url) {
		// TODO store filename somewhere
		parse_push_message( "setimage", convoid , "imageurl=" + img_url, done_preload_push);
		currentImageUrl = img_url;
	}
					
	function done_preload_push(result) {
		// send doodle callback as well to force client to display
		done_doodling();					
	}
	
	function done_doodling() {
		parse_push_message( "setoverlay", convoid, "overlaydata=" + encodeURIComponent(canvasToImage(flattenCanvas, 'rgba(255, 0, 0, 0)').substr(22)) + "&imageurl=" + currentImageUrl, done_doodling_push);
	}
	
	function close_remote_doodle() {
		parse_push_message( "removeimage", convoid, "", done_doodling_push);
	}

	function disable_doodles() {
		document.getElementById('doodle_area').style.visibility = 'hidden';
	}

	function enable_doodles() {
		document.getElementById('doodle_area').style.visibility = 'visible';
	}

	var ready_to_send = true;
	function done_doodling_push() {
		ready_to_send = true;
	}
	
	function show_filepicker() {
		filepicker.getFile( ['image/jpg','image/png'], {
			'multiple': false,
			'container': 'window',
			'services':[
				filepicker.SERVICES.BOX,
				filepicker.SERVICES.COMPUTER,
				filepicker.SERVICES.GMAIL],
			'location': filepicker.SERVICES.BOX
			}, function(response){
				//outp = JSON.stringify(response);
				filepicker.getContents(response, true, function(fileData) {
					// now we have a base64 image data
					//alert(fileData);
					scale_imported_image(fileData);
				});
				//alert(response);
			});
	}

	function scale_imported_image(imageb64) {
		var tmpImg = new Image();
		tmpImg.onload = function() {
			// tmpImg is loaded
			//alert("Loaded!");

			 var ratio = tmpImg.width / tmpImg.height;
			var newW, newH;

			if(ratio > 1.5) {
				newW = 480;
				newH = 480 / ratio;
			} else if(ratio < 1.5) {
				newH = 320;
				newW = 320 * ratio;
			} else {
				// r1 == r2
				newH = 320; newW = 480;
			}

			// draw onto canvas!
			var tcv = document.createElement("canvas");
			var tcv2 = document.createElement("canvas");
			tcv.width = tmpImg.width; tcv.height = tmpImg.height;
			tcv2.width = 480; tcv2.height = 320;
			//alert(tmpImg.width + " " + tmpImg.height);
			tcv.getContext("2d").drawImage(tmpImg, 0, 0, tmpImg.width, tmpImg.height);
			tcv2.getContext("2d").drawImage(tcv, 0, 0, tmpImg.width, tmpImg.height, (480-newW)/2, (320-newH)/2, newW, newH);
			imgdata = tcv2.toDataURL();
			enable_doodles();
			// draw
			parse_push_png( imgdata.substr(22) , done_cap_send);
	                setBackgroundPng( imgdata );

			//alert(ratio);
		};
		tmpImg.src = "data:image/png;base64," + imageb64;
	}

</script>


<div id="work_space">
	<div id="screen_shots" style="position: absolute;  bottom: 25px;">
		<h2>Screen Shots</h2>
		<div style="overflow-y:auto; overflow-x:hidden; position: absolute; top: 50px; left: 0px; right: 0px; bottom: 0px;">
			<?php // echo $this->Html->image('test.PNG', array('alt' => 'test')); ?>
			<?php // echo $this->Html->image('test.PNG', array('alt' => 'test')); ?>
		</div>
    </div>
    <div id="video_area">
	<div>
            <div id="video_feed">
        	    <div id="subscribers">
	                <h2>Remote Video</h2>
        	    </div>
	            <div id="links">
        	        <span id = "disconnectedLink" onClick="javascript:disconnect()">Finish helping!</span>
					
	                <span id = "capturePicture" onclick="cap()">Snap shot! </span>

			<span id = "capturePicture" onclick="show_filepicker()">Show file</span>
        	    </div>

	     </div>

	    <div style="position: absolute; width: 0px; height: 0px; top: -1000px; left: -1000px; display: block;">
		<br />
		<br />
		<br />
		<br />
		<br />
		<br />
		<div id="mycamera"></div>
	    </div>
	</div>

        <div id="doodle_area" style="visibility: hidden; position: relative; float:left; width: 50%; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; -o-user-select: none; user-select: none; ">            
			
			<div id="doodlediv" style="width: 480px; height: 320px; display: inline-block; position: relative">
				<div id="doodlebg" style="position: absolute; top: 0; left: 0; width: 480px; height: 320px; display: block; background:blue;"></div>
				<canvas id="doodle" width="480" height="320" style="position: absolute; top: 0; left: 0;"></canvas>
			</div>

			<div id="menu_container" style="background:grey; font-size: 14px; font-family: arial; position: relative; left: 50%; top: 5px; margin-left: -240px; width:480px; height: 45px;">
				<!-- keeps menu sized properly -->
				<style>
					#doodlemenu select {
						margin-top: 7px;
						font-size: 12px;
						height: 18px;
					}
					
					#doodlemenu .button {
						cursor: pointer;
					}
					#doodlemenu .gui {
						color: #ccc;
						background: #36393a url(img/menu.png) left bottom repeat-x;
						padding: 5px 10px;
						text-align: center;
						text-transform: uppercase;
						line-height: 18px;
						cursor: default;
						text-shadow: 0px 1px 1px black;
						-webkit-box-shadow: 0px 1px 5px rgba(0, 0, 0, 0.8);
					}
				</style>
				<div id="doodlemenu" width="480" height="50">
				</div>
			</div>
			
			<script type="text/javascript" src="http://www.omghelp.net/test/js/brushes/simple.js"></script>
			<script type="text/javascript" src="http://www.omghelp.net/test/js/brushes/arrow.js"></script>
			<script type="text/javascript" src="http://www.omghelp.net/test/js/colorutils.js"></script>
			<script type="text/javascript" src="http://www.omghelp.net/test/js/colorselector.js"></script>
			<script type="text/javascript" src="http://www.omghelp.net/test/js/palette.js"></script>
			<script type="text/javascript" src="http://www.omghelp.net/test/js/menu.js"></script>
			<script type="text/javascript" src="http://www.omghelp.net/test/js/about.js"></script>
			<script type="text/javascript" src="http://www.omghelp.net/test/js/main.js"></script>
			
        </div>

    </div>
</div>
