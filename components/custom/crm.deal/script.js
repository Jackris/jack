class CrmDealComponent {
    constructor(componentName, templateName) {
        this.componentName = componentName;
        this.templateName = templateName;
        
        // Элементы формы
        this.contactSearch = document.getElementById('contact-search');
        this.contactIdInput = document.getElementById('contact-id');
        this.contactSuggestions = document.querySelector('.contact-suggestions');
        this.createContactBtn = document.getElementById('create-contact-btn');
        this.modal = document.getElementById('contact-modal');
        this.closeModal = document.getElementById('close-modal');
        this.contactForm = document.getElementById('contact-form');
        this.dealForm = document.getElementById('deal-form');
        this.notificationArea = document.getElementById('notification-area');
        
        this.init();
    }
    
    init() {
        // Открытие модального окна для создания контакта
        this.createContactBtn.addEventListener('click', () => {
            this.modal.style.display = 'block';
        });
        
        // Закрытие модального окна
        this.closeModal.addEventListener('click', () => {
            this.modal.style.display = 'none';
        });
        
        // Закрытие модального окна при клике вне его
        window.addEventListener('click', (event) => {
            if (event.target === this.modal) {
                this.modal.style.display = 'none';
            }
        });
        
        // Обработка поиска контактов
        this.contactSearch.addEventListener('input', this.debounce((e) => {
            const query = e.target.value.trim();
            
            if (query.length > 2) {
                this.searchContacts(query);
            } else {
                this.contactSuggestions.innerHTML = '';
                this.contactIdInput.value = '';
            }
        }, 300));
        
        // Отправка формы создания контакта
        this.contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(this.contactForm);
            const newContact = {
                first_name: formData.get('first_name'),
                last_name: formData.get('last_name'),
                second_name: formData.get('second_name')
            };
            
            // Устанавливаем данные нового контакта в скрытое поле
            this.contactIdInput.value = JSON.stringify(newContact);
            
            // Закрываем модальное окно
            this.modal.style.display = 'none';
            
            // Обновляем поле поиска
            this.contactSearch.value = [newContact.first_name, newContact.last_name, newContact.second_name]
                .filter(name => name).join(' ');
            
            this.showNotification('Контакт будет создан при сохранении сделки', 'success');
        });
        
        // Отправка формы сделки
        this.dealForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(this.dealForm);
            
            // Проверяем, является ли контакт новым (JSON строка)
            const contactIdValue = this.contactIdInput.value;
            let newContact = null;
            
            if (contactIdValue.startsWith('{') && contactIdValue.endsWith('}')) {
                newContact = JSON.parse(contactIdValue);
                formData.delete('contact_id');
            }
            
            const dealData = {
                deal_name: formData.get('deal_name'),
                contact_id: parseInt(formData.get('contact_id')) || 0,
                new_contact: newContact,
                deal_sum: parseFloat(formData.get('deal_sum')) || 0,
                deal_description: formData.get('deal_description') || ''
            };
            
            this.createDeal(dealData);
        });
    }
    
    // Функция поиска контактов с задержкой
    debounce(func, wait) {
        let timeout;
        return (...args) => {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Поиск контактов
    async searchContacts(query) {
        try {
            const response = await BX.ajax.runAction(this.componentName + '.searchContacts', {
                data: {
                    query: query
                }
            });
            
            this.contactSuggestions.innerHTML = '';
            
            if (response.data.length === 0) {
                this.contactSuggestions.innerHTML = '<div>Контакт не найден</div>';
                return;
            }
            
            response.data.forEach(contact => {
                const div = document.createElement('div');
                div.textContent = contact.NAME;
                div.dataset.id = contact.ID;
                div.addEventListener('click', () => {
                    this.contactSearch.value = contact.NAME;
                    this.contactIdInput.value = contact.ID;
                    this.contactSuggestions.innerHTML = '';
                });
                this.contactSuggestions.appendChild(div);
            });
        } catch (error) {
            console.error('Ошибка поиска контактов:', error);
            this.showNotification('Ошибка поиска контактов', 'error');
        }
    }
    
    // Создание сделки
    async createDeal(dealData) {
        try {
            const response = await BX.ajax.runAction(this.componentName + '.createDeal', {
                data: dealData
            });
            
            if (response.data.success) {
                this.showNotification(response.data.message, 'success');
                this.dealForm.reset();
                this.contactIdInput.value = '';
                this.contactSuggestions.innerHTML = '';
            } else {
                if (response.data.errors && response.data.errors.length > 0) {
                    response.data.errors.forEach(error => {
                        this.showNotification(error, 'error');
                    });
                } else {
                    this.showNotification('Ошибка при создании сделки', 'error');
                }
            }
        } catch (error) {
            console.error('Ошибка:', error);
            this.showNotification('Ошибка сети при создании сделки', 'error');
        }
    }
    
    // Функция отображения уведомлений
    showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        this.notificationArea.appendChild(notification);
        
        // Автоматически удаляем уведомление через 5 секунд
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
}