<?php

namespace humhub\modules\flexTheme\models;

use humhub\modules\flexTheme\helpers\ColorHelper;
use humhub\modules\flexTheme\helpers\FileHelper;
use humhub\modules\ui\view\helpers\ThemeHelper;
use humhub\modules\ui\icon\widgets\Icon;
use Yii;
use yii\base\ErrorException;

class ColorSettings extends \yii\base\Model
{
    const BASE_THEME = 'HumHub';
    const VARS_FILE = '@flex-theme/themes/FlexTheme/css/variables.css';

    // Main Colors (configurable)
    const MAIN_COLORS = ['default', 'primary', 'info', 'link', 'success', 'warning', 'danger'];
    public $default;
    public $primary;
    public $info;
    public $link;
    public $success;
    public $warning;
    public $danger;

    // Text Colors (configurable)
    const TEXT_COLORS = ['text_color_main', 'text_color_secondary', 'text_color_highlight', 'text_color_soft', 'text_color_soft2', 'text_color_soft3', 'text_color_contrast'];
    public $text_color_main;
    public $text_color_secondary;
    public $text_color_highlight;
    public $text_color_soft;
    public $text_color_soft2;
    public $text_color_soft3;
    public $text_color_contrast;

    // Background Colors (configurable)
    const BACKGROUND_COLORS = ['background_color_main', 'background_color_secondary', 'background_color_page', 'background_color_highlight', 'background_color_highlight_soft', 'background3', 'background4'];
    public $background_color_main;
    public $background_color_secondary;
    public $background_color_page;
    public $background_color_highlight;
    public $background_color_highlight_soft;
    public $background3;
    public $background4;

    // Special colors which were generated by Less functions lighten(), darken() or fade()
    const SPECIAL_COLORS = ['background_color_page__darken__5','background_color_page__darken__8','background_color_page__lighten__3','background_color_secondary__darken__5','danger__darken__10','danger__darken__5','danger__lighten__20','danger__lighten__5','default__darken__2','default__darken__5','default__lighten__2','info__darken__10','info__darken__5','info__lighten__25','info__lighten__30','info__lighten__40','info__lighten__45','info__lighten__5','info__lighten__50','info__lighten__8','link__darken__2','link__fade__60','link__lighten__5','primary__darken__10','primary__darken__5','primary__lighten__10','primary__lighten__20','primary__lighten__25','primary__lighten__5','primary__lighten__8','success__darken__10','success__darken__5','success__lighten__20','success__lighten__5','text_color_secondary__lighten__25','warning__darken__10','warning__darken__2','warning__darken__5','warning__lighten__10','warning__lighten__20','warning__lighten__40','warning__lighten__5',];

    public $background_color_page__darken__5;
    public $background_color_page__darken__8;
    public $background_color_page__lighten__3;
    public $background_color_secondary__darken__5;
    public $danger__darken__10;
    public $danger__darken__5;
    public $danger__lighten__20;
    public $danger__lighten__5;
    public $default__darken__2;
    public $default__darken__5;
    public $default__lighten__2;
    public $info__darken__10;
    public $info__darken__5;
    public $info__lighten__25;
    public $info__lighten__30;
    public $info__lighten__40;
    public $info__lighten__45;
    public $info__lighten__5;
    public $info__lighten__50;
    public $info__lighten__8;
    public $link__darken__2;
    public $link__fade__60;
    public $link__lighten__5;
    public $primary__darken__10;
    public $primary__darken__5;
    public $primary__lighten__10;
    public $primary__lighten__20;
    public $primary__lighten__25;
    public $primary__lighten__5;
    public $primary__lighten__8;
    public $success__darken__10;
    public $success__darken__5;
    public $success__lighten__20;
    public $success__lighten__5;
    public $text_color_secondary__lighten__25;
    public $warning__darken__10;
    public $warning__darken__2;
    public $warning__darken__5;
    public $warning__lighten__10;
    public $warning__lighten__20;
    public $warning__lighten__40;
    public $warning__lighten__5;

    public function getColors(): array
    {
        $settings = self::getSettings();
        $base_theme = ThemeHelper::getThemeByName(self::BASE_THEME);
        $all_colors = array_merge(self::MAIN_COLORS, self::TEXT_COLORS, self::BACKGROUND_COLORS, self::SPECIAL_COLORS);

        foreach ($all_colors as $color) {
            $value = $settings->get($color);

            if (empty($value)) {
                $theme_var = str_replace('_', '-', $color);
                $value = $base_theme->variable($theme_var);
            }
            $result[$color] = $value;
        }

        return $result;
    }

