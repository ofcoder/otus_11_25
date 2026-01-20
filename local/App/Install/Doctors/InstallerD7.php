<?php
// local/App/Install/Doctors/Installer.php
namespace Install\Doctors;

use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\Iblock;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class InstallerD7
{
    const DOCTORS_IBLOCK_CODE = 'Doctors';
    const PROCEDURES_IBLOCK_CODE = 'Procedures';
    
    public static function install(): array
    {
        $result = [
            'success' => true,
            'errors' => [],
            'messages' => []
        ];
        
        if (!Loader::includeModule('iblock')) {
            $result['success'] = false;
            $result['errors'][] = 'Модуль iblock не установлен';
            return $result;
        }
        
        try {
            // Перед установкой очищаем старые инфоблоки если они есть
            self::removeExistingIblocks();
            $result['messages'][] = 'Старые инфоблоки удалены';
            
            // 1. Создаем инфоблок Процедуры
            $proceduresIblockId = self::createListIblock(
                self::PROCEDURES_IBLOCK_CODE,
                'Процедуры'
            );
            
            if (!$proceduresIblockId) {
                $result['success'] = false;
                $result['errors'][] = 'Не удалось создать инфоблок "Процедуры"';
                return $result;
            }
            
            $result['messages'][] = '✅ Инфоблок "Процедуры" создан (ID: ' . $proceduresIblockId . ')';
            
            // 2. Добавляем тестовые процедуры
            $proceduresAdded = self::addTestProcedures($proceduresIblockId);
            $result['messages'][] = '✅ Добавлено процедур: ' . $proceduresAdded;
            
            // 3. Создаем инфоблок Врачи
            $doctorsIblockId = self::createListIblock(
                self::DOCTORS_IBLOCK_CODE,
                'Врачи'
            );
            
            if (!$doctorsIblockId) {
                $result['success'] = false;
                $result['errors'][] = 'Не удалось создать инфоблок "Врачи"';
                return $result;
            }
            
            $result['messages'][] = '✅ Инфоблок "Врачи" создан (ID: ' . $doctorsIblockId . ')';
            
            // 4. Создаем свойства для врачей
            $propertiesCreated = self::createDoctorProperties($doctorsIblockId, $proceduresIblockId);
            $result['messages'][] = '✅ Создано свойств: ' . $propertiesCreated;
            
            // 5. Добавляем тестовых врачей
            $doctorsAdded = self::addTestDoctors($doctorsIblockId, $proceduresIblockId);
            $result['messages'][] = '✅ Добавлено врачей: ' . $doctorsAdded;
            
            // 6. Очищаем кэш ORM
            self::clearOrmCache();
            $result['messages'][] = '✅ Кэш ORM очищен';
            
        } catch (\Exception $e) {
            $result['success'] = false;
            $result['errors'][] = '❌ Ошибка: ' . $e->getMessage();
            $result['messages'][] = '🔍 Детали: ' . $e->getTraceAsString();
        }
        
        return $result;
    }
    
    /**
     * Создать универсальный список с правильными настройками
     */
    private static function createListIblock(string $code, string $name): ?int
    {
        // Проверяем существование
        $iblock = IblockTable::getList([
            'filter' => ['CODE' => $code],
            'select' => ['ID'],
            'cache' => ['ttl' => 0]
        ])->fetch();
        
        if ($iblock) {
            return (int)$iblock['ID'];
        }
        
        // Создаем универсальный список с правильными настройками
        $iblockFields = [
            'ACTIVE' => 'Y',
            'NAME' => $name,
            'CODE' => $code,
            //'TIMESTAMP_X' => 'lists',
            'IBLOCK_TYPE_ID' => 'lists', // Универсальный список
            'LID' => 's1',
            'SORT' => 100,
            'LIST_MODE' => 'C', // Списки и коллекции
            'REST_ON' => 'Y', // Включен REST API
            'WORKFLOW' => 'N',
            'BIZPROC' => 'N',
            'DESCRIPTION' => $name . ' - универсальный список',
            'DESCRIPTION_TYPE' => 'text',
            'GROUP_ID' => ['2' => 'R'], // Права на чтение для всех
            'INDEX_ELEMENT' => 'Y',
            'INDEX_SECTION' => 'N',
            'VERSION' => 1,
            'FIELDS' => [
                'CODE' => [
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => [
                        'UNIQUE' => 'Y',
                        'TRANSLITERATION' => 'Y',
                        'TRANS_CASE' => 'L',
                    ]
                ]
            ],
            // Критически важные поля для универсальных списков
            'API_CODE' => $code, // Код для REST API
            'XML_ID' => $code, // Внешний код
        ];
        
        $result = IblockTable::add($iblockFields);
        
        if ($result->isSuccess()) {
            // После создания нужно обновить настройки через старый API
            // чтобы правильно отображалось в пользовательском интерфейсе
            self::updateIblockSettings($result->getId(), $code);
            
            return $result->getId();
        } else {
            $errors = implode(', ', $result->getErrorMessages());
            throw new \Exception('Ошибка создания инфоблока ' . $code . ': ' . $errors);
        }
    }
    
    /**
     * Обновить настройки инфоблока через старый API для корректного отображения
     */
    private static function updateIblockSettings(int $iblockId, string $code): void
    {
        $iblock = new \CIBlock();
        
        $updateFields = [
            'API_CODE' => $code,
            'XML_ID' => $code,
            'LIST_MODE' => 'C',
            'REST_ON' => 'Y',
        ];
        
        $iblock->Update($iblockId, $updateFields);
    }
    
    /**
     * Удалить существующие инфоблоки
     */
    private static function removeExistingIblocks(): void
    {
        $iblockCodes = [
            self::DOCTORS_IBLOCK_CODE,
            self::PROCEDURES_IBLOCK_CODE
        ];
        
        foreach ($iblockCodes as $code) {
            $iblock = IblockTable::getList([
                'filter' => ['CODE' => $code],
                'select' => ['ID'],
                'cache' => ['ttl' => 0]
            ])->fetch();
            
            if ($iblock && $iblock['ID']) {
                // Сначала удаляем все элементы
                self::deleteIblockElements($iblock['ID']);
                
                // Затем удаляем инфоблок
                IblockTable::delete($iblock['ID']);
            }
        }
        
        // Даем время на удаление
        sleep(1);
    }
    
    /**
     * Удалить все элементы инфоблока
     */
    private static function deleteIblockElements(int $iblockId): void
    {
        $dbElements = \CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => $iblockId],
            false,
            false,
            ['ID']
        );
        
        while ($element = $dbElements->Fetch()) {
            \CIBlockElement::Delete($element['ID']);
        }
    }
    
    /**
     * Создать свойства для инфоблока Врачи
     */
    private static function createDoctorProperties(int $doctorsIblockId, int $proceduresIblockId): int
    {
        $properties = [
            [
                'NAME' => 'Фамилия',
                'CODE' => 'LAST_NAME',
                'PROPERTY_TYPE' => PropertyTable::TYPE_STRING,
                'IS_REQUIRED' => 'Y',
                'SEARCHABLE' => 'Y',
                'FILTRABLE' => 'Y',
            ],
            [
                'NAME' => 'Имя',
                'CODE' => 'FIRST_NAME',
                'PROPERTY_TYPE' => PropertyTable::TYPE_STRING,
                'IS_REQUIRED' => 'Y',
                'SEARCHABLE' => 'Y',
                'FILTRABLE' => 'Y',
            ],
            [
                'NAME' => 'Отчество',
                'CODE' => 'SECOND_NAME',
                'PROPERTY_TYPE' => PropertyTable::TYPE_STRING,
                'SEARCHABLE' => 'Y',
                'FILTRABLE' => 'Y',
            ],
            [
                'NAME' => 'Процедуры',
                'CODE' => 'PROCEDURES',
                'PROPERTY_TYPE' => PropertyTable::TYPE_ELEMENT,
                'LINK_IBLOCK_ID' => $proceduresIblockId,
                'MULTIPLE' => 'Y',
                'SEARCHABLE' => 'Y',
                'FILTRABLE' => 'Y',
            ]
        ];
        
        $createdCount = 0;
        
        foreach ($properties as $propertyFields) {
            $propertyFields['IBLOCK_ID'] = $doctorsIblockId;
            $propertyFields['ACTIVE'] = 'Y';
            $propertyFields['SORT'] = 100;
            $propertyFields['USER_TYPE'] = ''; // Явно указываем пустое значение
            
            // Для свойств привязки к элементам
            if ($propertyFields['CODE'] === 'PROCEDURES') {
                $propertyFields['WITH_DESCRIPTION'] = 'N';
            }
            
            $result = PropertyTable::add($propertyFields);
            if ($result->isSuccess()) {
                $createdCount++;
            } else {
                // Пробуем создать через старый API
                $prop = new \CIBlockProperty();
                $propId = $prop->Add($propertyFields);
                if ($propId) {
                    $createdCount++;
                }
            }
        }
        
        return $createdCount;
    }
    
    /**
     * Добавить тестовые процедуры
     */
    private static function addTestProcedures(int $iblockId): int
    {
        $procedures = [
            'Консультация терапевта',
            'Консультация кардиолога',
            'ЭКГ',
            'УЗИ брюшной полости',
            'Анализ крови',
            'Рентген легких',
            'МРТ головного мозга',
            'Физиотерапия',
            'Массаж',
            'Диагностика зрения'
        ];
        
        $addedCount = 0;
        
        foreach ($procedures as $procedureName) {
            $el = new \CIBlockElement();
            
            $fields = [
                'IBLOCK_ID' => $iblockId,
                'NAME' => $procedureName,
                'CODE' => \CUtil::translit($procedureName, 'ru'),
                'ACTIVE' => 'Y',
                'XML_ID' => 'PROCEDURE_' . \CUtil::translit($procedureName, 'ru', ['replace_space' => '_']),
            ];
            
            if ($el->Add($fields)) {
                $addedCount++;
            }
        }
        
        return $addedCount;
    }
    
    /**
     * Добавить тестовых врачей
     */
    private static function addTestDoctors(int $doctorsIblockId, int $proceduresIblockId): int
    {
        // Сначала получаем ID всех процедур
        $procedureIds = [];
        $dbProcedures = \CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => $proceduresIblockId],
            false,
            false,
            ['ID', 'NAME', 'XML_ID']
        );
        
        while ($procedure = $dbProcedures->Fetch()) {
            $procedureIds[$procedure['NAME']] = $procedure['ID'];
        }
        
        $doctors = [
            [
                'LAST_NAME' => 'Иванов',
                'FIRST_NAME' => 'Иван',
                'SECOND_NAME' => 'Иванович',
                'XML_ID' => 'DOCTOR_1',
                'procedures' => [
                    $procedureIds['Консультация терапевта'] ?? null,
                    $procedureIds['Консультация кардиолога'] ?? null,
                    $procedureIds['Анализ крови'] ?? null
                ]
            ],
            [
                'LAST_NAME' => 'Петрова',
                'FIRST_NAME' => 'Мария',
                'SECOND_NAME' => 'Сергеевна',
                'XML_ID' => 'DOCTOR_2',
                'procedures' => [
                    $procedureIds['ЭКГ'] ?? null,
                    $procedureIds['УЗИ брюшной полости'] ?? null,
                    $procedureIds['Рентген легких'] ?? null
                ]
            ],
            [
                'LAST_NAME' => 'Сидоров',
                'FIRST_NAME' => 'Алексей',
                'SECOND_NAME' => 'Петрович',
                'XML_ID' => 'DOCTOR_3',
                'procedures' => [
                    $procedureIds['МРТ головного мозга'] ?? null,
                    $procedureIds['Физиотерапия'] ?? null
                ]
            ],
            [
                'LAST_NAME' => 'Козлова',
                'FIRST_NAME' => 'Ольга',
                'SECOND_NAME' => 'Викторовна',
                'XML_ID' => 'DOCTOR_4',
                'procedures' => [
                    $procedureIds['Массаж'] ?? null,
                    $procedureIds['Диагностика зрения'] ?? null
                ]
            ],
            [
                'LAST_NAME' => 'Николаев',
                'FIRST_NAME' => 'Дмитрий',
                'SECOND_NAME' => 'Александрович',
                'XML_ID' => 'DOCTOR_5',
                'procedures' => [
                    $procedureIds['Консультация терапевта'] ?? null,
                    $procedureIds['ЭКГ'] ?? null,
                    $procedureIds['Анализ крови'] ?? null,
                    $procedureIds['МРТ головного мозга'] ?? null
                ]
            ]
        ];
        
        // Фильтруем null значения
        foreach ($doctors as &$doctor) {
            $doctor['procedures'] = array_filter($doctor['procedures']);
        }
        
        $addedCount = 0;
        
        foreach ($doctors as $doctor) {
            $el = new \CIBlockElement();
            
            $fields = [
                'IBLOCK_ID' => $doctorsIblockId,
                'NAME' => $doctor['LAST_NAME'] . ' ' . $doctor['FIRST_NAME'] . ' ' . $doctor['SECOND_NAME'],
                'CODE' => \CUtil::translit($doctor['LAST_NAME'], 'ru'),
                'XML_ID' => $doctor['XML_ID'],
                'ACTIVE' => 'Y',
                'PROPERTY_VALUES' => [
                    'LAST_NAME' => $doctor['LAST_NAME'],
                    'FIRST_NAME' => $doctor['FIRST_NAME'],
                    'SECOND_NAME' => $doctor['SECOND_NAME'],
                    'PROCEDURES' => $doctor['procedures']
                ]
            ];
            
            if ($el->Add($fields)) {
                $addedCount++;
            }
        }
        
        return $addedCount;
    }
    
    /**
     * Очистить кэш ORM
     */
    private static function clearOrmCache(): void
    {
        // Очищаем кэш сущностей ORM
        //\Bitrix\Main\Entity\Base->cleanCache();
        \Bitrix\Iblock\ElementTable::cleanCache();
        
        // Очищаем кэш инфоблоков
        if (class_exists('\Bitrix\Main\Data\Cache')) {
            $cache = \Bitrix\Main\Data\Cache::createInstance();
            $cache->cleanDir('b_iblock');
            $cache->cleanDir('b_iblock_property');
            $cache->cleanDir('lists');
        }
        
        // Обновляем теги кэша
        \CIBlock::clearIblockTagCache(-1);
        
        // Очищаем композитный кэш
        //\CHTMLPagesCache::CleanAll();
        
        // Обновляем кэш списков
        //\CList::ClearCache();
    }
}