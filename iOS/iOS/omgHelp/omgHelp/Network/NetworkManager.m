//
//  NetworkManager.m
//  omgHelp
//
//  Created by Cong Bach on 11/8/12.
//  Copyright (c) 2012 VSee Lab, Inc. All rights reserved.
//

#import "NetworkManager.h"
#import "AFJSONRequestOperation.h"
#import "OMGDebugger.h"

@implementation NetworkManager

@synthesize clientId = _clientId, vendorId = _vendorId;


static NetworkManager *_sharedNetworkManager;


+ (NetworkManager *)sharedNetworkManager
{
    if (! _sharedNetworkManager)
        _sharedNetworkManager = [[NetworkManager alloc] init];
    return _sharedNetworkManager;
}


- (id)init
{
    if (self = [super init])
    {
//        CFUUIDRef uuidObj = CFUUIDCreate(nil);
//        _clientId = (NSString*)CFUUIDCreateString(nil, uuidObj);
//        CFRelease(uuidObj);
        self.clientId = [UIDevice currentDevice].uniqueIdentifier;
        self.vendorId = @"0";
    }
    return self;
}


- (void)dealloc
{
    self.clientId = nil;
    self.vendorId = nil;
    
    [super dealloc];
}


- (void)requestInitializingInfoWithCallBackTarget:(id)target selector:(SEL)selector
{
    NSString *stringUrl = [NSString stringWithFormat:@"http://omghelp.net/mobileapi/startup/%@/%@", self.clientId, self.vendorId, nil];
    NSURL *url = [NSURL URLWithString:stringUrl];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    
    AFJSONRequestOperation *operation = [AFJSONRequestOperation JSONRequestOperationWithRequest:request success:^(NSURLRequest *request, NSHTTPURLResponse *response, id JSON) {
            if ([target respondsToSelector:selector])
                [target performSelectorOnMainThread:selector withObject:JSON waitUntilDone:NO];
    } failure:^(NSURLRequest *request, NSHTTPURLResponse *response, NSError *error, id JSON) {
        [[OMGDebugger sharedDebugger] logError:error];
    }];
    
    [operation start];
}


- (void)startCallWithCategoryId:(int)categoryId callbackTarget:(id)target selector:(SEL)selector
{
    NSString *stringUrl = [NSString stringWithFormat:@"http://omghelp.net/mobileapi/startconversation/%@/%d", self.clientId, categoryId, nil];
    NSURL *url = [NSURL URLWithString:stringUrl];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    
    AFJSONRequestOperation *operation = [AFJSONRequestOperation JSONRequestOperationWithRequest:request success:^(NSURLRequest *request, NSHTTPURLResponse *response, id JSON) {
        if ([target respondsToSelector:selector])
            [target performSelectorOnMainThread:selector withObject:JSON waitUntilDone:NO];
    } failure:^(NSURLRequest *request, NSHTTPURLResponse *response, NSError *error, id JSON) {
        [[OMGDebugger sharedDebugger] logError:error];
    }];
    
    [operation start];
}


- (void)pullCallInfoWithSessionId:(NSString *)sessionId callbackTarget:(id)target selector:(SEL)selector
{
    NSString *stringUrl = [NSString stringWithFormat:@"http://omghelp.net/mobileapi/getconversation/%@", sessionId, nil];
    NSURL *url = [NSURL URLWithString:stringUrl];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    
    AFJSONRequestOperation *operation = [AFJSONRequestOperation JSONRequestOperationWithRequest:request success:^(NSURLRequest *request, NSHTTPURLResponse *response, id JSON) {
        if ([target respondsToSelector:selector])
            [target performSelectorOnMainThread:selector withObject:JSON waitUntilDone:NO];
    } failure:^(NSURLRequest *request, NSHTTPURLResponse *response, NSError *error, id JSON) {
        [[OMGDebugger sharedDebugger] logError:error];
    }];
    
    [operation start];
}


- (void)endCallWithSessionId:(NSString *)sessionId
{
    NSString *stringUrl = [NSString stringWithFormat:@"http://omghelp.net/mobileapi/closeconversation/%@", sessionId];
    
    NSURL *url = [NSURL URLWithString:stringUrl];
    NSURLRequest *request = [NSURLRequest requestWithURL:url];
    
    AFJSONRequestOperation *operation = [AFJSONRequestOperation JSONRequestOperationWithRequest:request success:nil failure:^(NSURLRequest *request, NSHTTPURLResponse *response, NSError *error, id JSON) {
        [[OMGDebugger sharedDebugger] logError:error];
    }];
    
    [operation start];
    [operation waitUntilFinished];
}

@end
