<?php
/**
 * Add base page for PlatformPress
 */

$I = new UITester($scenario );
$I->wantTo('Add PlatformPress base page' );
$I->loginAsAdmin();
$I->loginAsAdmin();
$I->amOnPage('/wp-admin/post-new.php?post_type=page' );
$I->seeInSource('Questions', 'CSS:#the-list .row-title' );
//if (  ) {
	/*$I->fillField([ 'name' => 'post_title' ] , 'Questions' );
	$I->fillTinyMceEditorById('content', '[platformpress]' );
	$I->click('input#publish' );
	$I->amOnPage('/wp-admin/edit.php?post_type=page' );
	$I->seeInSource('Questions', 'CSS:#the-list .row-title' );
	$I->amOnPage('/questions/' );
	$I->seeInSource('Questions', 'CSS:h1' );*/
	
/*} else {
	$I->comment('PlatformPress base page already exists.' );
}*/

$I->wantTo('Check PlatformPress base page is selected');
$I->amOnPage('/questions/' );


if( !$I->seeElement('#platformpress') ){
	$I->amOnPage('/wp-admin/admin.php?page=platformpress_options' );
	$I->submitForm('#options_form',  array('platformpress_opt[base_page]' => '3' ), 'Save options');
}
