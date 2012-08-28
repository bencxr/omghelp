//
//  CategoriesViewController.m
//  omgHelp
//
//  Created by Cong Bach on 11/8/12.
//  Copyright (c) 2012 VSee Lab, Inc. All rights reserved.
//

#import "CategoriesViewController.h"
#import "CallViewController.h"
#import "AppDelegate.h"
#import "OMGDebugger.h"

@interface CategoriesViewController ()

@end

@implementation CategoriesViewController

@synthesize JSONCategoriesArray = _JSONCategoriesArray;

#pragma mark - CategoriesViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}


- (id)initWithJSONCategoriesArray:(id)JSONCategoriesArray
{
    if (self = [super init])
    {
        self.JSONCategoriesArray = JSONCategoriesArray;
    }
    return self;
}


- (void)dealloc
{
    self.JSONCategoriesArray = nil;
    [super dealloc];
}


- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    [[[AppDelegate sharedAppDelegate] navigationController] navigationBar].hidden = NO;
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
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}


#pragma mark - UITableViewDataSource

- (UITableViewCell*)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{   
    UITableViewCell *cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:nil] autorelease];
    id categoryJSON = [self.JSONCategoriesArray objectAtIndex:[indexPath indexAtPosition:1]];
    [cell.textLabel setText:[categoryJSON valueForKeyPath:@"Category.name"]];
    [cell setAccessoryType:UITableViewCellAccessoryDisclosureIndicator];
    return cell;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return [self.JSONCategoriesArray count];
}


#pragma mark - UITableViewDelegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    id categoryJSON = [self.JSONCategoriesArray objectAtIndex:[indexPath indexAtPosition:1]];
    id subCategories = [categoryJSON valueForKeyPath:@"children"];
    
    if ([subCategories count])
    {
        CategoriesViewController *subCategoriesViewController = [[[CategoriesViewController alloc] initWithJSONCategoriesArray:subCategories] autorelease];
        subCategoriesViewController.title = [categoryJSON valueForKeyPath:@"Category.name"];
        [[[AppDelegate sharedAppDelegate] navigationController] pushViewController:subCategoriesViewController animated:YES];
    }
    else
    {
        CallViewController *callViewController = [[[CallViewController alloc] initWithCategoryId:[[categoryJSON valueForKeyPath:@"Category.id"] intValue] categoryName:[categoryJSON valueForKeyPath:@"Category.name"]] autorelease];
        callViewController.navigationItem.hidesBackButton = YES;
        callViewController.title = [categoryJSON valueForKeyPath:@"Category.name"];
        [[[AppDelegate sharedAppDelegate] navigationController] pushViewController:callViewController animated:YES];
    }
}


- (void)tableView:(UITableView *)tableView accessoryButtonTappedForRowWithIndexPath:(NSIndexPath *)indexPath
{
    [self tableView:tableView didSelectRowAtIndexPath:indexPath];
}

@end
