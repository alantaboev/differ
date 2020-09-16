<?php

namespace Differ\Formatters\Pretty;

function render(array $differences)
{
    $lines = getLines($differences); // Получение строк
    return "{\n$lines\n}"; // Возврат всех строк в фигурных скобках
}

function getLines(array $tree)
{
    $indent = '  '; // Отступ строки (двойной пробел)
    // Сбор строк в массив
    $strings = array_reduce($tree, function ($acc, $node) use ($indent) {
        switch ($node['type']) {
            case 'unchanged':
                $value = checkValue($node['value']); // Если булев, то вернет строку, соответствующую значению булева
                $acc[] = "$indent  {$node['key']}: $value";
                break;
            case 'added':
                $value = checkValue($node['value']);
                $acc[] = "$indent+ {$node['key']}: $value";
                break;
            case 'deleted':
                $value = checkValue($node['value']);
                $acc[] = "$indent- {$node['key']}: $value";
                break;
            case 'changed':
                $oldValue = checkValue($node['beforeValue']);
                $newValue = checkValue($node['afterValue']);
                $acc[] = "$indent- {$node['key']}: $oldValue";
                $acc[] = "$indent+ {$node['key']}: $newValue";
                break;
            default:
                throw new \Exception("Unknown action '{$node['type']}'");
        }
        return $acc;
    }, $acc = []);
    return implode("\n", $strings); // Возврат массива в строки с разделителем переноса
}

// Проверка значения ключа. Если булев, то возврат строки, соответствующей значению
function checkValue($value): string
{
    if ($value === false) {
        return 'false';
    }
    if ($value === true) {
        return 'true';
    }
    return $value;
}