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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/assetbundles/src/js/general.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assetbundles/src/css/general.css":
/*!**********************************************!*\
  !*** ./src/assetbundles/src/css/general.css ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9jc3MvZ2VuZXJhbC5jc3MuanMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9jc3MvZ2VuZXJhbC5jc3M/MDBjMCJdLCJzb3VyY2VzQ29udGVudCI6WyIvLyBleHRyYWN0ZWQgYnkgbWluaS1jc3MtZXh0cmFjdC1wbHVnaW4iXSwibWFwcGluZ3MiOiJBQUFBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./src/assetbundles/src/css/general.css\n");

/***/ }),

/***/ "./src/assetbundles/src/js/general.js":
/*!********************************************!*\
  !*** ./src/assetbundles/src/js/general.js ***!
  \********************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _css_general_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../css/general.css */ \"./src/assetbundles/src/css/general.css\");\n/* harmony import */ var _css_general_css__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_css_general_css__WEBPACK_IMPORTED_MODULE_0__);\n/* global Craft */\n\n/**\n * @todo Get serious and rebuild this with Vue\n */\n\nvar loadCartsBtn = document.getElementById('load-carts');\nvar cartsTable = document.getElementById('carts');\n\nif (loadCartsBtn !== null) {\n  loadCartsBtn.onclick = fetchCarts;\n}\n\nfunction fetchCarts() {\n  Craft.postActionRequest('snipcart/carts/get-next-carts', {\n    continuationToken: loadCartsBtn.getAttribute('data-continuation-token')\n  }, function (response, textStatus) {\n    if (textStatus === 'success' && typeof response.error === 'undefined') {\n      if (response.hasMoreResults) {\n        loadCartsBtn.setAttribute('data-continuation-token', response.continuationToken);\n      } else {\n        loadCartsBtn.classList.add('hidden');\n      }\n\n      var cartsTableBody = cartsTable.querySelector('tbody');\n      response.items.forEach(function (cart) {\n        var row = document.createElement('tr');\n        row.setAttribute('data-id', cart.token);\n        row.setAttribute('data-name', cart.email);\n        var nameColumn = document.createElement('td');\n        nameColumn.innerHTML = \"<a href=\\\"\".concat(cart.cpUrl, \"\\\">\").concat(cart.billingAddress.name, \"</a>\");\n        var emailColumn = document.createElement('td');\n        emailColumn.innerHTML = cart.email;\n        var statusColumn = document.createElement('td');\n        statusColumn.innerHTML = cart.status;\n        var dateColumn = document.createElement('td');\n        dateColumn.innerHTML = cart.modificationDate;\n        var totalColumn = document.createElement('td');\n        totalColumn.innerHTML = cart.total;\n        row.appendChild(nameColumn);\n        row.appendChild(emailColumn);\n        row.appendChild(statusColumn);\n        row.appendChild(dateColumn);\n        row.appendChild(totalColumn);\n        cartsTableBody.appendChild(row);\n      });\n    }\n  });\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9qcy9nZW5lcmFsLmpzLmpzIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vc3JjL2Fzc2V0YnVuZGxlcy9zcmMvanMvZ2VuZXJhbC5qcz83OTMzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qIGdsb2JhbCBDcmFmdCAqL1xuXG5pbXBvcnQgJy4uL2Nzcy9nZW5lcmFsLmNzcyc7XG5cbi8qKlxuICogQHRvZG8gR2V0IHNlcmlvdXMgYW5kIHJlYnVpbGQgdGhpcyB3aXRoIFZ1ZVxuICovXG5jb25zdCBsb2FkQ2FydHNCdG4gPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbG9hZC1jYXJ0cycpO1xuY29uc3QgY2FydHNUYWJsZSA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdjYXJ0cycpO1xuXG5pZiAobG9hZENhcnRzQnRuICE9PSBudWxsKSB7XG4gICAgbG9hZENhcnRzQnRuLm9uY2xpY2sgPSBmZXRjaENhcnRzO1xufVxuXG5mdW5jdGlvbiBmZXRjaENhcnRzKCkge1xuICAgIENyYWZ0LnBvc3RBY3Rpb25SZXF1ZXN0KFxuICAgICAgICAnc25pcGNhcnQvY2FydHMvZ2V0LW5leHQtY2FydHMnLFxuICAgICAgICB7IGNvbnRpbnVhdGlvblRva2VuOiBsb2FkQ2FydHNCdG4uZ2V0QXR0cmlidXRlKCdkYXRhLWNvbnRpbnVhdGlvbi10b2tlbicpIH0sXG4gICAgICAgIGZ1bmN0aW9uKHJlc3BvbnNlLCB0ZXh0U3RhdHVzKSB7XG4gICAgICAgICAgICBpZiAodGV4dFN0YXR1cyA9PT0gJ3N1Y2Nlc3MnICYmIHR5cGVvZiAocmVzcG9uc2UuZXJyb3IpID09PSAndW5kZWZpbmVkJykge1xuICAgICAgICAgICAgICAgIGlmIChyZXNwb25zZS5oYXNNb3JlUmVzdWx0cykge1xuICAgICAgICAgICAgICAgICAgICBsb2FkQ2FydHNCdG4uc2V0QXR0cmlidXRlKCdkYXRhLWNvbnRpbnVhdGlvbi10b2tlbicsIHJlc3BvbnNlLmNvbnRpbnVhdGlvblRva2VuKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBsb2FkQ2FydHNCdG4uY2xhc3NMaXN0LmFkZCgnaGlkZGVuJyk7XG4gICAgICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICAgICAgY29uc3QgY2FydHNUYWJsZUJvZHkgPSBjYXJ0c1RhYmxlLnF1ZXJ5U2VsZWN0b3IoJ3Rib2R5Jyk7XG5cbiAgICAgICAgICAgICAgICByZXNwb25zZS5pdGVtcy5mb3JFYWNoKGZ1bmN0aW9uKGNhcnQpe1xuICAgICAgICAgICAgICAgICAgICBjb25zdCByb3cgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCd0cicpO1xuXG4gICAgICAgICAgICAgICAgICAgIHJvdy5zZXRBdHRyaWJ1dGUoJ2RhdGEtaWQnLCBjYXJ0LnRva2VuKTtcbiAgICAgICAgICAgICAgICAgICAgcm93LnNldEF0dHJpYnV0ZSgnZGF0YS1uYW1lJywgY2FydC5lbWFpbCk7XG5cbiAgICAgICAgICAgICAgICAgICAgY29uc3QgbmFtZUNvbHVtbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3RkJyk7XG4gICAgICAgICAgICAgICAgICAgIG5hbWVDb2x1bW4uaW5uZXJIVE1MID0gYDxhIGhyZWY9XCIke2NhcnQuY3BVcmx9XCI+JHtjYXJ0LmJpbGxpbmdBZGRyZXNzLm5hbWV9PC9hPmA7XG5cbiAgICAgICAgICAgICAgICAgICAgY29uc3QgZW1haWxDb2x1bW4gPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCd0ZCcpO1xuICAgICAgICAgICAgICAgICAgICBlbWFpbENvbHVtbi5pbm5lckhUTUwgPSBjYXJ0LmVtYWlsO1xuXG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IHN0YXR1c0NvbHVtbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ3RkJyk7XG4gICAgICAgICAgICAgICAgICAgIHN0YXR1c0NvbHVtbi5pbm5lckhUTUwgPSBjYXJ0LnN0YXR1cztcblxuICAgICAgICAgICAgICAgICAgICBjb25zdCBkYXRlQ29sdW1uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgndGQnKTtcbiAgICAgICAgICAgICAgICAgICAgZGF0ZUNvbHVtbi5pbm5lckhUTUwgPSBjYXJ0Lm1vZGlmaWNhdGlvbkRhdGU7XG5cbiAgICAgICAgICAgICAgICAgICAgY29uc3QgdG90YWxDb2x1bW4gPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCd0ZCcpO1xuICAgICAgICAgICAgICAgICAgICB0b3RhbENvbHVtbi5pbm5lckhUTUwgPSBjYXJ0LnRvdGFsO1xuXG4gICAgICAgICAgICAgICAgICAgIHJvdy5hcHBlbmRDaGlsZChuYW1lQ29sdW1uKTtcbiAgICAgICAgICAgICAgICAgICAgcm93LmFwcGVuZENoaWxkKGVtYWlsQ29sdW1uKTtcbiAgICAgICAgICAgICAgICAgICAgcm93LmFwcGVuZENoaWxkKHN0YXR1c0NvbHVtbik7XG4gICAgICAgICAgICAgICAgICAgIHJvdy5hcHBlbmRDaGlsZChkYXRlQ29sdW1uKTtcbiAgICAgICAgICAgICAgICAgICAgcm93LmFwcGVuZENoaWxkKHRvdGFsQ29sdW1uKTtcblxuICAgICAgICAgICAgICAgICAgICBjYXJ0c1RhYmxlQm9keS5hcHBlbmRDaGlsZChyb3cpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgKTtcbn1cbiJdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQTtBQUFBO0FBQUE7QUFFQTtBQUVBOzs7O0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFBQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBRUE7QUFDQTtBQUVBO0FBQ0E7QUFFQTtBQUNBO0FBRUE7QUFDQTtBQUVBO0FBQ0E7QUFFQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBRUEiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/assetbundles/src/js/general.js\n");

/***/ })

/******/ });