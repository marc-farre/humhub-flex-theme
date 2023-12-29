<?php

namespace humhub\modules\flexTheme\helpers;

use Yii;
use yii\base\ErrorException;

class FileHelper {

    const THEME_PATH = '@flex-theme/themes/FlexTheme';

    private static function getVarsFile()
    {
        return Yii::getAlias(self::THEME_PATH . '/css/variables.css');
    }

    private static function getThemeFile()
    {
        return Yii::getAlias(self::THEME_PATH . '/css/theme.css');
    }

    private static function getThemeBaseFile()
    {
        return Yii::getAlias(self::THEME_PATH . '/css/theme_base.css');
    }
    
    public static function updateVarsFile(string $content): void
    {
        try {
            file_put_contents(self::getVarsFile(), $content);
        } catch(ErrorException $e) {
            Yii::error($e, 'flex-theme');
        }
    }

    public static function updateThemeFile(): void
    {
        // Base Theme
        $theme_base = file_get_contents(self::getThemeBaseFile());

        // CSS Variables
        $vars = file_get_contents(self::getVarsFile());

        // Create/Update theme.css
        $content = $theme_base . $vars;
        
        try {
            file_put_contents(self::getThemeFile(), $content);
        } catch(ErrorException $e) {
            Yii::error($e, 'flex-theme');
        }

        // Clear Asset Manager to reload theme.css
        try {
            Yii::$app->assetManager->clear();
        } catch(ErrorException $e) {
            Yii::warning($e, 'flex-theme');
        }
    }
}
