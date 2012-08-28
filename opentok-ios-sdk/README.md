OpenTok iOS SDK
===============

This package contains what you need to get you started using the OpenTok iOS SDK.

The OpenTok iOS SDK lets you use OpenTok video sessions in apps you build for iPad, iPhone, and iPod touch devices.
This means you can use OpenTok video sessions that connect iOS users with each other and with web clients.
For information on OpenTok, see http://www.tokbox.com/.

Documentation of the SDK is available here: 

http://www.tokbox.com/opentok/ios/docs/index.html

See the topic on "Getting Started with the OpenTok iOS SDK."

Also, look at the OpenTokHelloWorld sample in the sample directory. This is the simplest sample app. Then look at the OpenTokBasic sample, which uses more of the API. These sample apps are also at GitHub:

https://github.com/opentok/OpenTok-iOS-Hello-World<br>
https://github.com/opentok/OpenTok-iOS-Basic-Tutorial

Support is available at the OpenTok forums: http://www.tokbox.com/forums/

New features
------------

August 3, 2012

* This is version 1.2.0 of the OpenTok iOS SDK. 
* This release officially supports 3G and 4G in addition to wifi internet connections.
* Video quality in iOS is markedly improved in this release.
* This release officially supports image snapshot functionality.

June 5, 2012

* Added OTVideoView, which gives view hierarchy access to components of a viewable object.

April 23, 2012

* Fixed a bug where the aspect ratio for publishers was incorrect at certain orientations.
* Improved average connection time for subscribers. Delegates should receive the [OTSubscriberDelegate subscriberVideoDataReceived:] message a bit faster.

April 5, 2012

* [OTPublisher cameraPosition] -- The preferred camera position. When setting this property, if the change is possible, the publisher will use the camera
with the specified position. If the publisher has begun publishing, getting this property returns the current camera position; if the publisher has not yet
begun publishing, getting this property returns the preferred camera position. Valid values are defined in the AVCaptureDevicePosition enum.
  
Known issues
------------

See the list of Known Issues posted here:

https://github.com/opentok/opentok-ios-sdk/blob/master/docs/src/Using_iOS-template.md