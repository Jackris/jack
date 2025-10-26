<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
?>

<div class="crm-deal-form">
    <h2>Создание сделки</h2>
    
    <form id="deal-form">
        <div class="form-group">
            <label for="deal-name">Название сделки:</label>
            <input type="text" id="deal-name" name="deal_name" required>
        </div>
        
        <div class="form-group">
            <label for="contact-search">Контакт (поиск по ФИО):</label>
            <input type="text" id="contact-search" name="contact_search" placeholder="Введите ФИО контакта">
            <input type="hidden" id="contact-id" name="contact_id" value="">
            <div class="contact-suggestions"></div>
            <button type="button" id="create-contact-btn">Создать новый контакт</button>
        </div>
        
        <div class="form-group">
            <label for="deal-sum">Сумма:</label>
            <input type="number" id="deal-sum" name="deal_sum" step="0.01">
        </div>
        
        <div class="form-group">
            <label for="deal-description">Описание:</label>
            <textarea id="deal-description" name="deal_description"></textarea>
        </div>
        
        <button type="submit">Сохранить</button>
    </form>
    
    <div id="notification-area"></div>
</div>

<div id="contact-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" id="close-modal">&times;</span>
        <h3>Создание нового контакта</h3>
        <form id="contact-form">
            <div class="form-group">
                <label for="first-name">Имя:</label>
                <input type="text" id="first-name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last-name">Фамилия:</label>
                <input type="text" id="last-name" name="last_name">
            </div>
            <div class="form-group">
                <label for="second-name">Отчество:</label>
                <input type="text" id="second-name" name="second_name">
            </div>
            <button type="submit">Создать контакт</button>
        </form>
    </div>
</div>

<style>
    .crm-deal-form {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
        box-sizing: border-box;
    }
    
    .form-group textarea {
        height: 100px;
        resize: vertical;
    }
    
    .contact-suggestions {
        margin-top: 5px;
    }
    
    .contact-suggestions div {
        padding: 5px;
        cursor: pointer;
        background-color: #f9f9f9;
        border: 1px solid #eee;
    }
    
    .contact-suggestions div:hover {
        background-color: #e9e9e9;
    }
    
    .modal {
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.4);
    }
    
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        border-radius: 5px;
        width: 400px;
        position: relative;
    }
    
    .close {
        position: absolute;
        right: 10px;
        top: 10px;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
    }
    
    #notification-area {
        margin-top: 15px;
    }
    
    .notification {
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 3px;
    }
    
    .notification.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .notification.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<script src="<?=$this->getComponentPath()?>/script.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализация компонента
        new CrmDealComponent('<?=$this->getComponentName()?>', '<?=$this->getTemplate()->getComponent()->getName()?>');
    });
</script>