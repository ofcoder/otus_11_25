<?php

namespace Models\Lists;

use Helper\Text;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Iblock\Iblock;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Main\Entity\ReferenceField;
use Models\Lists\ElementProceduresTable;

class ElementDoctorsTable extends DataManager
{
    const IBLOCK_CODE = 'Doctors';    
    protected static $iblockId = null;
    protected static $dataClass = null;
    
    public static function getMap(): array
    {
        $map = [
            'PROCEDURES' => new ReferenceField(
                'PROCEDURES_LIST',
                ElementProceduresTable::class,
                ['=this.PROCEDURES' => 'ref.IBLOCK_ELEMENT_ID']
            )
        ];
        
        return parent::getMap() + $map;
        
    }
    
    
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
    private static function initIblockId(): void
    {
        if (!Loader::includeModule('iblock')) {
            throw new SystemException('Модуль iblock не подключен');
        }
        
        $iblock = IblockTable::getList([
            'filter' => ['CODE' => self::IBLOCK_CODE],
            'select' => ['ID']
        ])->fetch();
        
        if ($iblock) {
            self::$iblockId = (int)$iblock['ID'];
        } else {
            throw new SystemException('Инфоблок doctors не найден');
        }
    }
    
    /**
     * Получить DataClass для работы с элементами
     */
    public static function getDataClass()
    {
        IblockTable::compileEntity(self::IBLOCK_CODE);
        
        if (self::$dataClass === null) {
            self::initIblockId();
            
            // Используем Iblock::wakeUp() как в статье
            $iblock = Iblock::wakeUp(self::$iblockId);
            if (!$iblock) {
                throw new SystemException('Не удалось получить инфоблок doctors');
            }
            
            self::$dataClass = $iblock->getEntityDataClass();
            if (!self::$dataClass) {
                throw new SystemException('Не удалось получить DataClass для инфоблока doctors');
            }
        }
        
        return self::$dataClass;
    }
    
    /**
     * Получить всех врачей с процедурами
     */
    public static function getDoctorsWithProcedures(): array
    {
        $dataClass = self::getDataClass();
        $doctors = [];
        
        // Получаем врачей с их свойствами
        $doctorsDb = $dataClass::getList([
            'select' => [
                '*',
                'NAME',
                'LAST_NAME',
                'FIRST_NAME',
                'SECOND_NAME',
                'PROCEDURES.ELEMENT'
            ],
            'filter' => ['=ACTIVE' => 'Y'],
            //'order' => ['LAST_NAME' => 'ASC']
        ])->fetchCollection();
        
        if (empty($doctorsDb)) {
            return [];
        }
        
        foreach ($doctorsDb as $doctor) {
            if (empty($doctor)) {
                continue;
            }
            
            
            // Получаем названия процедур
            $procedureNames = [];
            $procedureIds = [];
            foreach ($doctor->getProcedures()->getAll() as $procedure) {
                $procedureNames[] = $procedure->getElement()->getName();
                $procedureIds[] = $procedure->getElement()->getId();
            }
            if ($doctor->get('LAST_NAME')) {
                $lastName = $doctor->get('LAST_NAME')->getValue() ?: '';
            }
            if ($doctor->get('FIRST_NAME')) {
                $firstName = $doctor->get('FIRST_NAME')->getValue() ?: '';
            }
            if ($doctor->get('SECOND_NAME')) {
                $secondName = $doctor->get('SECOND_NAME')->getValue() ?: '';
            }
            
            
            $doctors[] = [
                'ID' => $doctor->getId(),
                'NAME' => $doctor->getName() ?? '',
                'FULL_NAME' => $lastName . ' ' . $firstName . ' ' . $secondName,
                'LAST_NAME' => $lastName,
                'FIRST_NAME' => $firstName,
                'SECOND_NAME' => $secondName,
                'PROCEDURE_NAMES' => $procedureNames,
                'PROCEDURE_IDS' => $procedureIds,
            ];
        }
        
        return $doctors;
    }
    
    /**
     * Добавить врача через D7 ORM
     */
    public static function addDoctor(array $data): ?int
    {
        
        if (!Loader::includeModule('iblock')) {
            throw new SystemException('Модуль iblock не подключен');
        }
        
        $proceduresClass = ElementProceduresTable::getDataClass();
        $doctorsClass = self::getDataClass();
        
        $doctor = $doctorsClass::createObject()
            //->set('XML_ID', Text::translit($data['SECOND_NAME']))
            //->setCode('CODE', Text::translit($data['SECOND_NAME']))
            ->setName("{$data['LAST_NAME']} {$data['FIRST_NAME']} {$data['SECOND_NAME']}" ?? '')
            ->setLastName($data['LAST_NAME'])
            ->setFirstName($data['FIRST_NAME'])
            ->setSecondName($data['SECOND_NAME']);
        
        
        //Сначала сохраняем доктора
        $doctor->save();
        
        if (empty($data['PROCEDURES'])) {
            return $doctor->getId();
        }
        
        // Находим процедуры для привязки
        $relatedProcedures = $proceduresClass::query()
            ->setSelect(['*'])
            ->whereIn('ID', $data['PROCEDURES'])
            ->fetchCollection();
        
        // а потом привязываем найденные процедуры
        if (!empty($relatedProcedures)){
            foreach ($relatedProcedures as $procedure) {            
                $doctor->addTo('PROCEDURES', $procedure->getId());
            }
        }

        
        $doctor->save();
        
        if ($doctor->getId()) {
            return $doctor->getId();
        }
        
        return null;
    }
    
