//
//  CallViewController.m
//  omgHelp
//
//  Created by Cong Bach on 11/8/12.
//  Copyright (c) 2012 VSee Lab, Inc. All rights reserved.
//

#import "CallViewController.h"
#import "AppDelegate.h"
#import "NetworkManager.h"
#import "OMGDebugger.h"
#import <TargetConditionals.h>

@interface CallViewController ()

@end

@implementation CallViewController

static NSString *const kApiKey = @"17077042";

@synthesize categoryId = _categoryId;
@synthesize categoryName = _categoryName;
@synthesize activityIndicator = _activityIndicator;
@synthesize connectingLabel = _connectingLabel;
@synthesize videoView = _videoView;
@synthesize imageView = _imageView;
@synthesize overlayImageView = _overlayImageView;
@synthesize overlayView = _overlayView;
@synthesize JSON = _JSON;
@synthesize token = _token;
@synthesize sessionId = _sessionId;
@synthesize opentokSession = _opentokSession;
@synthesize opentokPublisher = _opentokPublisher;
@synthesize opentokSubscriber = _opentokSubscriber;
@synthesize pullRequestTimer = _pullRequestTimer;
@synthesize categoryLabel = _categoryLabel;
@synthesize helperNameLabel = _helperNameLabel;

#pragma mark - CallViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}


- (id)init
{
    if (self = [super init])
    {
        
    }
    return self;
}


- (id)initWithCategoryId:(int)categoryId categoryName:(NSString *)categoryName
{
    if (self = [self init])
    {
        self.categoryId = categoryId;
        self.categoryName = categoryName;
    }
    return self;
}


- (id)initWithJSON:(id)JSON
{
    if (self = [self init])
    {
        self.JSON = JSON;
    }
    return self;
}


- (void)dealloc
{
    self.JSON = nil;
    self.token = nil;
    self.sessionId = nil;
    self.opentokSession = nil;
    self.opentokPublisher = nil;
    self.opentokSubscriber = nil;
    self.pullRequestTimer = nil;
    self.categoryName = nil;
    
    [super dealloc];
}


- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    [[[AppDelegate sharedAppDelegate] navigationController] navigationBar].hidden = YES;
    
    // hack to get correct video ration
    [[UIDevice currentDevice] performSelector:NSSelectorFromString(@"setOrientation:") withObject:(id)UIInterfaceOrientationLandscapeRight];
    
    [self.categoryLabel setText:self.categoryName];
    [self.helperNameLabel setText:@"Waiting for support…"];
    [self.activityIndicator startAnimating];
    self.imageView.hidden = YES;
    UITapGestureRecognizer *tapRecoginzer = [[[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(tapGestureCallback:)] autorelease];
    [self.videoView addGestureRecognizer:tapRecoginzer];
    
    if (self.JSON)
        [self startCallWithJSON:self.JSON];
    else
        [[NetworkManager sharedNetworkManager] startCallWithCategoryId:self.categoryId callbackTarget:self selector:@selector(startCallWithJSON:)];
}


- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
}


- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}


- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return (interfaceOrientation == UIInterfaceOrientationLandscapeRight);
}

- (void)pullCallInfo:(id)sender
{
    [[NetworkManager sharedNetworkManager] pullCallInfoWithSessionId:self.sessionId callbackTarget:self selector:@selector(updateCallWithJSON:)];
}


- (void)startCallWithJSON:(id)JSON
{
    self.JSON = JSON;
    
    self.token = [JSON valueForKeyPath:@"Conversation.Token"];
    self.sessionId = [JSON valueForKeyPath:@"Conversation.SessionId"];
    [self.categoryLabel setText:[JSON valueForKeyPath:@"Conversation.CategoryName"]];
    NSString *helperName = [JSON valueForKeyPath:@"Conversation.HelperName"];
    if (helperName && (NSNull *)helperName != [NSNull null])
        [self.helperNameLabel setText:helperName];
    
    //self.overlayImageView.hidden = YES;
    
#if !(TARGET_IPHONE_SIMULATOR)
    [self doConnect];
#else
    self.pullRequestTimer = [NSTimer scheduledTimerWithTimeInterval:5.0f target:self selector:@selector(pullCallInfo:) userInfo:nil repeats:YES];
    [self.pullRequestTimer fire];
#endif
}


