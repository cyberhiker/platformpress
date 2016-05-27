<?php
/**
 * PlatformPress comment form
 */

$I = new UITester($scenario );
$I->wantTo('Check PlatformPress form error message' );
$I->loginAs('user1', 'user1' );
$I->amOnPage( '/questions/comment/' );
$I->seeElement('#comment_form' );

$I->click('#comment_form .ap-btn-submit' );

$I->waitForText('This field is required', 30 );
$I->wantTo('Submit new question' );
$I->fillField([ 'name' => 'title' ], $I->questions['question1'] );
$I->fillTinyMceEditorById('description', 'Fusce iaculis condimentum nisi, nec commodo eros molestie at. Nullam libero erat, sollicitudin eu condimentum sit amet, rhoncus ut lacus. Integer vulputate nibh et diam sagittis in dictum mauris dapibus. ' );

$I->click('#comment_form .ap-btn-submit' );
$I->waitForJS( 'return jQuery.active == 0;',60 );
//$I->makeScreenshot('comment_form' );
$I->wantTo('Check if new question is showing' );
$I->amOnPage( '/questions/' );
$I->see( $I->questions['question1'] );
//$I->makeScreenshot('questions_page' );

// Add a dummy comment in question 1.
$I->wantTo('Submit new comment' );
$I->loginAs('user2', 'user2' );
$I->amOnPage( '/questions/question/this-is-question-1/' );
$I->click( '.ap-q-cells .comment-btn' );
$I->waitForJS( 'return jQuery.active == 0;',60 );
$I->fillField('#ap-comment-textarea', $I->comment['comment1'] );
$I->click( '#ap-commentform .ap-comment-submit' );
$I->makeScreenshot('questions_comment' );

$I->waitForText( $I->comment['comment1'], 60 );
//$I->makeScreenshot('questions_comment' );

