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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/assetbundles/src/js/settings-discount.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assetbundles/src/js/settings-discount.js":
/*!******************************************************!*\
  !*** ./src/assetbundles/src/js/settings-discount.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var triggerInput = document.getElementById('trigger');\nvar codeField = document.getElementById('code-field');\nvar itemIdField = document.getElementById('itemId-field');\nvar totalToReachField = document.getElementById('totalToReach-field');\ntriggerInput.onchange = updateTriggerOptions;\n\nfunction updateTriggerOptions() {\n  if (triggerInput.value === 'Total') {\n    codeField.style.display = 'none';\n    itemIdField.style.display = 'none';\n    totalToReachField.style.display = 'block';\n  } else if (triggerInput.value === 'Code') {\n    codeField.style.display = 'block';\n    itemIdField.style.display = 'none';\n    totalToReachField.style.display = 'none';\n  } else if (triggerInput.value === 'Product') {\n    codeField.style.display = 'none';\n    itemIdField.style.display = 'block';\n    totalToReachField.style.display = 'none';\n  }\n}\n\nupdateTriggerOptions();\nvar typeInput = document.getElementById('type');\nvar amountField = document.getElementById('amount-field');\nvar productIdsField = document.getElementById('productIds-field');\nvar rateField = document.getElementById('rate-field');\nvar alternatePriceField = document.getElementById('alternatePrice-field');\nvar shippingDescriptionField = document.getElementById('shippingDescription-field');\nvar shippingCostField = document.getElementById('shippingCost-field');\nvar shippingGuaranteedDaysToDeliveryField = document.getElementById('shippingGuaranteedDaysToDelivery-field');\ntypeInput.onchange = updateTypeOptions;\n\nfunction updateTypeOptions() {\n  if (typeInput.value === 'FixedAmount') {\n    amountField.style.display = 'block';\n    productIdsField.style.display = 'none';\n    rateField.style.display = 'none';\n    alternatePriceField.style.display = 'none';\n    shippingDescriptionField.style.display = 'none';\n    shippingCostField.style.display = 'none';\n    shippingGuaranteedDaysToDeliveryField.style.display = 'none';\n  } else if (typeInput.value === 'FixedAmountOnItems') {\n    amountField.style.display = 'none';\n    productIdsField.style.display = 'block';\n    rateField.style.display = 'none';\n    alternatePriceField.style.display = 'none';\n    shippingDescriptionField.style.display = 'none';\n    shippingCostField.style.display = 'none';\n    shippingGuaranteedDaysToDeliveryField.style.display = 'none';\n  } else if (typeInput.value === 'Rate') {\n    amountField.style.display = 'none';\n    productIdsField.style.display = 'none';\n    rateField.style.display = 'block';\n    alternatePriceField.style.display = 'none';\n    shippingDescriptionField.style.display = 'none';\n    shippingCostField.style.display = 'none';\n    shippingGuaranteedDaysToDeliveryField.style.display = 'none';\n  } else if (typeInput.value === 'AlternatePrice') {\n    amountField.style.display = 'none';\n    productIdsField.style.display = 'none';\n    rateField.style.display = 'none';\n    alternatePriceField.style.display = 'block';\n    shippingDescriptionField.style.display = 'none';\n    shippingCostField.style.display = 'none';\n    shippingGuaranteedDaysToDeliveryField.style.display = 'none';\n  } else if (typeInput.value === 'Shipping') {\n    amountField.style.display = 'none';\n    productIdsField.style.display = 'none';\n    rateField.style.display = 'none';\n    alternatePriceField.style.display = 'none';\n    shippingDescriptionField.style.display = 'block';\n    shippingCostField.style.display = 'block';\n    shippingGuaranteedDaysToDeliveryField.style.display = 'block';\n  }\n}\n\nupdateTypeOptions();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9qcy9zZXR0aW5ncy1kaXNjb3VudC5qcy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL3NyYy9hc3NldGJ1bmRsZXMvc3JjL2pzL3NldHRpbmdzLWRpc2NvdW50LmpzPzYyMTYiXSwic291cmNlc0NvbnRlbnQiOlsidmFyIHRyaWdnZXJJbnB1dCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCd0cmlnZ2VyJyk7XG52YXIgY29kZUZpZWxkID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2NvZGUtZmllbGQnKTtcbnZhciBpdGVtSWRGaWVsZCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdpdGVtSWQtZmllbGQnKTtcbnZhciB0b3RhbFRvUmVhY2hGaWVsZCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCd0b3RhbFRvUmVhY2gtZmllbGQnKTtcblxudHJpZ2dlcklucHV0Lm9uY2hhbmdlID0gdXBkYXRlVHJpZ2dlck9wdGlvbnM7XG5cbmZ1bmN0aW9uIHVwZGF0ZVRyaWdnZXJPcHRpb25zKCkge1xuICAgIGlmICh0cmlnZ2VySW5wdXQudmFsdWUgPT09ICdUb3RhbCcpIHtcbiAgICAgICAgY29kZUZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgIGl0ZW1JZEZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgIHRvdGFsVG9SZWFjaEZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuICAgIH0gZWxzZSBpZiAodHJpZ2dlcklucHV0LnZhbHVlID09PSAnQ29kZScpIHtcbiAgICAgICAgY29kZUZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuICAgICAgICBpdGVtSWRGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICB0b3RhbFRvUmVhY2hGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgIH0gZWxzZSBpZiAodHJpZ2dlcklucHV0LnZhbHVlID09PSAnUHJvZHVjdCcpIHtcbiAgICAgICAgY29kZUZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgIGl0ZW1JZEZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuICAgICAgICB0b3RhbFRvUmVhY2hGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgIH1cbn1cblxudXBkYXRlVHJpZ2dlck9wdGlvbnMoKTtcblxudmFyIHR5cGVJbnB1dCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCd0eXBlJyk7XG52YXIgYW1vdW50RmllbGQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnYW1vdW50LWZpZWxkJyk7XG52YXIgcHJvZHVjdElkc0ZpZWxkID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3Byb2R1Y3RJZHMtZmllbGQnKTtcbnZhciByYXRlRmllbGQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgncmF0ZS1maWVsZCcpO1xudmFyIGFsdGVybmF0ZVByaWNlRmllbGQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnYWx0ZXJuYXRlUHJpY2UtZmllbGQnKTtcbnZhciBzaGlwcGluZ0Rlc2NyaXB0aW9uRmllbGQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2hpcHBpbmdEZXNjcmlwdGlvbi1maWVsZCcpO1xudmFyIHNoaXBwaW5nQ29zdEZpZWxkID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NoaXBwaW5nQ29zdC1maWVsZCcpO1xudmFyIHNoaXBwaW5nR3VhcmFudGVlZERheXNUb0RlbGl2ZXJ5RmllbGQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2hpcHBpbmdHdWFyYW50ZWVkRGF5c1RvRGVsaXZlcnktZmllbGQnKTtcblxudHlwZUlucHV0Lm9uY2hhbmdlID0gdXBkYXRlVHlwZU9wdGlvbnM7XG5cbmZ1bmN0aW9uIHVwZGF0ZVR5cGVPcHRpb25zKCkge1xuICAgIGlmICh0eXBlSW5wdXQudmFsdWUgPT09ICdGaXhlZEFtb3VudCcpIHtcbiAgICAgICAgYW1vdW50RmllbGQuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG4gICAgICAgIHByb2R1Y3RJZHNGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICByYXRlRmllbGQuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgYWx0ZXJuYXRlUHJpY2VGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICBzaGlwcGluZ0Rlc2NyaXB0aW9uRmllbGQuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgc2hpcHBpbmdDb3N0RmllbGQuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgc2hpcHBpbmdHdWFyYW50ZWVkRGF5c1RvRGVsaXZlcnlGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgIH0gZWxzZSBpZiAodHlwZUlucHV0LnZhbHVlID09PSAnRml4ZWRBbW91bnRPbkl0ZW1zJykge1xuICAgICAgICBhbW91bnRGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICBwcm9kdWN0SWRzRmllbGQuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG4gICAgICAgIHJhdGVGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICBhbHRlcm5hdGVQcmljZUZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgIHNoaXBwaW5nRGVzY3JpcHRpb25GaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICBzaGlwcGluZ0Nvc3RGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICBzaGlwcGluZ0d1YXJhbnRlZWREYXlzVG9EZWxpdmVyeUZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgfSBlbHNlIGlmICh0eXBlSW5wdXQudmFsdWUgPT09ICdSYXRlJykge1xuICAgICAgICBhbW91bnRGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICBwcm9kdWN0SWRzRmllbGQuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgcmF0ZUZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuICAgICAgICBhbHRlcm5hdGVQcmljZUZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgIHNoaXBwaW5nRGVzY3JpcHRpb25GaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICBzaGlwcGluZ0Nvc3RGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICBzaGlwcGluZ0d1YXJhbnRlZWREYXlzVG9EZWxpdmVyeUZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgfSBlbHNlIGlmICh0eXBlSW5wdXQudmFsdWUgPT09ICdBbHRlcm5hdGVQcmljZScpIHtcbiAgICAgICAgYW1vdW50RmllbGQuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgcHJvZHVjdElkc0ZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgIHJhdGVGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICBhbHRlcm5hdGVQcmljZUZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuICAgICAgICBzaGlwcGluZ0Rlc2NyaXB0aW9uRmllbGQuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgc2hpcHBpbmdDb3N0RmllbGQuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgc2hpcHBpbmdHdWFyYW50ZWVkRGF5c1RvRGVsaXZlcnlGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgIH0gZWxzZSBpZiAodHlwZUlucHV0LnZhbHVlID09PSAnU2hpcHBpbmcnKSB7XG4gICAgICAgIGFtb3VudEZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnbm9uZSc7XG4gICAgICAgIHByb2R1Y3RJZHNGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICByYXRlRmllbGQuc3R5bGUuZGlzcGxheSA9ICdub25lJztcbiAgICAgICAgYWx0ZXJuYXRlUHJpY2VGaWVsZC5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnO1xuICAgICAgICBzaGlwcGluZ0Rlc2NyaXB0aW9uRmllbGQuc3R5bGUuZGlzcGxheSA9ICdibG9jayc7XG4gICAgICAgIHNoaXBwaW5nQ29zdEZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuICAgICAgICBzaGlwcGluZ0d1YXJhbnRlZWREYXlzVG9EZWxpdmVyeUZpZWxkLnN0eWxlLmRpc3BsYXkgPSAnYmxvY2snO1xuICAgIH1cbn1cblxudXBkYXRlVHlwZU9wdGlvbnMoKTtcblxuIl0sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/assetbundles/src/js/settings-discount.js\n");

/***/ })

/******/ });