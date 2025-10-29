/* eslint-disable */
this.BX = this.BX || {};
(function (exports,main_core) {
	'use strict';

	var MrmDeal = /*#__PURE__*/function () {
	  function MrmDeal() {
	    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {
	      name: 'MrmDeal'
	    };
	    babelHelpers.classCallCheck(this, MrmDeal);
	    this.name = options.name;
	  }
	  babelHelpers.createClass(MrmDeal, [{
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
	  return MrmDeal;
	}();

	exports.MrmDeal = MrmDeal;

}((this.BX.Mrm = this.BX.Mrm || {}),BX));