- (void)updateCallWithJSON:(id)JSON
{
    self.JSON = JSON;
    
    @synchronized(self)
    {
    
    NSString *helperName = [JSON valueForKeyPath:@"Conversation.HelperName"];
    if (helperName && (NSNull *)helperName != [NSNull null])
        [self.helperNameLabel setText:helperName];
    
    if ([[JSON valueForKeyPath:@"Conversation.Completed"] boolValue])
    {
        [self endCall:nil];
        return;
    }
    
    id imageJSON = [JSON valueForKeyPath:@"Conversation.Image"];
    if ([[imageJSON valueForKeyPath:@"Enabled"] boolValue])
    {
        NSString *photoUrl = [imageJSON valueForKeyPath:@"Photo"];
        if (photoUrl && (NSNull *)photoUrl != [NSNull null] && [photoUrl length])
        {
            
            [self.activityIndicator startAnimating];
            self.activityIndicator.hidden = NO;
            
            NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:photoUrl] cachePolicy:NSURLRequestUseProtocolCachePolicy timeoutInterval:30.0];
            [request setHTTPShouldHandleCookies:NO];
            [request setHTTPShouldUsePipelining:YES];
            
            [self.imageView setImageWithURLRequest:request
                                  placeholderImage:nil
                                           success:^(NSURLRequest *request, NSHTTPURLResponse *response, UIImage *image) {
                                               [self.activityIndicator stopAnimating];
                                               self.activityIndicator.hidden = YES;
            
                                               self.imageView.hidden = NO;
                                               self.opentokPublisher.view.hidden = YES;
                                               
                                               NSString *overlayData = [imageJSON valueForKeyPath:@"Overlay"];
                                               if (overlayData && (NSNull *)overlayData != [NSNull null] && [overlayData length])
                                               {
                                                   [self.overlayImageView performSelectorOnMainThread:@selector(setImage:) withObject:[UIImage imageWithData:[NSData base64DataFromString:overlayData]] waitUntilDone:NO];
                                               }
                                               else
                                               {
                                                   [self.overlayImageView setImage:nil];
                                               }
                                           }
                                           failure:^(NSURLRequest *request, NSHTTPURLResponse *response, NSError *error) {
                                               [[OMGDebugger sharedDebugger] logError:error];
                                           }];
        }
        else
        {
            NSString *overlayData = [imageJSON valueForKeyPath:@"Overlay"];
            if (overlayData && (NSNull *)overlayData != [NSNull null] && [overlayData length])
                [self.overlayImageView performSelectorOnMainThread:@selector(setImage:) withObject:[UIImage imageWithData:[NSData base64DataFromString:overlayData]] waitUntilDone:NO];
            else
                [self.overlayImageView setImage:nil];
        }
    }
    else
    {
        self.imageView.hidden = YES;
        self.opentokPublisher.view.hidden = NO;
    }
        
        
    }
}


- (void)tapGestureCallback:(id)sender
{
    [UIView beginAnimations:nil context:nil];
    BOOL hidden = ! self.overlayView.hidden;
    float alpha = hidden ? 0.0f : 1.0f;
    self.overlayView.hidden = hidden;
    self.overlayView.alpha = alpha;
    [UIView setAnimationDuration:1.0f];
    [UIView commitAnimations];
}


#pragma mark - IBActions

- (IBAction)endCall:(id)sender
{
    [self doUnpublish];
    [self doDisconnect];
    [[NetworkManager sharedNetworkManager] endCallWithSessionId:self.sessionId];

    [[[AppDelegate sharedAppDelegate] navigationController] popToRootViewControllerAnimated:NO];
}


#pragma mark - OpenTok methods

- (void)doConnect 
{
    _opentokSession = [[OTSession alloc] initWithSessionId:_sessionId delegate:self];
    [_opentokSession connectWithApiKey:kApiKey token:_token];
}

- (void)doDisconnect 
{
    [_opentokSession disconnect];
}

- (void)doPublish
{
    _opentokPublisher = [[OTPublisher alloc] initWithDelegate:self];
    _opentokPublisher.publishAudio = YES;
    _opentokPublisher.publishVideo = YES;
    _opentokPublisher.cameraPosition = AVCaptureDevicePositionBack;
    [_opentokSession publish:_opentokPublisher];
    [self.videoView addSubview:_opentokPublisher.view];
    [_opentokPublisher.view setFrame:CGRectMake(0, 0, self.videoView.frame.size.width, self.videoView.frame.size.height)];
    [_opentokPublisher.view setUserInteractionEnabled:NO];
    [self.activityIndicator stopAnimating];
    self.activityIndicator.hidden = YES;
    
    self.pullRequestTimer = [NSTimer scheduledTimerWithTimeInterval:1.0f target:self selector:@selector(pullCallInfo:) userInfo:nil repeats:YES];
    [self.pullRequestTimer fire];
}

