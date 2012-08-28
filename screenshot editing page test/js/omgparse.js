var config;


function parse_init(stream_id)
{
	config = {
		parseAppId: "2wux7lfkzXLEo0h6CfzOt4yO9REEQnRZIlXl0tc3",
		parseRestKey: "0ASljcfcctUER6s3WXxPoAsmAJx6j2zCttqxIzps",
		streamName: stream_id
	};

}

function parse_push_png(encoded_data)
{
	var filename = config.streamName + "-" + (new Date()).getTime() + ".png";
    $.ajax({
        url: 'https://api.parse.com/1/files/' + filename,
        headers: {
            'X-Parse-Application-Id': config.parseAppId,
            'X-Parse-REST-API-Key': config.parseRestKey
        },
        type: 'POST',
        data: window.atob(encoded_data),
        dataType: 'image/png',
		
        success: function (data) {
            alert("Image upload success");
        },
        error: function () {
            alert('Problem uploading photo');
        }
    });
}

function parse_push_message(json_string)
{
    $.ajax({
        url: ' https://api.parse.com/1/push',
        headers: {
            'X-Parse-Application-Id': config.parseAppId,
            'X-Parse-REST-API-Key': config.parseRestKey
        },
        type: 'POST',
        data: json_string,
        dataType: 'application/json',
		
        success: function (data) {
            alert("Json send success");
        },
        error: function () {
            alert('Json send fail');
        }
    });
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

