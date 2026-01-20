<?php
namespace Diag;

use Bitrix\Main\Diag\Debug,
Bitrix\Main\Config\Configuration,
Bitrix\Main\IO,
Bitrix\Main\Application;

class Logger
{
    private const string DEBUG_FILE_NAME = '/local/logs/debug.log';

    public static function dumpToFile($data) 
    {
        $path = Application::getDocumentRoot() . "/local/logs/debug.txt";
        IO\File::putFileContents($path, $data, IO\File::APPEND);
    }

    /**
     * writeToLog
     *
     * @param  mixed $data
     * @param  mixed $title
     * @return bool
     */
    public static function writeToLog($data, $title = ''): bool
    {
        if (!self::DEBUG_FILE_NAME)
            return false;
        $log = "\n------------------------\n";
        $log .= date("Y.m.d G:i:s") . "\n";
        $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
        $log .= print_r($data, 1);
        $log .= "\n------------------------\n";
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . self::DEBUG_FILE_NAME, $log, FILE_APPEND);
        return true;
    }

    /**
     * myDump
     *
     * @param  mixed $var
     * @return void
     */
    public static function myDump($var)
    {
        global $USER;
        if (($USER->isAdmin() == 1) || ($_REQUEST["dump"] === "Y")) {
            ?>
            <font style="text-align: left; font-size: 10px">
                <pre><? var_dump($var) ?></pre>
            </font><br>
            <?php
        }
    }

    /**
     * bitrixDumpToFile
     *
     * @param  mixed $var
     * @param  mixed $varName
     * @return void
     */
    public static function bitrixDumpToFile($var, $varName = '')
    {
        $variable = $var;
        //$fileName = self::DEBUG_FILE_NAME;
        $fileName = Configuration::getValue('exception_handling')['log']['settings']['file'] ?? self::DEBUG_FILE_NAME;
        try {
            Debug::dumpToFile($variable, $varName, $fileName);
        } catch (\Exception $e) {
            Debug::dumpToFile($e, 'Ошибка bitrixDumpToFile', $fileName);
        }
    }

    /**
     * log2file
     *
     * @param  mixed $var
     * @param  mixed $fn
     * @param  mixed $folder
     * @return void
     */
    public static function log2file($var, $fn = null, $folder = null)
    {
        if (!$folder)
            $folder = $_SERVER["DOCUMENT_ROOT"] . '/local/logs/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $error = "";
        $fn = $fn ? "-" . str_replace(['\\', '/', ' '], '', $fn) : "";
        $filePath = $folder . date("Y-m-d") . "-log2file{$fn}.log";
        $log = date("Y-m-d H:i:s") . "; ";

        if (is_scalar($var)) {
            $log .= $var . "\r\n";
        } else {
            $log .= print_r($var, true) . "\r\n";
        }
        try {
            file_put_contents($filePath, $log, FILE_APPEND);
        } catch (\Throwable $e) {
            Debug::dumpToFile($e, 'Ошибка записи в файл', $filePath);
        }
    }

    /**
     * pr
     *
     * @param  mixed $var
     * @param  mixed $type
     * @return void
     */

    public static function pr($var, $type = false)
    {
        echo '<pre style="font-size:10px; border:1px solid #000; background:#FFF; text-align:left; color:#000;">';
        if ($type)
            var_dump($var);
        else
            print_r($var);
        echo '</pre>';
    }
}