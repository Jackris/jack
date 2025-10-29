import {Type} from 'main.core';

export class CrmDeal
{
	constructor(options = {name: 'CrmDeal'})
	{
		this.name = options.name;
	}

	setName(name)
	{
		if (Type.isString(name))
		{
			this.name = name;
		}
	}

	getName()
	{
		return this.name;
	}

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
		var oPopup = new BX.PopupWindow('contact_create', window.body, {

			lightShadow: true,
			closeIcon: true,
			closeByEsc: true,
			overlay: {
				backgroundColor: 'gray', opacity: '80'
			}
		});
		oPopup.setContent(BX('contact_create_form'));

		document.querySelector('#create_contact').addEventListener('click', () => {
			oPopup.show();
		});
		document.querySelector('#save_contact_info').addEventListener('click', () => {
			oPopup.close();
		});
	}
}
