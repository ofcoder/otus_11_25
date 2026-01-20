<?php
namespace Models\Lists;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Iblock\Iblock;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Main\Entity;

class ElementProceduresTable extends DataManager
{
    const IBLOCK_CODE = 'Procedures';
    
    protected static $iblockId = null;
    protected static $dataClass = null;
    
    /**
     * Получить ID инфоблока через ORM D7
     */
    public static function getIblockId(): int
    {
        if (self::$iblockId === null) {
            self::initIblockId();
        }
        return self::$iblockId;
    }
    
    /**
     * Инициализировать ID инфоблока через ORM D7
     */
    private static function initIblockId(): void
    {
        if (!Loader::includeModule('iblock')) {
            throw new SystemException('Модуль iblock не подключен');
        }
        
        $iblock = IblockTable::getRow([
            'filter' => ['CODE' => self::IBLOCK_CODE],
            'select' => ['ID']
        ]);
        
        if ($iblock) {
            self::$iblockId = (int)$iblock['ID'];
        } else {
            throw new SystemException('Инфоблок procedures не найден');
        }
    }
    
    /**
     * Получить DataClass через ORM D7
     */
    public static function getDataClass()
    {
        if (self::$dataClass === null) {
            self::initIblockId();
            
            // Используем ORM D7
            $iblock = Iblock::wakeUp(self::$iblockId);
            if (!$iblock) {
                throw new SystemException('Не удалось получить инфоблок procedures');
            }
            
            self::$dataClass = $iblock->getEntityDataClass();
            if (!self::$dataClass) {
                throw new SystemException('Не удалось получить DataClass для инфоблока procedures');
            }
        }
        
        return self::$dataClass;
    }
    
    /**
     * Получить все процедуры через ORM D7
     */
    public static function getAllProcedures(): array
    {
        $dataClass = self::getDataClass();
        $procedures = [];
        
        $dbResult = $dataClass::getList([
            'select' => ['ID', 'NAME', 'CODE'],
            'filter' => ['=ACTIVE' => 'Y'],
            'order' => ['NAME' => 'ASC']
        ]);
        
        while ($procedure = $dbResult->fetch()) {
            $procedures[] = [
                'ID' => $procedure['ID'],
                'NAME' => $procedure['NAME'],
                'CODE' => $procedure['CODE'] ?? ''
            ];
        }
        
        return $procedures;
    }
    
    /**
     * Получить названия процедур по их ID через ORM D7
     */
    public static function getProcedureNamesByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        
        $dataClass = self::getDataClass();
        $procedureNames = [];
        
        $dbResult = $dataClass::getList([
            'select' => ['ID', 'NAME'],
            'filter' => ['=ID' => $ids]
        ]);
        
        while ($procedure = $dbResult->fetch()) {
            $procedureNames[] = $procedure['NAME'];
        }
        
        return $procedureNames;
    }
    
    /**
     * Получить процедуру по ID через ORM D7
     */
    public static function getProcedureById(int $id): ?array
    {
        $dataClass = self::getDataClass();
        
        $dbResult = $dataClass::getList([
            'select' => ['ID', 'NAME', 'CODE'],
            'filter' => [
                '=ID' => $id,
                '=ACTIVE' => 'Y'
            ]
        ]);
        
        if ($procedure = $dbResult->fetch()) {
            return [
                'ID' => $procedure['ID'],
                'NAME' => $procedure['NAME'],
                'CODE' => $procedure['CODE'] ?? ''
            ];
        }
        
        return null;
    }
    
    /**
     * Добавить процедуру через ORM D7 с генерацией уникального кода
     */
    public static function addProcedure(string $name): ?int
    {
        $dataClass = self::getDataClass();
        
        // Генерируем уникальный код
        $code = self::generateUniqueCode($name);
        
        $fields = [
            'NAME' => $name,
            'CODE' => $code,
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => self::getIblockId()
        ];
        
        $result = $dataClass::add($fields);
        
        if ($result->isSuccess()) {
            return $result->getId();
        }
        
        return null;
    }
    
    /**
     * Обновить процедуру через ORM D7
     */
    public static function updateProcedure(int $id, string $name): bool
    {
        $dataClass = self::getDataClass();
        
        $fields = [
            'NAME' => $name
        ];
        
        $result = $dataClass::update($id, $fields);
        return $result->isSuccess();
    }
    
    /**
     * Удалить процедуру через ORM D7
     */
    public static function deleteProcedure(int $id): bool
    {
        $dataClass = self::getDataClass();
        $result = $dataClass::delete($id);
        return $result->isSuccess();
    }
    
    /**
     * Генерировать уникальный код для процедуры
     */
    private static function generateUniqueCode(string $name): string
    {
        $baseCode = \CUtil::translit($name, 'ru', [
            'replace_space' => '-',
            'replace_other' => '-',
            'change_case' => 'L'
        ]);
        
        $code = $baseCode;
        $counter = 1;
        
        // Проверяем уникальность через ORM D7
        while (true) {
            $dataClass = self::getDataClass();
            $dbResult = $dataClass::getList([
                'filter' => ['=CODE' => $code],
                'select' => ['ID'],
                'limit' => 1
            ]);
            
            if (!$dbResult->fetch()) {
                break;
            }
            
            $code = $baseCode . '-' . $counter;
            $counter++;
        }
        
        return $code;
    }
}