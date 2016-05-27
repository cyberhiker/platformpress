<?php
/**
 * Check if PlatformPress is activate
 * @var UITester
 */

$I = new UITester($scenario);
$I->loginAsAdmin();
$I->amOnPluginPage();
$I->wantTo('Check if PlatformPress is active');
$I->seeElement('[data-slug="platformpress"] .deactivate');
//$I->makeScreenshot('admin' );
