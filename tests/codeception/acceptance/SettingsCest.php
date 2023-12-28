<?php

use flexTheme\AcceptanceTester;

class SettingsCest
{
    const BG_COLOR_PG = '#cfe2f3';
    const BG_COLOR_PG_D_5 = '#c3d5e5';
    
    public function testSettings(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->amOnRoute(['/flex-theme/config']);
        $I->waitForText('Flex Theme');
        $I->selectOption('#config-commentlink', 'icon');
        $I->selectOption('#config-likelink', 'icon');

        $I->click('Save');

        $I->seeSuccess();
    }
    
    public function testSettingsColor(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->amOnRoute(['/flex-theme/config/colors']);
        $I->waitForText('Flex Theme');
        $I->executeJS("$( '.form-collapsible-fields' ).trigger( 'click' )");
        $I->fillField('#colorsettings-background_color_page', self::BG_COLOR_PG);

        $I->click('Save');

        $I->seeSuccess();
    }
    
    public function testEffectOfPreviousSettings(AcceptanceTester $I)
    {
        $I->amOnRoute(['/dashboard']);
        $I->waitForElementVisible('#layout-content');

        $color = $I->executeJS("getComputedStyle(document.documentElement).getPropertyValue('--background_color_page');");
        $colorDarkened = $I->executeJS("getComputedStyle(document.documentElement).getPropertyValue('--background_color_page__darken__5');");
        
        if ($color !== self::BG_COLOR_PG) {
            $I->fail('Fail. Expected background color: ' . self::BG_COLOR_PG . ' - found color: ' . $color);
        }
        
        if ($colorDarkened !== self::BG_COLOR_PG_D_5) {
            $I->fail('Fail. Expected background color: ' . self::BG_COLOR_PG_D_5 . ' - found color: ' . $colorDarkened);
        }
    }
}