    /**
     * Получить врача по ID
     */
    public static function getDoctorById(int $id): ?array
    {
        $dataClass = self::getDataClass();
        
        $doctorDb = $dataClass::getByPrimary($id, [
            'select' => [
                '*',
                'LAST_NAME',
                'FIRST_NAME',
                'SECOND_NAME',
                'PROCEDURES.ELEMENT'
            ]
        ])->fetchObject();
        
        $proceduresNames = [];
        $proceduresIds = [];
        if (!empty($doctorDb->getProcedures())) {
            //$procedureNames = $doctorDb->getProcedures()->getValues();
            
            foreach ($doctorDb->getProcedures()->getAll() as $procedure) {
                $proceduresNames[] = $procedure->getElement()->getName();
                $proceduresIds[] = $procedure->getElement()->getId();
            }
            
        }
        $lastName = $doctorDb->get('LAST_NAME')->getValue();
        $firstName = $doctorDb->get('FIRST_NAME')->getValue();
        $secondName = $doctorDb->get('SECOND_NAME')->getValue();
        
        return [
            'ID' => $doctorDb->get('ID'),
            'NAME' => $doctorDb->get('NAME'),
            'FULL_NAME' => trim($lastName . ' ' . $firstName . ' ' . ($secondName ?? '')),
            'LAST_NAME' => $lastName,
            'FIRST_NAME' => $firstName,
            'SECOND_NAME' => $secondName ?? '',
            'PROCEDURES' => $proceduresNames,
            'PROCEDURES_IDS' => $proceduresIds
        ];
        
        
    }
    
    /**
     * Обновить врача через ORM D7
     */
    public static function updateDoctor(int $doctorId, array $data)
    {
        try {
            
            //$proceduresClass = IblockTable::compileEntity(self::PROCEDURES_IBLOCK_CODE)->getDataClass();
            $proceduresClass = ElementProceduresTable::getDataClass();
            //$doctorsClass = IblockTable::compileEntity(self::IBLOCK_CODE)->getDataClass();
            $doctorsClass = self::getDataClass();
            // Находим элемент по символьному коду
            $doctor = $doctorsClass::query()
                ->where('ID', $doctorId)
                ->setLimit(1)
                ->fetchObject();
            
            if ($doctor) {
                // Формируем полное имя
                $lastName = $data['LAST_NAME'] ?? '';
                $firstName = $data['FIRST_NAME'] ?? '';
                $secondName = $data['SECOND_NAME'] ?? '';
                $fullName = trim("{$lastName} {$firstName} {$secondName}");
                $newProceduresIds = $data['PROCEDURES'] ?? [];
                // Изменение стандартных полей
                $doctor
                    ->setName($fullName)
                    ->setSort(50)
                    ->set('LAST_NAME', $lastName)
                    ->set('SECOND_NAME', $secondName)
                    ->set('FIRST_NAME', $firstName)
                    ->removeAll('PROCEDURES');
                //Сначала сохраняем доктора
                $doctor->save();
                // А потом привязываем найденные процедуры.
                // Находим процедуры для привязки
                $relatedProcedures = [];
                $relatedProcedures = $proceduresClass::query()
                    ->setSelect(['*'])
                    ->whereIn('ID', $newProceduresIds)
                    ->fetchCollection();
                if (!empty($relatedProcedures)) {
                    foreach ($relatedProcedures as $procedure) {
                        $doctor->addTo('PROCEDURES', $procedure->getId());
                    }
                }

                $doctor->save();
                return true;
            }
        } catch (\Exception $e) {
            
            return false;
        }
    }
    
    /**
     * Удалить врача через ORM D7
     */
    public static function deleteDoctor(int $doctorId): bool
    {
        $dataClass = self::getDataClass();
        $result = $dataClass::delete($doctorId);
        return $result->isSuccess();
    }
    
    /**
     * Генерировать уникальный код для врача
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
        
        $dataClass = self::getDataClass();
        
        // Проверяем уникальность через ORM D7
        while (true) {
            
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
    
    /**
     * Получить список врачей (простой метод для теста)
     */
    public static function getDoctorsList(): array
    {
        $dataClass = self::getDataClass();
        $doctors = [];
        
        $doctorsDb = $dataClass::getList([
            'select' => ['*', 'NAME'],
            'filter' => ['=ACTIVE' => 'Y'],
            //'order' => ['NAME' => 'ASC']
        ])->fetchCollection();
        
        foreach ($doctorsDb as $doctor) {
            $doctors[$doctor->getId()] = $doctor->getName();
        }
        
        return $doctors;
    }
}