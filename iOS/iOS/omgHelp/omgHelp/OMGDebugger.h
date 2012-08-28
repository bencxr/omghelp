//
//  OMGDebugger.h
//  omgHelp
//
//  Created by Cong Bach on 11/8/12.
//  Copyright (c) 2012 VSee Lab, Inc. All rights reserved.
//

#import <Foundation/Foundation.h>

#define BUILD_RELEASE   1
#define BUILD_DEBUG     1

@interface OMGDebugger : NSObject

- (void)log:(id)msg;
- (void)logError:(id)msg;


+ (OMGDebugger *)sharedDebugger;

@end
