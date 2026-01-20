<?php
// local/App/Install/Doctors/Installer.php
namespace Install\Doctors;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Installer
{
    const DOCTORS_IBLOCK_CODE = 'Doctors';
    const PROCEDURES_IBLOCK_CODE = 'Procedures';
    const DOCTORS_IBLOCK_TYPE = 'lists';
    const PROCEDURES_IBLOCK_TYPE = 'lists';
    
    public static function install(): array
    {
        $result = [
            'success' => true,
            'errors' => [],
            'messages' => [],
            'data' => []
        ];
        
        if (!Loader::includeModule('iblock')) {
            $result['success'] = false;
            $result['errors'][] = 'Модуль iblock не установлен';
            return $result;
        }
        
        try {
            $result['messages'][] = '🔧 Начинаем установку приложения "Врачи и процедуры"';
            
            // 1. Полностью удаляем старые инфоблоки
            $removedResult = self::removeExistingIblocks();
            $result['messages'] = array_merge($result['messages'], $removedResult['messages']);
            
            // 2. Создаем инфоблок Процедуры (список)
            $result['messages'][] = '📋 Создаем инфоблок Процедуры...';
            $proceduresIblockId = self::createListIblock(
                self::PROCEDURES_IBLOCK_CODE,
                'Процедуры',
                'Список медицинских процедур'
            );
            
            if (!$proceduresIblockId) {
                $result['success'] = false;
                $result['errors'][] = 'Не удалось создать инфоблок "Процедуры"';
                return $result;
            }
            
            $result['messages'][] = "✅ Инфоблок 'Процедуры' создан (ID: {$proceduresIblockId})";
            $result['data']['procedures_iblock_id'] = $proceduresIblockId;
            
            // 3. Добавляем тестовые процедуры
            $result['messages'][] = '📝 Добавляем тестовые процедуры...';
            $proceduresAdded = self::addTestProcedures($proceduresIblockId);
            $result['messages'][] = "✅ Добавлено процедур: {$proceduresAdded}";
            $result['data']['procedures_added'] = $proceduresAdded;
            
            // 4. Создаем инфоблок Врачи (список)
            $result['messages'][] = '📋 Создаем инфоблок Врачи...';
            $doctorsIblockId = self::createListIblock(
                self::DOCTORS_IBLOCK_CODE,
                'Врачи',
                'Список врачей и их специализаций'
            );
            
            if (!$doctorsIblockId) {
                $result['success'] = false;
                $result['errors'][] = 'Не удалось создать инфоблок "Врачи"';
                return $result;
            }
            
            $result['messages'][] = "✅ Инфоблок 'Врачи' создан (ID: {$doctorsIblockId})";
            $result['data']['doctors_iblock_id'] = $doctorsIblockId;
            
            // 5. Создаем свойства для врачей через старое ядро (это нормально для установки)
            $result['messages'][] = '🔧 Создаем свойства для врачей...';
            $propertiesCreated = self::createDoctorProperties($doctorsIblockId, $proceduresIblockId);
            $result['messages'][] = "✅ Создано свойств: {$propertiesCreated}";
            
            // 6. Добавляем тестовых врачей через старое ядро
            $result['messages'][] = '👨‍⚕️ Добавляем тестовых врачей...';
            $doctorsAdded = self::addTestDoctors($doctorsIblockId, $proceduresIblockId);
            $result['messages'][] = "✅ Добавлено врачей: {$doctorsAdded}";
            $result['data']['doctors_added'] = $doctorsAdded;
            
            // 7. Очищаем кэш для правильной работы ORM D7
            $result['messages'][] = '🧹 Очищаем кэш...';
            self::clearCache();
            
            // 8. Проверяем создание через ORM D7
            $result['messages'][] = '🔍 Проверяем создание через ORM D7...';
            $ormCheck = self::checkOrmEntities($doctorsIblockId, $proceduresIblockId);
            
            if ($ormCheck['success']) {
                $result['messages'][] = '✅ ORM D7 сущности доступны';
            } else {
                $result['messages'][] = '⚠️ Проблемы с ORM D7 сущностями: ' . implode(', ', $ormCheck['errors']);
            }
            
            $result['messages'][] = '🎉 Установка завершена успешно!';
            
        } catch (\Exception $e) {
            $result['success'] = false;
            $result['errors'][] = '❌ Критическая ошибка: ' . $e->getMessage();
            $result['messages'][] = '🔍 Трассировка: ' . $e->getTraceAsString();
        }
        
        return $result;
    }
    
