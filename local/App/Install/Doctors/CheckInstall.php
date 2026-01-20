<?php
// local/App/Install/CheckInstall.php
namespace Install\Doctors;

use Install\Doctors\Installer;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;

class CheckInstall
{
    /**
     * Проверить и установить инфоблоки
     * @param bool $forceInstall Принудительная установка даже если инфоблоки существуют
     * @return bool
     */
    public static function checkAndInstall(bool $forceInstall = false): bool
    {
        // Проверяем модуль iblock
        if (!Loader::includeModule('iblock')) {
            return false;
        }
        
        // Проверяем существование инфоблоков БЕЗ кэширования
        $iblockCodes = [
            Installer::DOCTORS_IBLOCK_CODE,
            Installer::PROCEDURES_IBLOCK_CODE
        ];
        
        $allExist = true;
        foreach ($iblockCodes as $code) {
            $iblock = IblockTable::getList([
                'filter' => ['CODE' => $code],
                'select' => ['ID', 'API_CODE', 'XML_ID'],
                'cache' => ['ttl' => 0]
            ])->fetch();
            
            if (!$iblock) {
                $allExist = false;
                break;
            }
            
            // Дополнительная проверка: если инфоблок существует, но у него нет API_CODE или XML_ID
            // считаем что нужна переустановка
            if (empty($iblock['API_CODE']) || empty($iblock['XML_ID'])) {
                $allExist = false;
                break;
            }
        }
        
        // Если инфоблоки не найдены или принудительная установка
        if (!$allExist || $forceInstall) {
            // Запускаем установку
            $result = Installer::install();
            return $result['success'];
        }
        
        return true;
    }
    
    /**
     * Проверить полноту настроек инфоблоков
     */
    public static function checkIblocksSettings(): array
    {
        $results = [];
        
        if (!Loader::includeModule('iblock')) {
            return ['success' => false, 'message' => 'Модуль iblock не подключен'];
        }
        
        $iblockCodes = [
            Installer::DOCTORS_IBLOCK_CODE,
            Installer::PROCEDURES_IBLOCK_CODE
        ];
        
        foreach ($iblockCodes as $code) {
            $iblock = IblockTable::getList([
                'filter' => ['CODE' => $code],
                'select' => ['ID', 'NAME', 'API_CODE', 'XML_ID', 'LIST_MODE', 'REST_ON'],
                'cache' => ['ttl' => 0]
            ])->fetch();
            
            $results[$code] = [
                'exists' => (bool)$iblock,
                'id' => $iblock['ID'] ?? null,
                'name' => $iblock['NAME'] ?? null,
                'has_api_code' => !empty($iblock['API_CODE']),
                'has_xml_id' => !empty($iblock['XML_ID']),
                'list_mode' => $iblock['LIST_MODE'] ?? null,
                'rest_enabled' => ($iblock['REST_ON'] ?? '') === 'Y',
                'fully_configured' => false
            ];
            
            // Проверяем полноту настроек
            if ($iblock && 
                !empty($iblock['API_CODE']) && 
                !empty($iblock['XML_ID']) && 
                ($iblock['LIST_MODE'] ?? '') === 'C' &&
                ($iblock['REST_ON'] ?? '') === 'Y') {
                $results[$code]['fully_configured'] = true;
            }
        }
        
        return $results;
    }
    
    /**
     * Принудительная переустановка
     */
    public static function forceReinstall(): bool
    {
        return self::checkAndInstall(true);
    }
    
    /**
     * Проверить существование инфоблоков
     */
    public static function checkIblocksExist(): bool
    {
        if (!Loader::includeModule('iblock')) {
            return false;
        }
        
        $iblockCodes = [
            Installer::DOCTORS_IBLOCK_CODE,
            Installer::PROCEDURES_IBLOCK_CODE
        ];
        
        foreach ($iblockCodes as $code) {
            $iblock = IblockTable::getList([
                'filter' => ['CODE' => $code],
                'select' => ['ID'],
                'cache' => ['ttl' => 0]
            ])->fetch();
            
            if (!$iblock) {
                return false;
            }
        }
        
        return true;
    }
}