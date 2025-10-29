/* eslint-disable */
this.BX = this.BX || {};
this.BX.Test = this.BX.Test || {};
this.BX.Test[''] = this.BX.Test[''] || {};
this.BX.Test[''].Home = this.BX.Test[''].Home || {};
this.BX.Test[''].Home.Bitrix = this.BX.Test[''].Home.Bitrix || {};
this.BX.Test[''].Home.Bitrix.Www = this.BX.Test[''].Home.Bitrix.Www || {};
this.BX.Test[''].Home.Bitrix.Www.Local = this.BX.Test[''].Home.Bitrix.Www.Local || {};
(function (exports,main_core) {
	'use strict';

	var Test = /*#__PURE__*/function () {
	  function Test() {
	    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
	      name: 'Test'
	    };
	    babelHelpers.classCallCheck(this, Test);
	    this.name = options.name;
	  }
	  babelHelpers.createClass(Test, [{
	    key: "setName",
	    value: function setName(name) {
	      if (main_core.Type.isString(name)) {
	        this.name = name;
	      }
	    }
	  }, {
	    key: "getName",
	    value: function getName() {
	      return this.name;
	    }
	  }, {
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
	  return Test;
	}();

	exports.Test = Test;

}((this.BX.Test[''].Home.Bitrix.Www.Local.JS = this.BX.Test[''].Home.Bitrix.Www.Local.JS || {}),BX));
