//
//  CallViewController.h
//  omgHelp
//
//  Created by Cong Bach on 11/8/12.
//  Copyright (c) 2012 VSee Lab, Inc. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <OpenTok/Opentok.h>

@interface CallViewController : UIViewController<OTSessionDelegate, OTPublisherDelegate, OTSubscriberDelegate>

@property (assign, nonatomic) int categoryId;
@property (retain, nonatomic) NSString *categoryName;
@property (assign, nonatomic) IBOutlet UIActivityIndicatorView *activityIndicator;
@property (assign, nonatomic) IBOutlet UILabel *connectingLabel;
@property (assign, nonatomic) IBOutlet UIView *videoView;
@property (assign, nonatomic) IBOutlet UIImageView *imageView;
@property (assign, nonatomic) IBOutlet UIImageView *overlayImageView;
@property (assign, nonatomic) IBOutlet UIView *overlayView;
@property (assign, nonatomic) IBOutlet UILabel *categoryLabel;
@property (assign, nonatomic) IBOutlet UILabel *helperNameLabel;
@property (retain, nonatomic) id JSON;
@property (retain, nonatomic) NSString *token;
@property (retain, nonatomic) NSString *sessionId;
@property (retain, nonatomic) OTSession *opentokSession;
@property (retain, nonatomic) OTPublisher *opentokPublisher;
@property (retain, nonatomic) OTSubscriber *opentokSubscriber;
@property (retain, nonatomic) NSTimer *pullRequestTimer;

- (id)initWithCategoryId:(int)categoryId categoryName:(NSString *)categoryName;
- (id)initWithJSON:(id)JSON;
- (void)startCallWithJSON:(id)JSON;
- (void)pullCallInfo:(id)sender;
- (void)updateCallWithJSON:(id)JSON;

- (void)doConnect;
- (void)doDisconnect;
- (void)doPublish;
- (void)doUnpublish;

- (void)tapGestureCallback:(id)sender;
- (IBAction)endCall:(id)sender;

@end
