//
//  NSData+Base64Decoding.h
//  omgHelp
//
//  Created by Cong Bach on 12/8/12.
//  Copyright (c) 2012 VSee Lab, Inc. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSData (Base64Decoding)

+ (NSData *) base64DataFromString:(NSString *)string;

@end
