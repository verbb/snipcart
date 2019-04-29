/******/ (function(modules) { // webpackBootstrap
/******/ 	// install a JSONP callback for chunk loading
/******/ 	function webpackJsonpCallback(data) {
/******/ 		var chunkIds = data[0];
/******/ 		var moreModules = data[1];
/******/ 		var executeModules = data[2];
/******/
/******/ 		// add "moreModules" to the modules object,
/******/ 		// then flag all "chunkIds" as loaded and fire callback
/******/ 		var moduleId, chunkId, i = 0, resolves = [];
/******/ 		for(;i < chunkIds.length; i++) {
/******/ 			chunkId = chunkIds[i];
/******/ 			if(installedChunks[chunkId]) {
/******/ 				resolves.push(installedChunks[chunkId][0]);
/******/ 			}
/******/ 			installedChunks[chunkId] = 0;
/******/ 		}
/******/ 		for(moduleId in moreModules) {
/******/ 			if(Object.prototype.hasOwnProperty.call(moreModules, moduleId)) {
/******/ 				modules[moduleId] = moreModules[moduleId];
/******/ 			}
/******/ 		}
/******/ 		if(parentJsonpFunction) parentJsonpFunction(data);
/******/
/******/ 		while(resolves.length) {
/******/ 			resolves.shift()();
/******/ 		}
/******/
/******/ 		// add entry modules from loaded chunk to deferred list
/******/ 		deferredModules.push.apply(deferredModules, executeModules || []);
/******/
/******/ 		// run deferred modules when all chunks ready
/******/ 		return checkDeferredModules();
/******/ 	};
/******/ 	function checkDeferredModules() {
/******/ 		var result;
/******/ 		for(var i = 0; i < deferredModules.length; i++) {
/******/ 			var deferredModule = deferredModules[i];
/******/ 			var fulfilled = true;
/******/ 			for(var j = 1; j < deferredModule.length; j++) {
/******/ 				var depId = deferredModule[j];
/******/ 				if(installedChunks[depId] !== 0) fulfilled = false;
/******/ 			}
/******/ 			if(fulfilled) {
/******/ 				deferredModules.splice(i--, 1);
/******/ 				result = __webpack_require__(__webpack_require__.s = deferredModule[0]);
/******/ 			}
/******/ 		}
/******/ 		return result;
/******/ 	}
/******/
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// object to store loaded and loading chunks
/******/ 	// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 	// Promise = chunk loading, 0 = chunk loaded
/******/ 	var installedChunks = {
/******/ 		"settings-plugin": 0
/******/ 	};
/******/
/******/ 	var deferredModules = [];
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
/******/ 	var jsonpArray = window["webpackJsonp"] = window["webpackJsonp"] || [];
/******/ 	var oldJsonpFunction = jsonpArray.push.bind(jsonpArray);
/******/ 	jsonpArray.push = webpackJsonpCallback;
/******/ 	jsonpArray = jsonpArray.slice();
/******/ 	for(var i = 0; i < jsonpArray.length; i++) webpackJsonpCallback(jsonpArray[i]);
/******/ 	var parentJsonpFunction = oldJsonpFunction;
/******/
/******/
/******/ 	// add entry module to deferred list
/******/ 	deferredModules.push(["./src/assetbundles/src/js/settings-plugin.js","vendors"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assetbundles/src/css/settings-plugin.css":
/*!******************************************************!*\
  !*** ./src/assetbundles/src/css/settings-plugin.css ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9jc3Mvc2V0dGluZ3MtcGx1Z2luLmNzcy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL3NyYy9hc3NldGJ1bmRsZXMvc3JjL2Nzcy9zZXR0aW5ncy1wbHVnaW4uY3NzPzdjNjciXSwic291cmNlc0NvbnRlbnQiOlsiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luIl0sIm1hcHBpbmdzIjoiQUFBQSIsInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./src/assetbundles/src/css/settings-plugin.css\n");

/***/ }),

/***/ "./src/assetbundles/src/js/settings-plugin.js":
/*!****************************************************!*\
  !*** ./src/assetbundles/src/js/settings-plugin.js ***!
  \****************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _css_settings_plugin_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../css/settings-plugin.css */ \"./src/assetbundles/src/css/settings-plugin.css\");\n/* harmony import */ var _css_settings_plugin_css__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_css_settings_plugin_css__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var clipboard__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! clipboard */ \"./node_modules/clipboard/dist/clipboard.js\");\n/* harmony import */ var clipboard__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(clipboard__WEBPACK_IMPORTED_MODULE_1__);\n\n\nvar inputField = document.getElementById('webhookEndpoint');\nvar inputContainer = inputField.parentElement;\nvar copyButton = document.createElement('a');\ncopyButton.className = 'copy-btn';\ncopyButton.setAttribute('data-clipboard-target', '#webhookEndpoint');\ninputContainer.appendChild(copyButton);\nvar clipboard = new clipboard__WEBPACK_IMPORTED_MODULE_1___default.a('.copy-btn');\nclipboard.on('success', function (e) {\n  e.trigger.classList.add('success');\n  setTimeout(function () {\n    e.trigger.classList.remove('success');\n  }, 3000);\n  e.clearSelection();\n});\nvar shipStationSettingsDeleteBtn = document.getElementById('shipstation-settings-delete');\nvar shipStationSettings = document.getElementById('shipstation-provider-settings');\nvar shipStationEnabledField = document.getElementById('shipstation-panel-enabled');\nvar shipStationAddBtn = document.getElementById('shipstation-add-btn');\nvar shipFromFields = document.getElementById('ship-from-fields');\nshipStationSettingsDeleteBtn.onclick = removeShipStationSettings;\nshipStationAddBtn.onclick = addShipStationSettings;\n\nfunction removeShipStationSettings() {\n  shipStationSettings.classList.add('hidden');\n  shipStationAddBtn.classList.remove('hidden');\n  resetShipStationFieldValues();\n  shipFromFields.classList.add('hidden');\n  shipStationEnabledField.setAttribute('value', 0);\n}\n\nfunction addShipStationSettings() {\n  shipStationSettings.classList.remove('hidden');\n  shipStationAddBtn.classList.add('hidden');\n  shipFromFields.classList.remove('hidden');\n  shipStationEnabledField.setAttribute('value', 1);\n}\n\nfunction resetShipStationFieldValues() {\n  var textFields = shipStationSettings.querySelectorAll('input[type=text]');\n  var lightswitchFields = shipStationSettings.querySelectorAll('.lightswitch');\n  textFields.forEach(function (field) {\n    field.setAttribute('value', '');\n  });\n  lightswitchFields.forEach(function (field) {\n    var input = field.querySelector('input[type=hidden]');\n    input.removeAttribute('value');\n    field.classList.remove('on');\n  });\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9qcy9zZXR0aW5ncy1wbHVnaW4uanMuanMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9qcy9zZXR0aW5ncy1wbHVnaW4uanM/NjMzYiJdLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgJy4uL2Nzcy9zZXR0aW5ncy1wbHVnaW4uY3NzJztcbmltcG9ydCBDbGlwYm9hcmRKUyBmcm9tICdjbGlwYm9hcmQnO1xuXG5jb25zdCBpbnB1dEZpZWxkID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3dlYmhvb2tFbmRwb2ludCcpO1xuY29uc3QgaW5wdXRDb250YWluZXIgPSBpbnB1dEZpZWxkLnBhcmVudEVsZW1lbnQ7XG5jb25zdCBjb3B5QnV0dG9uID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYScpO1xuXG5jb3B5QnV0dG9uLmNsYXNzTmFtZSA9ICdjb3B5LWJ0bic7XG5jb3B5QnV0dG9uLnNldEF0dHJpYnV0ZSgnZGF0YS1jbGlwYm9hcmQtdGFyZ2V0JywgJyN3ZWJob29rRW5kcG9pbnQnKTtcblxuaW5wdXRDb250YWluZXIuYXBwZW5kQ2hpbGQoY29weUJ1dHRvbik7XG5cbmNvbnN0IGNsaXBib2FyZCA9IG5ldyBDbGlwYm9hcmRKUygnLmNvcHktYnRuJyk7XG5cbmNsaXBib2FyZC5vbignc3VjY2VzcycsIGZ1bmN0aW9uKGUpIHtcbiAgICBlLnRyaWdnZXIuY2xhc3NMaXN0LmFkZCgnc3VjY2VzcycpO1xuICAgIHNldFRpbWVvdXQoZnVuY3Rpb24oKXtcbiAgICAgICAgZS50cmlnZ2VyLmNsYXNzTGlzdC5yZW1vdmUoJ3N1Y2Nlc3MnKTtcbiAgICB9LCAzMDAwKTtcbiAgICBlLmNsZWFyU2VsZWN0aW9uKCk7XG59KTtcblxuY29uc3Qgc2hpcFN0YXRpb25TZXR0aW5nc0RlbGV0ZUJ0biA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzaGlwc3RhdGlvbi1zZXR0aW5ncy1kZWxldGUnKTtcbmNvbnN0IHNoaXBTdGF0aW9uU2V0dGluZ3MgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2hpcHN0YXRpb24tcHJvdmlkZXItc2V0dGluZ3MnKTtcbmNvbnN0IHNoaXBTdGF0aW9uRW5hYmxlZEZpZWxkID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NoaXBzdGF0aW9uLXBhbmVsLWVuYWJsZWQnKTtcbmNvbnN0IHNoaXBTdGF0aW9uQWRkQnRuID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NoaXBzdGF0aW9uLWFkZC1idG4nKTtcbmNvbnN0IHNoaXBGcm9tRmllbGRzID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NoaXAtZnJvbS1maWVsZHMnKTtcblxuc2hpcFN0YXRpb25TZXR0aW5nc0RlbGV0ZUJ0bi5vbmNsaWNrID0gcmVtb3ZlU2hpcFN0YXRpb25TZXR0aW5ncztcbnNoaXBTdGF0aW9uQWRkQnRuLm9uY2xpY2sgPSBhZGRTaGlwU3RhdGlvblNldHRpbmdzO1xuXG5mdW5jdGlvbiByZW1vdmVTaGlwU3RhdGlvblNldHRpbmdzKCkge1xuICAgIHNoaXBTdGF0aW9uU2V0dGluZ3MuY2xhc3NMaXN0LmFkZCgnaGlkZGVuJyk7XG4gICAgc2hpcFN0YXRpb25BZGRCdG4uY2xhc3NMaXN0LnJlbW92ZSgnaGlkZGVuJyk7XG5cbiAgICByZXNldFNoaXBTdGF0aW9uRmllbGRWYWx1ZXMoKTtcblxuICAgIHNoaXBGcm9tRmllbGRzLmNsYXNzTGlzdC5hZGQoJ2hpZGRlbicpO1xuICAgIHNoaXBTdGF0aW9uRW5hYmxlZEZpZWxkLnNldEF0dHJpYnV0ZSgndmFsdWUnLCAwKTtcbn1cblxuZnVuY3Rpb24gYWRkU2hpcFN0YXRpb25TZXR0aW5ncygpIHtcbiAgICBzaGlwU3RhdGlvblNldHRpbmdzLmNsYXNzTGlzdC5yZW1vdmUoJ2hpZGRlbicpO1xuICAgIHNoaXBTdGF0aW9uQWRkQnRuLmNsYXNzTGlzdC5hZGQoJ2hpZGRlbicpO1xuICAgIHNoaXBGcm9tRmllbGRzLmNsYXNzTGlzdC5yZW1vdmUoJ2hpZGRlbicpO1xuICAgIHNoaXBTdGF0aW9uRW5hYmxlZEZpZWxkLnNldEF0dHJpYnV0ZSgndmFsdWUnLCAxKTtcbn1cblxuZnVuY3Rpb24gcmVzZXRTaGlwU3RhdGlvbkZpZWxkVmFsdWVzKCkge1xuICAgIGNvbnN0IHRleHRGaWVsZHMgPSBzaGlwU3RhdGlvblNldHRpbmdzLnF1ZXJ5U2VsZWN0b3JBbGwoJ2lucHV0W3R5cGU9dGV4dF0nKTtcbiAgICBjb25zdCBsaWdodHN3aXRjaEZpZWxkcyA9IHNoaXBTdGF0aW9uU2V0dGluZ3MucXVlcnlTZWxlY3RvckFsbCgnLmxpZ2h0c3dpdGNoJyk7XG5cbiAgICB0ZXh0RmllbGRzLmZvckVhY2goZnVuY3Rpb24oZmllbGQpe1xuICAgICAgICBmaWVsZC5zZXRBdHRyaWJ1dGUoJ3ZhbHVlJywgJycpO1xuICAgIH0pO1xuXG4gICAgbGlnaHRzd2l0Y2hGaWVsZHMuZm9yRWFjaChmdW5jdGlvbihmaWVsZCl7XG4gICAgICAgIGNvbnN0IGlucHV0ID0gZmllbGQucXVlcnlTZWxlY3RvcignaW5wdXRbdHlwZT1oaWRkZW5dJyk7XG4gICAgICAgIGlucHV0LnJlbW92ZUF0dHJpYnV0ZSgndmFsdWUnKTtcblxuICAgICAgICBmaWVsZC5jbGFzc0xpc3QucmVtb3ZlKCdvbicpO1xuICAgIH0pO1xufVxuIl0sIm1hcHBpbmdzIjoiQUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFFQTtBQUVBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./src/assetbundles/src/js/settings-plugin.js\n");

/***/ })

/******/ });