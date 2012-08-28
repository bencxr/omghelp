The OpenTokHello sample app
===========================

***Important:*** Be sure to git clone with --recursive to grab the required OpenTok iOS SDK submodule!

The OpenTokHello sample app is a basic sample app that shows the most basic features of the OpenTok iOS SDK.

Once the app connects to the OpenTok session, it publishes an audio-video stream, which is displayed onscreen. Then, the audio-video stream shows up as a subscribed stream (along with any other streams currently in the session).

Before you test the sample app, be sure to read [Using the OpenTok iOS SDK](http://www.tokbox.com/opentok/ios/docs/docs/Using_iOS.html).

Testing the sample app
----------------------

1. Open the OpenTokHelloWorld.xcodeproj file in XCode.

2. Connect your iOS device to a USB port on your Mac. Make sure that your device is connected to the internet.

3. Select the XCode Organizer (Window > Organizer), and make sure that your device is provisioned to work with the sample app. For more information,
see the section on "Setting up your development environment" at [this page](https://developer.apple.com/programs/ios/gettingstarted/) at
the Apple iOS Dev Center.

	Note that the OpenTok iOS SDK does not support publishing streams in the XCode iOS Simulator.
	To test OpenTok apps on an iOS device, you will need to register as an Apple iOS developer at
	[http://developer.apple.com/programs/register/](http://developer.apple.com/programs/register/).
	
4. In the main XCode project window, click the Run button (or select Product > Run).

	The app should start on your connected device. Once the app connects to the OpenTok session, it publishes an audio-video stream, which is displayed onscreen. Then, the audio-video stream shows up as a subscribed stream (along with any other streams currently in the session).

5. Close the app. Now set up the app to subscribe to audio-video streams other than your own:

	- In XCode, near the top of the ViewController.m file, change the `subscribeToSelf` property to be set to `NO`:
	
			static bool subscribeToSelf = NO;
	- Run the app on your iOS device again.
	- In a browser on your Mac, load the browser_demo.html file, included with the sample app, to add more streams to the session. 
	In the web page, click the Connect and Publish buttons.
	*Note:* If the web page asks you to set the Flash Player Settings, or if you do not see a display of your camera in the page, see the
	instructions in ["Flash Player Settings for local testing"](http://www.tokbox.com/opentok/api/tools/js/tutorials/helloworld.html#localTest).

6. You can generate a unique session ID at this URL:

	[http://staging.tokbox.com/hl/session/create](http://staging.tokbox.com/hl/session/create)

	Using a unique session ID prevents other test developers from seeing your published streams (and it keeps their streams out of your session). 
	In the ViewController.m file, change the `kSessionId` constant in the ViewController.m file to the new session ID. Also change the value of the 
	`sessionId` variable in the browser_demo.html page.

Understanding the code
----------------------

The ViewController.m file contains the main implementation code that includes use of the OpenTok iOS API.

### Initializing an OTSession object and connecting to an OpenTok session

When the view initially loads, the app allocates an OTSession object and sends it the `[OTSession initWithSessionId:andDelegate:]` message:

	- (void)viewDidLoad
	{
		[super viewDidLoad];
		
		_session = [[OTSession alloc] initWithSessionId:kSessionId
											andDelegate:self];
		[self doConnect];
	}

The `kSessionId` constant is the session ID string for the OpenTok session your app connects to. For testing purposes, you can generate a new session ID at http://staging.tokbox.com/hl/session/create.

The `_subscribers` array will be used to track OTSubscriber objects. These objects represent audio-video streams in the session that you subscribe to (and display onscreen).

The `doConnect` method sends the [OTSession connectWithApiKey:token:] to the the `_session` object:

	- (void)doConnect 
	{
		[_session connectWithApiKey:kApiKey token:kToken];
	}

The `kToken` constant is the token constant for the client connecting to the session. For testing purposes, this app uses the `"devtoken"` test token string. For a production version of your app, you will need to generate unique tokens for each user, using one of the OpenTok server-side libraries. See [Connection Token Creation](http://www.tokbox.com/opentok/api/tools/js/documentation/overview/token_creation.html).

When the app connects to the OpenTok session, the session delegate is sent the sessionDidConnect message:

	- (void)sessionDidConnect:(OTSession*)session
	{
		NSLog(@"sessionDidConnect (%@)", session.sessionId);
		[self doPublish];
	}

### Publishing an audio-video stream to a session

The `doPublish` method allocates and initializes an OTPublisher object, and then sends the `[OTSession publish:]` message to 
the `_session` object:

	- (void)doPublish
	{
		_publisher = [[OTPublisher alloc] initWithDelegate:self];
		[_publisher setName:[[UIDevice currentDevice] name]];
		[_session publish:_publisher];
		[self.view addSubview:_publisher.view];
		[_publisher.view setFrame:CGRectMake(0, 0, widgetWidth, widgetHeight)];
	}

The name of a stream is an optional string that appears at the bottom of the stream's view when the user taps the stream (or clicks it in
a browser). You set the name for the stream by setting the `name` property of the OTPublisher object before you send the `[OTSession publish]` message.

The view of the OTPublisher object is added as a subview of the ViewController's view.

### Subscribing to streams

When a stream is added to a session, the OTSessionDelegate's `session:didReceiveStream:` message is sent. It then allocates and initializes
an OTSubscriber object for the stream, and adds that OTSubscriber object to the `_subscribers` array:

	- (void)session:(OTSession*)mySession didReceiveStream:(OTStream*)stream
	{
		NSLog(@"session didReceiveStream (%@)", stream.streamId);
		
		if ( (subscribeToSelf && [stream.connection.connectionId isEqualToString: _session.connection.connectionId])
			|| 
			(!subscribeToSelf && ![stream.connection.connectionId isEqualToString: _session.connection.connectionId])
			) {
			if (!_subscriber) {
				_subscriber = [[OTSubscriber alloc] initWithStream:stream delegate:self];
			}
		}
	}

This app subscribes to one stream, at most. It either subscribes to the stream you publish, or it subscribes to one of the other streams
in the session (if there is one), based on the `subscribeToSelf` property, which is set at the top of the file:
	
	static bool subscribeToSelf = NO;
	
The connection ID for the stream you publish with match the connection ID for your session. (Note that in a real application, a client would not
normally subscribe to its own published stream. However, for this test app, it is convenient for the client to subscribe to its own stream.)

If the session does not yet have the `_subscriber` property set to an OTSubscriber object, it does so in this method.
	
The OTSubscriber's delegate is sent the `subscriberDidConnectToStream` message when the subscriber connects to the stream. At this point, the code adds the OTSubscriber's view to as a subview of the ViewController:

	- (void)subscriberDidConnectToStream:(OTSubscriber*)subscriber
	{
		NSLog(@"subscriberDidConnectToStream (%@)", subscriber.stream.connection.connectionId);
		[subscriber.view setFrame:CGRectMake(widgetWidth * subscriberCount++, widgetHeight, widgetWidth, widgetHeight)];
		[self.view addSubview:subscriber.view];
	}

The OpenTok iOS SDK supports a limited number of simultaneous audio-video streams in an app:

- On iPad 2 and iPad 3, the limit is four streams. An app can have up to four simultaneous subscribers, or one publisher and up to three subscribers.
- On all other supported iOS devices, the limit is two streams. An app can have up to two subscribers, or one publisher and one subscriber.

### Removing dropped streams

As streams leave the session (when clients disconnect or stop publishing), the OTSession delegate is sent the `session:didDropStream:` message. 

Subscriber objects are automatically removed from their superview when their stream is dropped. 

The code checks to see if you are subscribing to streams other than your own. If so, it checks to see if the dropped stream matches the subscriber's
stream. It does this by comparing the `streamId` property of the dropped OTStream object with the `stream.streamId` property of the OTSubscriber:

	- (void)session:(OTSession*)session didDropStream:(OTStream*)stream{
		NSLog(@"session didDropStream (%@)", stream.streamId);
		if (!subscribeToSelf
			&& _subscriber
			&& [_subscriber.stream.streamId isEqualToString: stream.streamId])
		{
			_subscriber = nil;
			[self updateSubscriber];
		}
	}

The `updateSubscriber` method subscribes to another stream in the session (other than the one you publish), if there is one. The
`OTSession.streams` property is a dictionary of all streams in a session:

	- (void)updateSubscriber {
		for (NSString* streamId in _session.streams) {
			OTStream* stream = [_session.streams valueForKey:streamId];
			if (stream.connection.connectionId != _session.connection.connectionId) {
				_subscriber = [[OTSubscriber alloc] initWithStream:stream delegate:self];
				break;
			}
		}
	}

Knowing when you have disconnected from the session
---------------------------------------------------

Finally, when the app disconnects from the session, the OTSubscriber's delegate is sent the `sessionDidDisconnect:` message:

	- (void)sessionDidDisconnect:(OTSession*)session
	{
		NSString* alertMessage = [NSString stringWithFormat:@"Session disconnected: (%@)", session.sessionId];
		NSLog(@"sessionDidDisconnect (%@)", alertMessage);
	}

If an app cannot connect to the session (perhaps because of no network connection), the OTSubscriber's delegate is sent
the `session: didFailWithError:` message:

	- (void)session:(OTSession*)session didFailWithError:(OTError*)error {
		NSLog(@"sessionDidFail");
		[self showAlert:[NSString stringWithFormat:@"There was an error connecting to session %@", session.sessionId]];
	}

## Next steps

The [OpenTokBasic sample app](https://github.com/opentok/OpenTok-iOS-Basic-Tutorial) uses more features of the OpenTok iOS SDK than the OpenTokHello app does.

For details on the full OpenTok iOS API, see the [reference documentation](http://www.tokbox.com/opentok/ios/docs/index.html).

