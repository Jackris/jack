import {Type} from 'main.core';
import './style.css';

export class CrmDeal
{
	createContactDialog()
	{
		//ПОИСК КОНТАКТОВ
		const field = document.getElementById('contact_search');
		console.log(field);
		let dialog = new BX.UI.EntitySelector.Dialog({
			targetNode: field,
			context: 'MY_PAGE_CONTEXT',
			enableSearch: true,
			searchOptions: {
				allowCreateItem: false
			},
			multiple: false,
			entities: [
				{
					id: 'contact',
					dynamicLoad: true,
					dynamicSearch: true
				},
			],
			events: {
				'Item:onSelect': (event) => {
					const selectedItem = event.getData().item;
					console.log(selectedItem);
					field.value = selectedItem.getTitle();
				}
			}
		});
		field.addEventListener('click', () => {
			dialog.show();
		});

		//МОДАЛКА
		let htmlCode = this.generateHtml();
		console.log(htmlCode);
		var oPopup = new BX.PopupWindow('contact_create', window.body, {

			lightShadow: true,
			closeIcon: true,
			closeByEsc: true,
			overlay: {
				backgroundColor: 'gray', opacity: '80'
			},
			titleBar: {
				content: BX.create('div', {
					children: [
						BX.create('h1', {'text': 'Создание контакта: '})
					]
				})
			},
			content: htmlCode,
		});
		//oPopup.setContent(BX('contact_create_form'));

		document.querySelector('#create_contact').addEventListener('click', () => {
			oPopup.show();
		});
		document.querySelector('#save_contact_info').addEventListener('click', () => {
			oPopup.close();
		});
	}

	generateHtml(){
		let fields = {
			'NAME': 'Имя',
			'LAST_NAME': 'Фамилия',
			'SECOND_NAME': 'Отчество'
		};
		let resultHtml = BX.create('div', {
			attrs: {'id': 'contact_create_form'},
			props: {
				className: 'content-form'
			}
		});
		Object.keys(fields).forEach(key => {
			console.log(`Ключ: ${key}, Значение: ${fields[key]}`);
			BX.append(BX.create('div', {
				children: [
					BX.create('span', {'text': fields[key]}),
					BX.create('input', {
						attrs: {name: key}
					}),
				]
			}), resultHtml);
		});

		return resultHtml;
	}
}
