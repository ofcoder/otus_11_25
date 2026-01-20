<?php

/**
 * - /local/php_interface/classes/{Path|raw}/{*|raw}.php
 * - /local/php_interface/classes/{Path|ucfirst,lowercase}/{*|ucfirst,lowercase}.php
 */
spl_autoload_register(function($sClassName)
{
    $sClassFile = __DIR__.'/classes';

    if ( file_exists($sClassFile.'/'.str_replace('\\', '/', $sClassName).'.php') )
    {
        require_once($sClassFile.'/'.str_replace('\\', '/', $sClassName).'.php');
        return;
    }

    $arClass = explode('\\', strtolower($sClassName));
    foreach($arClass as $sPath )
    {
        $sClassFile .= '/'.ucfirst($sPath);
    }
    $sClassFile .= '.php';
    if (file_exists($sClassFile))
    {
        require_once($sClassFile);
    }
});

/**
 * Project bootstrap files
 */
foreach( [
     /**
      *Composer
     */
     __DIR__ . '/../../vendor/autoload.php',

      /**
      *App classes
     */
     __DIR__ . '/../App/autoload.php',

    ]
    as $filePath )
{
    if ( file_exists($filePath) )
    {
        require_once($filePath);
    }
}
unset($filePath);
