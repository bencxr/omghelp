//
//  OMGDebugger.m
//  omgHelp
//
//  Created by Cong Bach on 11/8/12.
//  Copyright (c) 2012 VSee Lab, Inc. All rights reserved.
//

#import "OMGDebugger.h"

@implementation OMGDebugger

static OMGDebugger *_sharedDebugger;

+ (OMGDebugger *)sharedDebugger
{
    if (! _sharedDebugger)
        _sharedDebugger = [[OMGDebugger alloc] init];
    return _sharedDebugger;
}

- (void)log:(id)msg
{
#if BUILD_RELEASE
    NSLog(@"%@", msg);
#elif BUILD_DEBUG
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Log"
                                                    message:[NSString stringWithFormat:@"%@", msg]
                                                   delegate:self
                                          cancelButtonTitle:@"OK"
                                          otherButtonTitles:nil];
    [alert show];
    //[alert performSelectorOnMainThread:@selector(show) withObject:nil waitUntilDone:YES];
    [alert release];
#endif
}

- (void)logError:(id)msg
{
    [self log:msg];
}

@end