- (void)doUnpublish
{
    [_opentokSession unpublish:_opentokPublisher];
    [self.pullRequestTimer invalidate];
    self.pullRequestTimer = nil;
}


#pragma mark - OTSessionDelegate methods

- (void)sessionDidConnect:(OTSession*)session
{
    NSLog(@"sessionDidConnect: %@", session.sessionId);
    NSLog(@"- connectionId: %@", session.connection.connectionId);
    NSLog(@"- creationTime: %@", session.connection.creationTime);
    [self doPublish];
}

- (void)sessionDidDisconnect:(OTSession*)session 
{
    NSLog(@"sessionDidDisconnect: %@", session.sessionId);
}

- (void)session:(OTSession*)session didFailWithError:(OTError*)error
{
    NSLog(@"session: didFailWithError:");
    NSLog(@"- error code: %d", error.code);
    NSLog(@"- description: %@", error.localizedDescription);
    
    [[OMGDebugger sharedDebugger] logError:[NSString stringWithFormat:@"Error connecting to session:%@", error]];
}

- (void)session:(OTSession*)mySession didReceiveStream:(OTStream*)stream
{
    NSLog(@"session: didReceiveStream:");
    NSLog(@"- connection.connectionId: %@", stream.connection.connectionId);
    NSLog(@"- connection.creationTime: %@", stream.connection.creationTime);
    NSLog(@"- session.sessionId: %@", stream.session.sessionId);
    NSLog(@"- streamId: %@", stream.streamId);
    NSLog(@"- type %@", stream.type);
    NSLog(@"- creationTime %@", stream.creationTime);
    NSLog(@"- name %@", stream.name);
    NSLog(@"- hasAudio %@", (stream.hasAudio ? @"YES" : @"NO"));
    NSLog(@"- hasVideo %@", (stream.hasVideo ? @"YES" : @"NO"));
    
    if (!_opentokSubscriber && ![stream.connection.connectionId isEqualToString: _opentokSession.connection.connectionId])
    {
        [[OMGDebugger sharedDebugger] log:@"Received stream"];
        _opentokSubscriber = [[OTSubscriber alloc] initWithStream:stream delegate:self];
        _opentokSubscriber.subscribeToVideo = NO;
        _opentokSubscriber.subscribeToAudio = YES;
    }
}

- (void)session:(OTSession*)session didDropStream:(OTStream*)stream
{
    NSLog(@"session didDropStream (%@)", stream.streamId);
}


#pragma mark - OTPublisherDelegate methods

- (void)publisher:(OTPublisher*)publisher didFailWithError:(OTError*) error
{
    NSLog(@"publisher: %@ didFailWithError:", publisher);
    NSLog(@"- error code: %d", error.code);
    NSLog(@"- description: %@", error.localizedDescription);
}

- (void)publisherDidStartStreaming:(OTPublisher *)publisher
{
    NSLog(@"publisherDidStartStreaming: %@", publisher);
    NSLog(@"- publisher.session: %@", publisher.session.sessionId);
    NSLog(@"- publisher.name: %@", publisher.name);
}

-(void)publisherDidStopStreaming:(OTPublisher*)publisher
{
    NSLog(@"publisherDidStopStreaming:%@", publisher);
}


#pragma mark - OTSubscriberDelegate methods

- (void)subscriberDidConnectToStream:(OTSubscriber*)subscriber
{
    NSLog(@"subscriberDidConnectToStream (%@)", subscriber.stream.connection.connectionId);
}

- (void)subscriberVideoDataReceived:(OTSubscriber*)subscriber {
    NSLog(@"subscriberVideoDataReceived (%@)", subscriber.stream.streamId);
}

- (void)subscriber:(OTSubscriber *)subscriber didFailWithError:(OTError *)error
{
    NSLog(@"subscriber: %@ didFailWithError: ", subscriber.stream.streamId);
    NSLog(@"- code: %d", error.code);
    NSLog(@"- description: %@", error.localizedDescription);
}

@end