    /**
     * Полностью удалить существующие инфоблоки
     */
    private static function removeExistingIblocks(): array
    {
        $result = [
            'messages' => [],
            'removed' => 0
        ];
        
        $iblockCodes = [
            self::DOCTORS_IBLOCK_CODE,
            self::PROCEDURES_IBLOCK_CODE
        ];
        
        foreach ($iblockCodes as $code) {
            $dbIblock = \CIBlock::GetList([], ['CODE' => $code]);
            if ($iblock = $dbIblock->Fetch()) {
                $iblockId = $iblock['ID'];
                
                // Удаляем все элементы инфоблока
                $dbElements = \CIBlockElement::GetList(
                    [],
                    ['IBLOCK_ID' => $iblockId],
                    false,
                    false,
                    ['ID']
                );
                
                $elementsDeleted = 0;
                while ($element = $dbElements->Fetch()) {
                    if (\CIBlockElement::Delete($element['ID'])) {
                        $elementsDeleted++;
                    }
                }
                
                // Удаляем свойства инфоблока
                $dbProps = \CIBlockProperty::GetList([], ['IBLOCK_ID' => $iblockId]);
                while ($prop = $dbProps->Fetch()) {
                    \CIBlockProperty::Delete($prop['ID']);
                }
                
                // Удаляем сам инфоблок
                if (\CIBlock::Delete($iblockId)) {
                    $result['removed']++;
                    $result['messages'][] = "🗑️ Удален инфоблок '{$code}' (ID: {$iblockId}), элементов: {$elementsDeleted}";
                }
            }
        }
        
        // Даем время на удаление
        sleep(1);
        
        // Очищаем кэш после удаления
        if (class_exists('\Bitrix\Main\Data\Cache')) {
            $cache = \Bitrix\Main\Data\Cache::createInstance();
            $cache->cleanDir('b_iblock');
            $cache->cleanDir('b_iblock_property');
        }
        
        return $result;
    }
    
