var config;

function parse_init(stream_id)
{
	config = {
		parseAppId: "AmFR8LyEIpQXYePOUoFOsFUatSayYM4mVcpiTget",
		parseRestKey: "ObY82DR7kXa3DQ0BICBQaKcCPQhvIRNhnAfMec80",
		streamName: stream_id
	};

}

function parse_push_png(encoded_data, callback)
{
	var filename = 'https://api.parse.com/1/files/' + config.streamName + "-" + (new Date()).getTime() + ".png";
	
	var xhr = new XMLHttpRequest();
	xhr.open("POST", filename, true);
	xhr.setRequestHeader("X-Parse-Application-Id", config.parseAppId);
	xhr.setRequestHeader("X-Parse-REST-API-Key", config.parseRestKey);
	xhr.setRequestHeader("Content-Type", "image/png");
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4) {
			var result = JSON.parse(xhr.responseText);
			
			if (result) {
				// Done
				console.log(result.url);
				callback(result.url);
				
			} else {
				//alert('Problem uploading photo');
				// Error
			}	
		}
	}
	
	// TODO base64 decode before sending
	//xhr.send(window.atob(encoded_data.replace(/\n/g,'')));
	xhr.send(Base64Binary.decodeArrayBuffer(encoded_data));
}

function parse_push_message(method, convoid, data, callback)
{
	var xhr = new XMLHttpRequest();
	xhr.open("POST", 'http://www.omghelp.net/conversations/' + method + '/' + convoid, true);
	//xhr.setRequestHeader("X-Parse-Application-Id", config.parseAppId);
	//xhr.setRequestHeader("X-Parse-REST-API-Key", config.parseRestKey);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4) {
			var result = xhr.responseText;
			console.log(result);
			callback(result);
		}
	}
	console.log('http://www.omghelp.net/conversations/' + method + '/' + convoid);
	//console.log(data);
	xhr.send(data);
}



// Object push test
/*
var TestObject = Parse.Object.extend("TestObject");
var testObject = new TestObject();
	testObject.save({foo: "bar"}, {
	success: function(object) {
		alert("object put success");
	},
    
	error: function(model, error) {
		alert("object put fail");
    }
});
*/

