//
//  NetworkManager.h
//  omgHelp
//
//  Created by Cong Bach on 11/8/12.
//  Copyright (c) 2012 VSee Lab, Inc. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NetworkManager : NSObject {
    NSString *_clientId, *_vendorId;
}

@property (retain, nonatomic) NSString *clientId, *vendorId;

- (void)requestInitializingInfoWithCallBackTarget:(id)target selector:(SEL)selector;
- (void)startCallWithCategoryId:(int)categoryId callbackTarget:(id)target selector:(SEL)selector;
- (void)pullCallInfoWithSessionId:(NSString *)sessionId callbackTarget:(id)target selector:(SEL)selector;
- (void)endCallWithSessionId:(NSString *)sessionId;

+ (NetworkManager *)sharedNetworkManager;

@end
