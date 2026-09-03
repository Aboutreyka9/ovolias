<?php

class Autoloader
{
    private static array $classMap = [];

    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    public static function autoload(string $class): void
    {
        if (isset(self::$classMap[$class]) && file_exists(self::$classMap[$class])) {
            require_once self::$classMap[$class];
            return;
        }

        $baseDir = __DIR__ . '/../';
        $fileName = $class . '.php';

        // 1. Dossier Core
        $coreFile = $baseDir . 'core/' . $fileName;
        if (file_exists($coreFile)) {
            self::$classMap[$class] = $coreFile;
            require_once $coreFile;
            return;
        }

        // 2. Dossier Models
        if (strpos($class, 'Model') === 0 || $class === 'Validator') {
            $direct = $baseDir . 'models/' . $fileName;
            if (file_exists($direct)) {
                self::$classMap[$class] = $direct;
                require_once $direct;
                return;
            }
            $matches = glob($baseDir . 'models/*/' . $fileName);
            if (!empty($matches)) {
                self::$classMap[$class] = $matches[0];
                require_once $matches[0];
                return;
            }
        }

        // 3. Dossier Controllers
        if (strpos($class, 'Controller') !== false) {
            $direct = $baseDir . 'controllers/' . $fileName;
            if (file_exists($direct)) {
                self::$classMap[$class] = $direct;
                require_once $direct;
                return;
            }
            $matches = glob($baseDir . 'controllers/*/' . $fileName);
            if (!empty($matches)) {
                self::$classMap[$class] = $matches[0];
                require_once $matches[0];
                return;
            }
        }
    }
}

// Enregistrement de l'autoloader
Autoloader::register();
