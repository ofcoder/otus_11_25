<?php
// /bx_razr/students_dz/homework3/doctors/check-lists.php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if (!CModule::IncludeModule('iblock')) {
    die('Модуль iblock не установлен');
}

echo "<h1>Проверка настроек универсальных списков</h1>";

$iblockCodes = ['doctors', 'procedures'];

foreach ($iblockCodes as $code) {
    echo "<h2>Инфоблок: $code</h2>";
    
    // Проверяем через D7 ORM
    $iblock = \Bitrix\Iblock\IblockTable::getList([
        'filter' => ['CODE' => $code],
        'select' => ['*']
    ])->fetch();
    
    if ($iblock) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Поле</th><th>Значение</th></tr>";
        
        $importantFields = [
            'ID', 'NAME', 'CODE', 'IBLOCK_TYPE_ID', 'API_CODE', 'XML_ID',
            'LIST_MODE', 'REST_ON', 'WORKFLOW', 'BIZPROC'
        ];
        
        foreach ($importantFields as $field) {
            $value = $iblock[$field] ?? '(не задано)';
            $color = '';
            
            // Проверяем критические поля
            if ($field === 'API_CODE' && empty($value)) {
                $color = 'style="color: red;"';
                $value .= ' ❌ ВНИМАНИЕ: API_CODE не задан!';
            }
            
            if ($field === 'XML_ID' && empty($value)) {
                $color = 'style="color: red;"';
                $value .= ' ❌ ВНИМАНИЕ: XML_ID не задан!';
            }
            
            if ($field === 'LIST_MODE' && $value !== 'C') {
                $color = 'style="color: orange;"';
                $value .= ' ⚠️ Рекомендуется: C (списки и коллекции)';
            }
            
            if ($field === 'REST_ON' && $value !== 'Y') {
                $color = 'style="color: orange;"';
                $value .= ' ⚠️ Рекомендуется: Y (включить REST API)';
            }
            
            echo "<tr><td><strong>$field</strong></td><td $color>$value</td></tr>";
        }
        
        echo "</table>";
        
        // Проверяем свойства
        echo "<h3>Свойства:</h3>";
        $dbProps = \CIBlockProperty::GetList([], ['IBLOCK_ID' => $iblock['ID']]);
        $hasProps = false;
        
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Код</th><th>Название</th><th>Тип</th><th>Множественное</th></tr>";
        
        while ($prop = $dbProps->Fetch()) {
            $hasProps = true;
            $multiple = $prop['MULTIPLE'] === 'Y' ? '✅ Да' : 'Нет';
            echo "<tr>";
            echo "<td>{$prop['ID']}</td>";
            echo "<td>{$prop['CODE']}</td>";
            echo "<td>{$prop['NAME']}</td>";
            echo "<td>{$prop['PROPERTY_TYPE']}</td>";
            echo "<td>$multiple</td>";
            echo "</tr>";
        }
        
        if (!$hasProps) {
            echo "<tr><td colspan='5' style='color: orange;'>⚠️ Свойств нет</td></tr>";
        }
        
        echo "</table>";
        
        // Проверяем элементы
        echo "<h3>Элементы:</h3>";
        $dbElements = \CIBlockElement::GetList([], ['IBLOCK_ID' => $iblock['ID']], false, false, ['ID', 'NAME', 'CODE', 'XML_ID']);
        $elementCount = 0;
        
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Название</th><th>Код</th><th>XML_ID</th></tr>";
        
        while ($el = $dbElements->Fetch()) {
            $elementCount++;
            echo "<tr>";
            echo "<td>{$el['ID']}</td>";
            echo "<td>{$el['NAME']}</td>";
            echo "<td>{$el['CODE']}</td>";
            echo "<td>{$el['XML_ID']}</td>";
            echo "</tr>";
        }
        
        echo "<tr><td colspan='4'>Всего элементов: $elementCount</td></tr>";
        echo "</table>";
        
    } else {
        echo "<p style='color: red;'>❌ Инфоблок не найден</p>";
    }
    
    echo "<hr>";
}

// Проверяем доступность через REST API
echo "<h2>Проверка REST API</h2>";

// Ссылки на REST API
foreach ($iblockCodes as $code) {
    $iblock = \Bitrix\Iblock\IblockTable::getList([
        'filter' => ['CODE' => $code],
        'select' => ['ID', 'API_CODE']
    ])->fetch();
    
    if ($iblock && !empty($iblock['API_CODE'])) {
        echo "<p>✅ $code: REST API доступен через код: {$iblock['API_CODE']}</p>";
        echo "<p>Ссылка: /rest/lists.element.get?IBLOCK_TYPE_ID=lists&IBLOCK_CODE={$iblock['API_CODE']}</p>";
    } elseif ($iblock) {
        echo "<p style='color: orange;'>⚠️ $code: REST API может быть недоступен (API_CODE не задан)</p>";
    }
}

echo "<hr>";
echo "<p><a href='index.php'>Вернуться в приложение</a></p>";
echo "<p><a href='index.php?action=install'>Переустановить данные</a></p>";

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');