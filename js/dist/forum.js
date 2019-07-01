module.exports =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./forum.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./forum.js":
/*!******************!*\
  !*** ./forum.js ***!
  \******************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _src_forum__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./src/forum */ "./src/forum/index.js");
/* empty/unused harmony star reexport */

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _inheritsLoose; });
function _inheritsLoose(subClass, superClass) {
  subClass.prototype = Object.create(superClass.prototype);
  subClass.prototype.constructor = subClass;
  subClass.__proto__ = superClass;
}

/***/ }),

/***/ "./src/forum/addSplitControl.js":
/*!**************************************!*\
  !*** ./src/forum/addSplitControl.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var flarum_extend__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! flarum/extend */ "flarum/extend");
/* harmony import */ var flarum_extend__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(flarum_extend__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var flarum_app__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/app */ "flarum/app");
/* harmony import */ var flarum_app__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_app__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flarum_utils_PostControls__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flarum/utils/PostControls */ "flarum/utils/PostControls");
/* harmony import */ var flarum_utils_PostControls__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flarum_utils_PostControls__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var flarum_components_Button__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! flarum/components/Button */ "flarum/components/Button");
/* harmony import */ var flarum_components_Button__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(flarum_components_Button__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var flarum_components_CommentPost__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! flarum/components/CommentPost */ "flarum/components/CommentPost");
/* harmony import */ var flarum_components_CommentPost__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(flarum_components_CommentPost__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _components_SplitPostModal__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/SplitPostModal */ "./src/forum/components/SplitPostModal.js");






/* harmony default export */ __webpack_exports__["default"] = (function (controller) {
  Object(flarum_extend__WEBPACK_IMPORTED_MODULE_0__["extend"])(flarum_utils_PostControls__WEBPACK_IMPORTED_MODULE_2___default.a, 'moderationControls', function (items, post) {
    var discussion = post.discussion();
    if (post.contentType() !== 'comment' || !discussion.canSplit() || post.number() == 1) return;
    items.add('splitFrom', [m(flarum_components_Button__WEBPACK_IMPORTED_MODULE_3___default.a, {
      icon: 'fas fa-code-branch',
      className: 'flagrow-split-startSplitButton',
      onclick: function onclick() {
        controller.start(post.id(), post.number());
      }
    }, flarum_app__WEBPACK_IMPORTED_MODULE_1___default.a.translator.trans('fof-split.forum.split.from'))]);
  });
  Object(flarum_extend__WEBPACK_IMPORTED_MODULE_0__["extend"])(flarum_components_CommentPost__WEBPACK_IMPORTED_MODULE_4___default.a.prototype, 'footerItems', function (items) {
    var post = this.props.post;
    var discussion = post.discussion();
    if (post.contentType() !== 'comment' || !discussion.canSplit() || post.number() == 1) return;
    items.add('splitTo', [m(flarum_components_Button__WEBPACK_IMPORTED_MODULE_3___default.a, {
      icon: 'fas fa-code-branch',
      className: 'flagrow-split-endSplitButton Button Button--link',
      onclick: function onclick() {
        controller.end(post.number());
        var splitModal = new _components_SplitPostModal__WEBPACK_IMPORTED_MODULE_5__["default"]();
        splitModal.setController(controller);
        flarum_app__WEBPACK_IMPORTED_MODULE_1___default.a.modal.show(splitModal);
      },
      style: {
        display: 'none'
      }
    }, flarum_app__WEBPACK_IMPORTED_MODULE_1___default.a.translator.trans('fof-split.forum.split.to'))]);
  });
});

/***/ }),

/***/ "./src/forum/components/DiscussionSplit.js":
/*!*************************************************!*\
  !*** ./src/forum/components/DiscussionSplit.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return DiscussionSplit; });
/* harmony import */ var _babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inheritsLoose */ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js");
/* harmony import */ var flarum_components_EventPost__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/components/EventPost */ "flarum/components/EventPost");
/* harmony import */ var flarum_components_EventPost__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_components_EventPost__WEBPACK_IMPORTED_MODULE_1__);



