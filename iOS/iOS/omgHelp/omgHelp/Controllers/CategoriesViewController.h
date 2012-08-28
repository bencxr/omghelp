//
//  CategoriesViewController.h
//  omgHelp
//
//  Created by Cong Bach on 11/8/12.
//  Copyright (c) 2012 VSee Lab, Inc. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface CategoriesViewController : UIViewController

@property (retain, nonatomic) id JSONCategoriesArray;

- (id)initWithJSONCategoriesArray:(id)JSONCategoriesArray;

@end