    public function init()
    {
        parent::init();

        $settings = self::getSettings();
        $configurable_colors = array_merge(self::MAIN_COLORS, self::TEXT_COLORS, self::BACKGROUND_COLORS);

        foreach($configurable_colors as $color) {
            $this->$color = $settings->get($color);
        }
    }

    public function attributeHints(): array
    {
        $hints = [];

        $base_theme = ThemeHelper::getThemeByName(self::BASE_THEME);
        $configurable_colors = array_merge(self::MAIN_COLORS, self::TEXT_COLORS, self::BACKGROUND_COLORS);

        foreach ($configurable_colors as $color) {
            $theme_var = str_replace('_', '-', $color);
            $default_value = $base_theme->variable($theme_var);
            $icon = Icon::get('circle', ['color' => $default_value ]);
            $hints[$color] = Yii::t('FlexThemeModule.admin', 'Default') . ': ' . '<code>' . $default_value . '</code> ' . $icon;
        }

        return $hints;
    }

    public function rules(): array
    {
        return [
            [[
                'default', 'primary', 'info', 'link', 'success', 'warning', 'danger',
                'text_color_main', 'text_color_secondary', 'text_color_highlight', 'text_color_soft', 'text_color_soft2', 'text_color_soft3', 'text_color_contrast',
                'background_color_main', 'background_color_secondary', 'background_color_page', 'background_color_highlight', 'background_color_highlight_soft', 'background3', 'background4'
                ], 'validateHexColor']
        ];
    }

    public function validateHexColor(string $attribute, $params, $validator)
    {
        if (!preg_match("/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", $this->$attribute)) {
            $this->addError($attribute, Yii::t('FlexThemeModule.admin', 'Invalid Format') . '. ' . Yii::t('FlexThemeModule.admin', 'Must be a color in hexadecimal format, like "#00aaff" or "#FA0"'));
        }
    }

    public function save(): bool
    {
        if(!$this->validate()) {
            return false;
        }

        // Save color values
        self::saveColors();

        // Calculate and save lightened, darkened and faded colors
        self::saveSpecialColors();

        // Save colors to variables file
        self::saveVarsToFile();

        // Update theme.css
        FileHelper::updateThemeFile();

        return true;
    }

    public function saveColors(): void
    {
        $settings = self::getSettings();
        $configurable_colors = array_merge(self::MAIN_COLORS, self::TEXT_COLORS, self::BACKGROUND_COLORS);

        foreach ($configurable_colors as $color) {

            $value = $this->$color;

            // Save as module settings (value can be emtpy)
            $settings->set($color, $value);

            // Save color values as theme variables (take community theme's color if value is empty)
            $theme_var = str_replace('_', '-', $color);
            if (empty($value)) {
                $value = ThemeHelper::getThemeByName(self::BASE_THEME)->variable($theme_var);
            }
            $theme_key = 'theme.var.FlexTheme.' . $theme_var;
            Yii::$app->settings->set($theme_key, $value);
        }
    }

    public function saveSpecialColors(): void
    {
        $settings = self::getSettings();

        $special_colors = self::SPECIAL_COLORS;

        foreach ($special_colors as $color) {

            // split color names into base color, manipulation function and amount of manipulation
            list($base_var, $function, $amount) = explode("__", $color);

            // Get value of base color
            $original_color = $this->$base_var;
            if (empty($original_color)) {
                $theme_var = str_replace('_', '-', $base_var);
                $original_color = ThemeHelper::getThemeByName(self::BASE_THEME)->variable($theme_var);
            }

            // Calculate color value with ColorHelper functions
            if ($function == 'darken') {

                $value = ColorHelper::darken($original_color, $amount);

            } elseif ($function == 'lighten') {

                $value = ColorHelper::lighten($original_color, $amount);

            } elseif ($function == 'fade') {

                $value = ColorHelper::fade($original_color, $amount);

            } elseif ($function == 'fadeout') {

                $value = ColorHelper::fadeout($original_color, $amount);

            } else {
                $value = '';
            }

            // Save calculated value
            $settings->set($color, $value);
        }
    }

    public function saveVarsToFile(): void
    {
        $colors = self::getColors();

        $vars = '';

        foreach($colors as $key => $value) {
              $vars = $vars .  '--' . $key . ':' . $value . ';';
        }

        $content = ':root {' . $vars . '}';
        
        FileHelper::updateVarsFile($content);
    }

    private function getSettings()
    {
        return Yii::$app->getModule('flex-theme')->settings;
    }
}
