//
//  HomeViewController.m
//  omgHelp
//
//  Created by Cong Bach on 11/8/12.
//  Copyright (c) 2012 VSee Lab, Inc. All rights reserved.
//

#import "HomeViewController.h"
#import "NetworkManager.h"
#import "JSONKit.h"
#import "AppDelegate.h"
#import "CategoriesViewController.h"
#import "CallViewController.h"
#import "OMGDebugger.h"

@interface HomeViewController ()

@end

@implementation HomeViewController

@synthesize activityIndicator = _activityIndicator;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)dealloc
{
    [super dealloc];
}


- (void)viewDidAppear:(BOOL)animated
{
    [self.activityIndicator startAnimating];
    [[NetworkManager sharedNetworkManager] requestInitializingInfoWithCallBackTarget:self selector:@selector(startWithJSON:)];
}

- (void)viewDidLoad
{
    [super viewDidLoad];
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}


- (void)startWithJSON:(id)JSON
{
    if ([JSON valueForKeyPath:@"MenuCategories"])
        [self displayCategoriesFromJSON:JSON];
    else
        [self resumeCallFromJSON:JSON];
}


- (void)displayCategoriesFromJSON:(id)JSON
{
    [self.activityIndicator stopAnimating];
    IntroViewController *intro = [[IntroViewController alloc] init];
    
    AppDelegate *delegate = [[UIApplication sharedApplication] delegate];
    if (!delegate.instructionsShown) {
      delegate.instructionsShown = YES;
      [self.navigationController presentModalViewController:intro animated:NO];
    }
    
    CategoriesViewController *categoriesViewController = [[[CategoriesViewController alloc] initWithJSONCategoriesArray:[JSON valueForKeyPath:@"MenuCategories"]] autorelease];
//    categoriesViewController.navigationItem.titleView = [[UIView alloc] initWithFrame:CGRectZero];  
    categoriesViewController.title = @"omg.HELP";
    categoriesViewController.navigationItem.hidesBackButton = YES;
    [[[AppDelegate sharedAppDelegate] navigationController] pushViewController:categoriesViewController animated:NO];
    
}


- (void)resumeCallFromJSON:(id)JSON
{
    CallViewController *callViewController = [[[CallViewController alloc] initWithJSON:JSON] autorelease];
    [[[AppDelegate sharedAppDelegate] navigationController] pushViewController:callViewController animated:YES];
}


- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

@end