    /**
     * Создать универсальный список через старое ядро
     */
    private static function createListIblock(string $code, string $name, string $description = ''): ?int
    {
        // Проверяем, не существует ли уже (после удаления должно не быть, но на всякий случай)
        $dbIblock = \CIBlock::GetList([], ['CODE' => $code]);
        if ($existingIblock = $dbIblock->Fetch()) {
            return (int)$existingIblock['ID'];
        }
        
        // Параметры для универсального списка
        $arFields = [
            'ACTIVE' => 'Y',
            'NAME' => $name,
            'CODE' => $code,
            'IBLOCK_TYPE_ID' => 'lists', // Важно: тип "списки"
            'SITE_ID' => ['s1'],
            'SORT' => 500,
            'LIST_MODE' => 'C', // Списки и коллекции
            'REST_ON' => 'Y', // Включить REST API
            'WORKFLOW' => 'N',
            'BIZPROC' => 'N',
            'VERSION' => 1,
            'GROUP_ID' => [
                1 => 'X', // Администраторы - полный доступ
                2 => 'R'  // Все - чтение
            ],
            'DESCRIPTION' => $description,
            'DESCRIPTION_TYPE' => 'text',
            // Критически важные поля
            'API_CODE' => $code,
            'XML_ID' => $code,
        ];
        
        $iblock = new \CIBlock;
        $iblockId = $iblock->Add($arFields);
        
        if ($iblockId) {
            // Дополнительная настройка
            $iblock->Update($iblockId, [
                'API_CODE' => $code,
                'XML_ID' => $code,
            ]);
            
            // Настройка прав
            $iblock->SetPermission($iblockId, [
                1 => 'X',
                2 => 'R'
            ]);
            
            return (int)$iblockId;
        }
        
        return null;
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
                'PROPERTY_TYPE' => 'S',
                'IS_REQUIRED' => 'Y',
                'SORT' => 100,
                'SEARCHABLE' => 'Y',
                'FILTRABLE' => 'Y',
            ],
            [
                'NAME' => 'Имя',
                'CODE' => 'FIRST_NAME',
                'PROPERTY_TYPE' => 'S',
                'IS_REQUIRED' => 'Y',
                'SORT' => 200,
                'SEARCHABLE' => 'Y',
                'FILTRABLE' => 'Y',
            ],
            [
                'NAME' => 'Отчество',
                'CODE' => 'SECOND_NAME',
                'PROPERTY_TYPE' => 'S',
                'IS_REQUIRED' => 'N',
                'SORT' => 300,
                'SEARCHABLE' => 'Y',
                'FILTRABLE' => 'Y',
            ],
            [
                'NAME' => 'Процедуры',
                'CODE' => 'PROCEDURES',
                'PROPERTY_TYPE' => 'E',
                'LINK_IBLOCK_ID' => $proceduresIblockId,
                'MULTIPLE' => 'Y',
                'SORT' => 400,
                'SEARCHABLE' => 'Y',
                'FILTRABLE' => 'Y',
                'WITH_DESCRIPTION' => 'N',
            ]
        ];
        
        $created = 0;
        
        foreach ($properties as $propertyFields) {
            $propertyFields['IBLOCK_ID'] = $doctorsIblockId;
            $propertyFields['ACTIVE'] = 'Y';
            
            $ibp = new \CIBlockProperty;
            if ($ibp->Add($propertyFields)) {
                $created++;
            }
        }
        
        return $created;
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
        
        $added = 0;
        
        foreach ($procedures as $procedureName) {
            $el = new \CIBlockElement;
            
            $code = self::generateUniqueCode($procedureName, $iblockId);
            
            $fields = [
                'IBLOCK_ID' => $iblockId,
                'NAME' => $procedureName,
                'CODE' => $code,
                'ACTIVE' => 'Y',
                'XML_ID' => 'PROCEDURE_' . $code,
            ];
            
            if ($el->Add($fields)) {
                $added++;
            }
        }
        
        return $added;
    }
    
    /**
     * Добавить тестовых врачей
     */
    private static function addTestDoctors(int $doctorsIblockId, int $proceduresIblockId): int
    {
        // Получаем все процедуры
        $procedureIds = [];
        $dbProcedures = \CIBlockElement::GetList(
            ['NAME' => 'ASC'],
            ['IBLOCK_ID' => $proceduresIblockId, 'ACTIVE' => 'Y'],
            false,
            false,
            ['ID', 'NAME']
        );
        
        while ($procedure = $dbProcedures->Fetch()) {
            $procedureIds[$procedure['NAME']] = $procedure['ID'];
        }
        
        $doctors = [
            [
                'LAST_NAME' => 'Иванов',
                'FIRST_NAME' => 'Иван',
                'SECOND_NAME' => 'Иванович',
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
                'procedures' => [
                    $procedureIds['МРТ головного мозга'] ?? null,
                    $procedureIds['Физиотерапия'] ?? null
                ]
            ],
            [
                'LAST_NAME' => 'Козлова',
                'FIRST_NAME' => 'Ольга',
                'SECOND_NAME' => 'Викторовна',
                'procedures' => [
                    $procedureIds['Массаж'] ?? null,
                    $procedureIds['Диагностика зрения'] ?? null
                ]
            ],
            [
                'LAST_NAME' => 'Николаев',
                'FIRST_NAME' => 'Дмитрий',
                'SECOND_NAME' => 'Александрович',
                'procedures' => [
                    $procedureIds['Консультация терапевта'] ?? null,
                    $procedureIds['ЭКГ'] ?? null,
                    $procedureIds['Анализ крови'] ?? null,
                    $procedureIds['МРТ головного мозга'] ?? null
                ]
            ]
        ];
        
        $added = 0;
        
        foreach ($doctors as $doctor) {
            $el = new \CIBlockElement;
            
            $doctorProcedures = array_filter($doctor['procedures']);
            $fullName = trim("{$doctor['LAST_NAME']} {$doctor['FIRST_NAME']} {$doctor['SECOND_NAME']}");
            
            // Генерируем уникальный код
            $code = self::generateUniqueCode($fullName, $doctorsIblockId);
            
            $fields = [
                'IBLOCK_ID' => $doctorsIblockId,
                'NAME' => $fullName,
                'CODE' => $code,
                'ACTIVE' => 'Y',
                'PROPERTY_VALUES' => [
                    'LAST_NAME' => $doctor['LAST_NAME'],
                    'FIRST_NAME' => $doctor['FIRST_NAME'],
                    'SECOND_NAME' => $doctor['SECOND_NAME'],
                    'PROCEDURES' => $doctorProcedures
                ]
            ];
            
            if ($el->Add($fields)) {
                $added++;
            }
        }
        
        return $added;
    }
    
    /**
     * Генерировать уникальный код элемента
     */
    private static function generateUniqueCode(string $name, int $iblockId): string
    {
        $baseCode = \CUtil::translit($name, 'ru', [
            'replace_space' => '-',
            'replace_other' => '-',
            'change_case' => 'L'
        ]);
        
        $code = $baseCode;
        $counter = 1;
        
        // Проверяем уникальность кода в инфоблоке
        while (true) {
            $dbElement = \CIBlockElement::GetList(
                [],
                ['IBLOCK_ID' => $iblockId, '=CODE' => $code],
                false,
                false,
                ['ID']
            );
            
            if (!$dbElement->Fetch()) {
                break;
            }
            
            $code = $baseCode . '-' . $counter;
            $counter++;
        }
        
        return $code;
    }
    
    /**
     * Проверить ORM D7 сущности
     */
    private static function checkOrmEntities(int $doctorsIblockId, int $proceduresIblockId): array
    {
        $result = [
            'success' => true,
            'errors' => []
        ];
        
        try {
            Loader::includeModule('iblock');
            
            // Проверяем врачей через ORM D7
            $doctorsEntity = \Bitrix\Iblock\Iblock::wakeUp($doctorsIblockId);
            if (!$doctorsEntity) {
                $result['success'] = false;
                $result['errors'][] = 'Не удалось получить сущность врачей';
            } else {
                $doctorsDataClass = $doctorsEntity->getEntityDataClass();
                if (!$doctorsDataClass) {
                    $result['success'] = false;
                    $result['errors'][] = 'Не удалось получить DataClass врачей';
                }
            }
            
            // Проверяем процедуры через ORM D7
            $proceduresEntity = \Bitrix\Iblock\Iblock::wakeUp($proceduresIblockId);
            if (!$proceduresEntity) {
                $result['success'] = false;
                $result['errors'][] = 'Не удалось получить сущность процедур';
            } else {
                $proceduresDataClass = $proceduresEntity->getEntityDataClass();
                if (!$proceduresDataClass) {
                    $result['success'] = false;
                    $result['errors'][] = 'Не удалось получить DataClass процедур';
                }
            }
            
        } catch (\Exception $e) {
            $result['success'] = false;
            $result['errors'][] = $e->getMessage();
        }
        
        return $result;
    }
    
    /**
     * Очистить кэш
     */
    private static function clearCache(): void
    {
        // Очищаем тегированный кэш инфоблоков
        \CIBlock::ClearIblockTagCache(-1);
        
        // Очищаем управляемый кэш
        if (class_exists('\Bitrix\Main\Data\Cache')) {
            $cache = \Bitrix\Main\Data\Cache::createInstance();
            $cache->cleanDir('b_iblock');
            $cache->cleanDir('b_iblock_property');
            $cache->cleanDir('b_iblock_element');
        }
        
        // Очищаем кэш ORM
        if (class_exists('\Bitrix\Main\ORM\Data\DataManager')) {
            \Bitrix\Iblock\IblockTable::getEntity()->cleanCache();
            \Bitrix\Iblock\ElementTable::getEntity()->cleanCache();
            \Bitrix\Iblock\PropertyTable::getEntity()->cleanCache();
        }
        
        // Очищаем кэш компонентов
        \CBitrixComponent::clearComponentCache('bitrix:lists');
    }
}