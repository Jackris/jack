/* eslint-disable */
this.BX = this.BX || {};
(function (exports,main_core) {
	'use strict';

	var CrmDeal = /*#__PURE__*/function () {
	  function CrmDeal() {
	    babelHelpers.classCallCheck(this, CrmDeal);
	    babelHelpers.defineProperty(this, "nun", 123);
	    babelHelpers.defineProperty(this, "str", 'hello');
	  }
	  babelHelpers.createClass(CrmDeal, [{
	    key: "createContactDialog",
	    value: function createContactDialog() {
	      //ПОИСК КОНТАКТОВ
	      var field = document.getElementById('contact_search');
	      console.log(field);
	      var dialog = new BX.UI.EntitySelector.Dialog({
	        targetNode: field,
	        context: 'MY_PAGE_CONTEXT',
	        enableSearch: true,
	        searchOptions: {
	          allowCreateItem: false
	        },
	        multiple: false,
	        entities: [{
	          id: 'contact',
	          dynamicLoad: true,
	          dynamicSearch: true
	        }],
	        events: {
	          'Item:onSelect': function ItemOnSelect(event) {
	            var selectedItem = event.getData().item;
	            console.log(selectedItem);
	            field.value = selectedItem.getTitle();
	          }
	        }
	      });
	      field.addEventListener('click', function () {
	        dialog.show();
	      });

	      //МОДАЛКА
	      var oPopup = new BX.PopupWindow('contact_create', window.body, {
	        lightShadow: true,
	        closeIcon: true,
	        closeByEsc: true,
	        overlay: {
	          backgroundColor: 'gray',
	          opacity: '80'
	        }
	      });
	      oPopup.setContent(BX('contact_create_form'));
	      document.querySelector('#create_contact').addEventListener('click', function () {
	        oPopup.show();
	      });
	      document.querySelector('#save_contact_info').addEventListener('click', function () {
	        oPopup.close();
	      });
	    }
	  }]);
	  return CrmDeal;
	}();

	exports.CrmDeal = CrmDeal;

}((this.BX.CrmDeal = this.BX.CrmDeal || {}),BX));
//# sourceMappingURL=crm.deal.bundle.js.map
