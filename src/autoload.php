<?php
    spl_autoload_register(function (string $class) {
        $fullyQualifiedClassName = explode('\\', $class);

        if ($fullyQualifiedClassName[0] === 'MauMau') {
            // Autoload all classes under the MauMau namespace.

            $className = array_pop($fullyQualifiedClassName);
            $fileName = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $fullyQualifiedClassName) . DIRECTORY_SEPARATOR . $className . '.php';

            if (file_exists($fileName)) {
                require_once $fileName;
            }
        }
    });
