<?php
/**
 * Файл обработки AJAX-запросов на создание сделки и контакта
 * для компонента local/components/custom/crm.deal
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use Bitrix\Crm;
use Bitrix\Main\UserTable;

try {
    // Защита от CSRF-атак
    if (!check_bitrix_sessid()) {
        throw new \Exception('Неверный идентификатор сессии');
    }

    // Проверка метода запроса
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new \Exception('Недопустимый метод запроса');
    }

    // Проверка прав доступа
    if (!CUser::IsAuthorized() || !CModule::IncludeModule('crm')) {
        throw new \Exception('Недостаточно прав для выполнения операции');
    }

    // Получение и валидация параметров
    $dealName = isset($_POST['deal_name']) ? trim($_POST['deal_name']) : '';
    $contactId = isset($_POST['contact_id']) ? (int)$_POST['contact_id'] : 0;
    $newContact = isset($_POST['new_contact']) ? $_POST['new_contact'] : null;
    $dealSum = isset($_POST['deal_sum']) ? floatval($_POST['deal_sum']) : 0;
    $dealDescription = isset($_POST['deal_description']) ? trim($_POST['deal_description']) : '';

    // Валидация данных
    $errors = [];

    if (empty($dealName)) {
        $errors[] = 'Название сделки обязательно';
    }

    // Проверка длины названия сделки
    if (strlen($dealName) > 255) {
        $errors[] = 'Слишком длинное название сделки';
    }

    // Проверка суммы сделки
    if ($dealSum < 0) {
        $errors[] = 'Сумма сделки не может быть отрицательной';
    }

    // Проверка длины описания
    if (strlen($dealDescription) > 1000) {
        $errors[] = 'Слишком длинное описание сделки';
    }

    // Если контакт не выбран и не создается новый
    if ($contactId <= 0 && !$newContact) {
        $errors[] = 'Необходимо выбрать контакт или создать новый';
    }

    // Валидация данных нового контакта
    if ($newContact) {
        if (!is_array($newContact)) {
            $errors[] = 'Данные нового контакта должны быть в формате массива';
        } else {
            $firstName = isset($newContact['first_name']) ? trim($newContact['first_name']) : '';
            $lastName = isset($newContact['last_name']) ? trim($newContact['last_name']) : '';
            $secondName = isset($newContact['second_name']) ? trim($newContact['second_name']) : '';

            // Проверка максимальной длины полей контакта
            if (strlen($firstName) > 50) {
                $errors[] = 'Слишком длинное имя контакта';
            }
            if (strlen($lastName) > 50) {
                $errors[] = 'Слишком длинная фамилия контакта';
            }
            if (strlen($secondName) > 50) {
                $errors[] = 'Слишком длинное отчество контакта';
            }

            // Проверка, что хотя бы одно поле имени заполнено
            if (empty($firstName) && empty($lastName)) {
                $errors[] = 'У нового контакта должны быть указаны имя или фамилия';
            }
        }
    }

    // Проверка на потенциальные XSS-атаки в названии сделки
    if (preg_match('/(<script|javascript:|vbscript:|onload|onerror)/i', $dealName)) {
        $errors[] = 'Недопустимые символы в названии сделки';
    }

    // Если есть ошибки валидации, возвращаем их
    if (!empty($errors)) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['success' => false, 'errors' => $errors]);
        die();
    }

    $result = [
        'success' => false,
        'errors' => [],
        'data' => []
    ];

    // Создаем контакт, если указан
    if ($newContact) {
        $contactFields = [
            'NAME' => isset($newContact['first_name']) ? trim($newContact['first_name']) : '',
            'LAST_NAME' => isset($newContact['last_name']) ? trim($newContact['last_name']) : '',
            'SECOND_NAME' => isset($newContact['second_name']) ? trim($newContact['second_name']) : ''
        ];

        // Удаляем пустые значения
        $contactFields = array_filter($contactFields, function($value) {
            return $value !== '';
        });

        $contactRes = Crm\ContactTable::add($contactFields);
        if (!$contactRes->isSuccess()) {
            $result['errors'] = array_merge($result['errors'], $contactRes->getErrorMessages());
        } else {
            $contactId = $contactRes->getId();
            $result['data']['contact_id'] = $contactId;
        }
    } else if ($contactId > 0) {
        // Проверяем, что контакт существует
        $existingContact = Crm\ContactTable::getRow([
            'select' => ['ID'],
            'filter' => ['ID' => $contactId]
        ]);

        if (!$existingContact) {
            $result['errors'][] = 'Выбранный контакт не существует';
        }
    }

    if ($contactId > 0 && empty($result['errors'])) {
        // Создаем сделку
        $dealFields = [
            'TITLE' => $dealName,
            'OPPORTUNITY' => $dealSum,
            'COMMENTS' => $dealDescription,
            'STAGE_ID' => 'NEW', // Новая стадия
            'CONTACT_ID' => $contactId
        ];

        $dealRes = Crm\DealTable::add($dealFields);
        if ($dealRes->isSuccess()) {
            $result['success'] = true;
            $result['message'] = 'Сделка успешно создана';
            $result['data']['deal_id'] = $dealRes->getId();
        } else {
            $result['errors'] = array_merge($result['errors'], $dealRes->getErrorMessages());
        }
    }

    header('Content-Type: application/json');
    if (!empty($result['errors'])) {
        http_response_code(400);
    }
    echo json_encode($result);

} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'errors' => [$e->getMessage()]]);
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');