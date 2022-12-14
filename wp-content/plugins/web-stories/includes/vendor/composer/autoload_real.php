<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitc5b5157ec3b3e1f47540883e23085cce
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Google_Web_Stories_Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Google_Web_Stories_Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitc5b5157ec3b3e1f47540883e23085cce', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Google_Web_Stories_Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitc5b5157ec3b3e1f47540883e23085cce', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Google_Web_Stories_Composer\Autoload\ComposerStaticInitc5b5157ec3b3e1f47540883e23085cce::getInitializer($loader));

        $loader->setClassMapAuthoritative(true);
        $loader->register(true);

        return $loader;
    }
}
