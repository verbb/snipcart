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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/assetbundles/src/js/field-product-details.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assetbundles/src/css/field-product-details.css":
/*!************************************************************!*\
  !*** ./src/assetbundles/src/css/field-product-details.css ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9jc3MvZmllbGQtcHJvZHVjdC1kZXRhaWxzLmNzcy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL3NyYy9hc3NldGJ1bmRsZXMvc3JjL2Nzcy9maWVsZC1wcm9kdWN0LWRldGFpbHMuY3NzP2Y4ZGMiXSwic291cmNlc0NvbnRlbnQiOlsiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luIl0sIm1hcHBpbmdzIjoiQUFBQSIsInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./src/assetbundles/src/css/field-product-details.css\n");

/***/ }),

/***/ "./src/assetbundles/src/js/field-product-details.js":
/*!**********************************************************!*\
  !*** ./src/assetbundles/src/js/field-product-details.js ***!
  \**********************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _css_field_product_details_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../css/field-product-details.css */ \"./src/assetbundles/src/css/field-product-details.css\");\n/* harmony import */ var _css_field_product_details_css__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_css_field_product_details_css__WEBPACK_IMPORTED_MODULE_0__);\n\nvar shippableSwitch = document.getElementById('fields-shippable');\nvar weightField = document.getElementById('fields-snipcart-weight-field');\nvar dimensionsField = document.getElementById('fields-snipcart-dimensions-field');\n\nif (shippableSwitch) {\n  shippableSwitch.onchange = togglePhysicalFields;\n}\n\nfunction togglePhysicalFields() {\n  if (shippableSwitch.classList.contains('on')) {\n    weightField.classList.remove('hidden');\n    dimensionsField.classList.remove('hidden');\n  } else {\n    weightField.classList.add('hidden');\n    dimensionsField.classList.add('hidden');\n  }\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9qcy9maWVsZC1wcm9kdWN0LWRldGFpbHMuanMuanMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9qcy9maWVsZC1wcm9kdWN0LWRldGFpbHMuanM/OTZjMiJdLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgJy4uL2Nzcy9maWVsZC1wcm9kdWN0LWRldGFpbHMuY3NzJztcblxudmFyIHNoaXBwYWJsZVN3aXRjaCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdmaWVsZHMtc2hpcHBhYmxlJyk7XG52YXIgd2VpZ2h0RmllbGQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZmllbGRzLXNuaXBjYXJ0LXdlaWdodC1maWVsZCcpO1xudmFyIGRpbWVuc2lvbnNGaWVsZCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdmaWVsZHMtc25pcGNhcnQtZGltZW5zaW9ucy1maWVsZCcpO1xuXG5pZiAoc2hpcHBhYmxlU3dpdGNoKSB7XG4gICAgc2hpcHBhYmxlU3dpdGNoLm9uY2hhbmdlID0gdG9nZ2xlUGh5c2ljYWxGaWVsZHM7XG59XG5cbmZ1bmN0aW9uIHRvZ2dsZVBoeXNpY2FsRmllbGRzKCkge1xuICAgIGlmIChzaGlwcGFibGVTd2l0Y2guY2xhc3NMaXN0LmNvbnRhaW5zKCdvbicpKSB7XG4gICAgICAgIHdlaWdodEZpZWxkLmNsYXNzTGlzdC5yZW1vdmUoJ2hpZGRlbicpO1xuICAgICAgICBkaW1lbnNpb25zRmllbGQuY2xhc3NMaXN0LnJlbW92ZSgnaGlkZGVuJyk7XG4gICAgfSBlbHNlIHtcbiAgICAgICAgd2VpZ2h0RmllbGQuY2xhc3NMaXN0LmFkZCgnaGlkZGVuJyk7XG4gICAgICAgIGRpbWVuc2lvbnNGaWVsZC5jbGFzc0xpc3QuYWRkKCdoaWRkZW4nKTtcbiAgICB9XG59Il0sIm1hcHBpbmdzIjoiQUFBQTtBQUFBO0FBQUE7QUFBQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/assetbundles/src/js/field-product-details.js\n");

/***/ })

/******/ });