<?php

namespace Models;

use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;

abstract class AbstractIblockEntity extends DataManager
{
    /**
     * Код инфоблока
     * @return string
     */
    abstract public static function getIblockCode(): string;
    
    /**
     * ID инфоблока (кэшируется)
     */
    protected static $iblockId = null;
    
    /**
     * Компилированная сущность
     */
    protected static $compiledEntity = null;
    
    /**
     * Получить ID инфоблока
     */
    public static function getIblockId(): int
    {
        if (self::$iblockId === null) {
            self::initIblockId();
        }
        return self::$iblockId;
    }
    
    /**
     * Инициализировать ID инфоблока
     */
    protected static function initIblockId(): void
    {
        if (!Loader::includeModule('iblock')) {
            throw new SystemException('Модуль iblock не подключен');
        }
        
        $iblock = IblockTable::getList([
            'filter' => ['CODE' => static::getIblockCode()],
            'select' => ['ID'],
            'cache' => ['ttl' => 3600]
        ])->fetch();
        
        if (!$iblock) {
            throw new SystemException('Инфоблок с кодом "' . static::getIblockCode() . '" не найден');
        }
        
        self::$iblockId = (int)$iblock['ID'];
    }
    
    /**
     * Получить компилированную сущность
     */
    public static function getCompiledEntity()
    {
        if (self::$compiledEntity === null) {
            self::$compiledEntity = IblockTable::compileEntity(self::getIblockId());
        }
        return self::$compiledEntity;
    }
    
    /**
     * Proxy методы для работы с компилированной сущностью
     */
    public static function getMap()
    {
        return self::getCompiledEntity()->getFields();
    }
    
    public static function getTableName()
    {
        return self::getCompiledEntity()->getDBTableName();
    }
    
    public static function getEntity()
    {
        return self::getCompiledEntity();
    }
    
    /**
     * Получить элементы инфоблока
     */
    public static function getElements(array $parameters = [])
    {
        $entity = self::getCompiledEntity();
        $entityClass = $entity->getDataClass();
        
        return $entityClass::getList($parameters);
    }
    
    /**
     * Добавить элемент
     */
    public static function addElement(array $data)
    {
        $entity = self::getCompiledEntity();
        $entityClass = $entity->getDataClass();
        
        return $entityClass::add($data);
    }
    
    /**
     * Обновить элемент
     */
    public static function updateElement($primary, array $data)
    {
        $entity = self::getCompiledEntity();
        $entityClass = $entity->getDataClass();
        
        return $entityClass::update($primary, $data);
    }
    
    /**
     * Удалить элемент
     */
    public static function deleteElement($primary)
    {
        $entity = self::getCompiledEntity();
        $entityClass = $entity->getDataClass();
        
        return $entityClass::delete($primary);
    }
}