var DiscussionSplit =
/*#__PURE__*/
function (_EventPost) {
  Object(_babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(DiscussionSplit, _EventPost);

  function DiscussionSplit() {
    return _EventPost.apply(this, arguments) || this;
  }

  var _proto = DiscussionSplit.prototype;

  /**
   * Get the name of the event icon.
   *
   * @return {String}
   */
  _proto.icon = function icon() {
    return 'fas fa-code-branch';
  };
  /**
   * Get the translation key for the description of the event.
   *
   * @return {String}
   */


  _proto.descriptionKey = function descriptionKey() {
    if (this.props.post.content()['toNew']) {
      return 'fof-split.forum.post.was_split_to';
    }

    return 'fof-split.forum.post.was_split_from';
  };
  /**
   * Get the translation data for the description of the event.
   *
   * @return {Object}
   */


  _proto.descriptionData = function descriptionData() {
    return {
      'count': this.props.post.content()['count'],
      'target': m("a", {
        className: "EventPost-Split-target",
        href: this.props.post.content()['url'],
        config: m.route
      }, this.props.post.content()['title'])
    };
  };

  return DiscussionSplit;
}(flarum_components_EventPost__WEBPACK_IMPORTED_MODULE_1___default.a);



/***/ }),

/***/ "./src/forum/components/SplitController.js":
/*!*************************************************!*\
  !*** ./src/forum/components/SplitController.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return SplitController; });
var SplitController =
/*#__PURE__*/
function () {
  function SplitController() {
    this.reset();
  }

  var _proto = SplitController.prototype;

  _proto.start = function start(postId, postNumber) {
    this.reset();
    this.startPostId = postId;
    $('.PostStream-item').each(function () {
      if ($(this).attr('data-number') >= postNumber) {
        $('.flagrow-split-endSplitButton', $(this)).show();
      }
    });
    $('.flagrow-split-startSplitButton').hide();
  };

  _proto.end = function end(postNumber) {
    this.endPostNumber = postNumber;
  };

  _proto.reset = function reset() {
    this.startPostId = null;
    this.endPostNumber = null;
  };

  return SplitController;
}();



/***/ }),

/***/ "./src/forum/components/SplitPostModal.js":
/*!************************************************!*\
  !*** ./src/forum/components/SplitPostModal.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return SplitPostModal; });
/* harmony import */ var _babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/inheritsLoose */ "./node_modules/@babel/runtime/helpers/esm/inheritsLoose.js");
/* harmony import */ var flarum_components_Modal__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/components/Modal */ "flarum/components/Modal");
/* harmony import */ var flarum_components_Modal__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_components_Modal__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var flarum_components_Button__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! flarum/components/Button */ "flarum/components/Button");
/* harmony import */ var flarum_components_Button__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(flarum_components_Button__WEBPACK_IMPORTED_MODULE_2__);




var SplitPostModal =
/*#__PURE__*/
function (_Modal) {
  Object(_babel_runtime_helpers_esm_inheritsLoose__WEBPACK_IMPORTED_MODULE_0__["default"])(SplitPostModal, _Modal);

  function SplitPostModal() {
    return _Modal.apply(this, arguments) || this;
  }

  var _proto = SplitPostModal.prototype;

  _proto.init = function init() {
    _Modal.prototype.init.call(this);

    this.newDiscussionTitle = m.prop('');
  };

  _proto.setController = function setController(controller) {
    this.split = controller;
  };

  _proto.className = function className() {
    return 'SplitPostModal Modal--small';
  };

  _proto.title = function title() {
    return app.translator.trans('fof-split.forum.modal.title');
  };

  _proto.content = function content() {
    return [m('div', {
      className: 'Modal-body'
    }, [m('div', {
      className: 'Form Form--centered'
    }, [m('div', {
      className: 'Form-group'
    }, [m('label', {}, app.translator.trans('fof-split.forum.modal.new_discussion_label')), m('input', {
      className: 'FormControl',
      name: 'new_discussion_title',
      value: this.newDiscussionTitle(),
      oninput: m.withAttr('value', this.newDiscussionTitle)
    })]), m('div', {
      className: 'Form-group'
    }, [m(flarum_components_Button__WEBPACK_IMPORTED_MODULE_2___default.a, {
      className: 'Button Button--primary Button--block',
      type: 'submit',
      loading: this.loading,
      disabled: !this.newDiscussionTitle()
    }, app.translator.trans('fof-split.forum.modal.submit_button'))])])])];
  };

  _proto.onsubmit = function onsubmit(e) {
    var _this = this;

    e.preventDefault();
    this.loading = true;
    var data = new FormData();
    data.append('title', this.newDiscussionTitle());
    data.append('start_post_id', this.split.startPostId);
    data.append('end_post_number', this.split.endPostNumber);
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/split',
      serialize: function serialize(raw) {
        return raw;
      },
      data: data
    }).then(function (data) {
      var discussion = {};
      discussion.id = m.prop(data.data.id);
      discussion.slug = m.prop(data.data.attributes.slug);
      discussion.startUser = m.prop(data.data.attributes.startUser);
      discussion.isUnread = m.prop(data.data.attributes.isUnread);

      _this.hide();

      m.route(app.route.discussion(discussion));
    }, this.loaded.bind(this));
  };

  return SplitPostModal;
}(flarum_components_Modal__WEBPACK_IMPORTED_MODULE_1___default.a);



