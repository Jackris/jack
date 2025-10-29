/* eslint-disable */
this.BX = this.BX || {};
(function (exports,main_core) {
	'use strict';

	var Deal = /*#__PURE__*/function () {
	  function Deal() {
	    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
	      name: 'Deal'
	    };
	    babelHelpers.classCallCheck(this, Deal);
	    this.name = options.name;
	  }
	  babelHelpers.createClass(Deal, [{
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
	  }]);
	  return Deal;
	}();

	exports.Deal = Deal;

}((this.BX.Custom = this.BX.Custom || {}),BX));
