<?php
namespace atuin\engine;


/**
 * TODO LIST
 *
 * 1 . añadir un archivo composer a atuin.
 * 2 . hacer que el archivo composer cree un directorio llamado "atuin" en la raíz
 *
 *
 */

use Yii;

/**
 * Engine Module
 *
 * @author joseba <joseba.juaniz@gmail.com>
 */
class Module extends \atuin\skeleton\Module
{
    protected static $_id = 'engine';


    protected static $_version = '0.0';


    public $is_core_module = 1;

    public static function loadDatabaseConfig($config)
    {
        if (file_exists(dirname($config['basePath']) . '/atuin/config/config-db.php'))
        {
            $config = Yii\helpers\ArrayHelper::merge(
                $config,
                require(dirname($config['basePath']) . '/atuin/config/config-db.php')
            );
        }

        return $config;
    }


    /**
     * Will return all the config array for Yii2 Application class with the modules defined
     * in Atuin.
     *
     * This static method should be called in the index.php of each yii2 application (backend and frontend)
     *
     *
     * @param array $config
     * @return array
     * @throws \Exception
     */
    public static function loadAtuinConfig($config)
    {
        // we will make sure that the first module loaded in Yii2 it's the
        // Atuin Engine module, that will handle all the CMS operations
        // 
        if (!array_key_exists('bootstrap', $config))
        {
            $config['bootstrap'] = [self::getId()];
        } else
        {
            array_unshift($config['bootstrap'], self::getId());
        }

        if (!array_key_exists('basePath', $config))
        {
            throw new \Exception('Config module must have defined a basePath.');
        }

        if (file_exists(dirname($config['basePath']) . '/atuin/config/' . $config['id'] . '.php'))
        {
            $config = Yii\helpers\ArrayHelper::merge(
                $config,
                require(dirname($config['basePath']) . '/atuin/config/' . $config['id'] . '.php')
            );
        }

        // No we will define the basic core modules that will be defined by the Atuin system.

        $config = Yii\helpers\ArrayHelper::merge(
            $config,
            [
                'modules' => [
                    'engine' =>
                        [
                            'class' => 'atuin\engine\Module'
                        ],
                    'installation' =>
                        [
                            'class' => 'atuin\installation\Module'
                        ],
                    'apps' =>
                        [
                            'class' => 'atuin\apps\Module'
                        ],
                    'config' =>
                        [
                            'class' => 'atuin\config\Module'
                        ],
                    'menus' =>
                        [
                            'class' => 'atuin\menus\Module'
                        ]
                ],
                'components' => [
                    'i18n' => [
                        'translations' => [
                            'atuin-installation' => [
                                'class' => 'yii\i18n\PhpMessageSource',
                                'basePath' => '@vendor/atuin/engine/messages',
                            ],
                            'atuin' => [
                                'class' => 'yii\i18n\PhpMessageSource',
                                'basePath' => '@vendor/atuin/engine/messages',
                            ]
                        ],
                    ],
                ],
                'language' => 'es',
            ]
        );

        $config = self::loadDatabaseConfig($config);

        return $config;
    }

    /**
     * Loads all the specific apps from database and launches the ones that are marked as bootstrap
     *
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // We will check if the system it's already installed, if so, then the system will 
        // continue loading normally, if it isn't then the installation module will get the
        // lead in the CMS

        if (Yii::$app->getModule('installation')->checkInstallation() === FALSE)
        {
            Yii::$app->getModule('installation')->launchInstallation();
        }

    }


}