/***/ }),

/***/ "./src/forum/index.js":
/*!****************************!*\
  !*** ./src/forum/index.js ***!
  \****************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var flarum_extend__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! flarum/extend */ "flarum/extend");
/* harmony import */ var flarum_extend__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(flarum_extend__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var flarum_Model__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! flarum/Model */ "flarum/Model");
/* harmony import */ var flarum_Model__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(flarum_Model__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _addSplitControl__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./addSplitControl */ "./src/forum/addSplitControl.js");
/* harmony import */ var _components_SplitController__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/SplitController */ "./src/forum/components/SplitController.js");
/* harmony import */ var _components_DiscussionSplit__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/DiscussionSplit */ "./src/forum/components/DiscussionSplit.js");





app.initializers.add('flagrow-split', function (app) {
  app.store.models.discussions.prototype.canSplit = flarum_Model__WEBPACK_IMPORTED_MODULE_1___default.a.attribute('canSplit');
  app.postComponents.discussionSplit = _components_DiscussionSplit__WEBPACK_IMPORTED_MODULE_4__["default"];
  var splitController = new _components_SplitController__WEBPACK_IMPORTED_MODULE_3__["default"]();
  Object(_addSplitControl__WEBPACK_IMPORTED_MODULE_2__["default"])(splitController);
});

/***/ }),

/***/ "flarum/Model":
/*!**********************************************!*\
  !*** external "flarum.core.compat['Model']" ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = flarum.core.compat['Model'];

/***/ }),

/***/ "flarum/app":
/*!********************************************!*\
  !*** external "flarum.core.compat['app']" ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = flarum.core.compat['app'];

/***/ }),

/***/ "flarum/components/Button":
/*!**********************************************************!*\
  !*** external "flarum.core.compat['components/Button']" ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = flarum.core.compat['components/Button'];

/***/ }),

/***/ "flarum/components/CommentPost":
/*!***************************************************************!*\
  !*** external "flarum.core.compat['components/CommentPost']" ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = flarum.core.compat['components/CommentPost'];

/***/ }),

/***/ "flarum/components/EventPost":
/*!*************************************************************!*\
  !*** external "flarum.core.compat['components/EventPost']" ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = flarum.core.compat['components/EventPost'];

/***/ }),

/***/ "flarum/components/Modal":
/*!*********************************************************!*\
  !*** external "flarum.core.compat['components/Modal']" ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = flarum.core.compat['components/Modal'];

/***/ }),

/***/ "flarum/extend":
/*!***********************************************!*\
  !*** external "flarum.core.compat['extend']" ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = flarum.core.compat['extend'];

/***/ }),

/***/ "flarum/utils/PostControls":
/*!***********************************************************!*\
  !*** external "flarum.core.compat['utils/PostControls']" ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = flarum.core.compat['utils/PostControls'];

/***/ })

/******/ });
//# sourceMappingURL=forum.js.map
