/* eslint-disable */
this.BX = this.BX || {};
(function (exports,main_core) {
    'use strict';

    var CrmDeal = /*#__PURE__*/function () {
      function CrmDeal() {
        babelHelpers.classCallCheck(this, CrmDeal);
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
          var htmlCode = this.generateHtml();
          console.log(htmlCode);
          var oPopup = new BX.PopupWindow('contact_create', window.body, {
            lightShadow: true,
            closeIcon: true,
            closeByEsc: true,
            overlay: {
              backgroundColor: 'gray',
              opacity: '80'
            },
            titleBar: {
              content: BX.create('div', {
                children: [BX.create('h1', {
                  'text': 'Создание контакта: '
                })]
              })
            },
            content: htmlCode
          });
          //oPopup.setContent(BX('contact_create_form'));

          document.querySelector('#create_contact').addEventListener('click', function () {
            oPopup.show();
          });
          document.querySelector('#save_contact_info').addEventListener('click', function () {
            oPopup.close();
          });
        }
      }, {
        key: "generateHtml",
        value: function generateHtml() {
          var fields = {
            'NAME': 'Имя',
            'LAST_NAME': 'Фамилия',
            'SECOND_NAME': 'Отчество'
          };
          var resultHtml = BX.create('div', {
            attrs: {
              'id': 'contact_create_form'
            },
            props: {
              className: 'content-form'
            }
          });
          Object.keys(fields).forEach(function (key) {
            console.log("\u041A\u043B\u044E\u0447: ".concat(key, ", \u0417\u043D\u0430\u0447\u0435\u043D\u0438\u0435: ").concat(fields[key]));
            BX.append(BX.create('div', {
              children: [BX.create('span', {
                'text': fields[key]
              }), BX.create('input', {
                attrs: {
                  name: key
                }
              })]
            }), resultHtml);
          });
          return resultHtml;
        }
      }]);
      return CrmDeal;
    }();

    exports.CrmDeal = CrmDeal;

}((this.BX.Custom = this.BX.Custom || {}),BX));
//# sourceMappingURL=crm.deal.bundle.js.map
