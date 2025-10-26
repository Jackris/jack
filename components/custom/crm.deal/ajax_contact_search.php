<?php
/**
 * Файл обработки AJAX-запросов на поиск контактов
 * для компонента local/components/custom/crm.deal
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use Bitrix\Crm;

try {
    // Защита от CSRF-атак
    if (!check_bitrix_sessid()) {
        throw new \Exception('Неверный идентификатор сессии');
    }

    // Проверка прав доступа
    if (!CUser::IsAuthorized() || !CModule::IncludeModule('crm')) {
        throw new \Exception('Недостаточно прав для выполнения операции');
    }

    // Проверка метода запроса
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new \Exception('Недопустимый метод запроса');
    }

    // Получение и валидация параметров
    $query = isset($_POST['query']) ? trim($_POST['query']) : '';
    
    // Ограничение длины запроса
    if (strlen($query) > 100) {
        throw new \Exception('Слишком длинный поисковый запрос');
    }
    
    // Дополнительная проверка на потенциальные SQL-инъекции
    if (preg_match('/(\b(union|select|insert|delete|update|drop|create|alter|exec|script|javascript|vbscript)\b)/i', $query)) {
        throw new \Exception('Недопустимые символы в поисковом запросе');
    }

    // Проверка, что запрос не пустой и содержит хотя бы 3 символа
    if (empty($query) || strlen($query) < 3) {
        echo json_encode([]);
        die();
    }

    // Защита от SQL-инъекций с использованием методов Bitrix
    $safeQuery = $DB->ForSQL($query);
    
    // Поиск контактов с использованием ORM
    $contacts = [];
    $rsContacts = Crm\ContactTable::getList([
        'select' => ['ID', 'NAME', 'LAST_NAME', 'SECOND_NAME'],
        'filter' => [
            'LOGIC' => 'OR',
            ['%NAME' => '%' . $safeQuery . '%'],
            ['%LAST_NAME' => '%' . $safeQuery . '%'],
            ['%SECOND_NAME' => '%' . $safeQuery . '%'],
            ['%FULL_NAME' => '%' . $safeQuery . '%']
        ],
        'limit' => 10
    ]);

    while ($contact = $rsContacts->fetch()) {
        $fullName = trim($contact['NAME'] . ' ' . $contact['LAST_NAME'] . ' ' . $contact['SECOND_NAME']);
        $contacts[] = [
            'ID' => (int)$contact['ID'],
            'NAME' => htmlspecialcharsbx($fullName)
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($contacts);

} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');