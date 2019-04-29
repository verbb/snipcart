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
/******/ 		"OrdersWidget": 0
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
/******/ 	deferredModules.push(["./src/assetbundles/src/js/OrdersWidget.js","vendors"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assetbundles/src/js/OrdersWidget.js":
/*!*************************************************!*\
  !*** ./src/assetbundles/src/js/OrdersWidget.js ***!
  \*************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var apexcharts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! apexcharts */ \"./node_modules/apexcharts/dist/apexcharts.esm.js\");\nfunction _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }\n\nfunction _nonIterableSpread() { throw new TypeError(\"Invalid attempt to spread non-iterable instance\"); }\n\nfunction _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === \"[object Arguments]\") return Array.from(iter); }\n\nfunction _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }\n\n/* global Craft */\n\n/* global Garnish */\n\n/* global jQuery */\n\n\n(function ($) {\n  Craft.OrdersWidget = Garnish.Base.extend({\n    settings: null,\n    $widget: null,\n    $chartContainer: null,\n    init: function init(widgetId) {\n      var widgetSelector = '#widget' + widgetId;\n      this.$widget = $(widgetSelector);\n      this.$chartContainer = this.$widget.find('.orders-chart');\n      this.updateChart();\n    },\n    getYMax: function getYMax(series) {\n      var max = 0;\n      series.forEach(function (row) {\n        var data = row.data;\n        var rowMax = Math.max.apply(Math, _toConsumableArray(data));\n\n        if (rowMax > max) {\n          max = rowMax;\n        }\n      }); // round up to nearest 5\n\n      var resolution = 5;\n      max = Math.round((max + resolution / 2) / resolution) * resolution;\n      return max;\n    },\n    updateChart: function updateChart() {\n      this.$chartContainer.addClass('spinner');\n      var self = this;\n      Craft.postActionRequest('snipcart/charts/get-orders-data', {\n        type: this.$chartContainer.data('chart-type'),\n        range: this.$chartContainer.data('chart-range')\n      }, $.proxy(function (response, textStatus) {\n        // TODO: gracefully handle error\n        self.$chartContainer.removeClass('spinner');\n\n        if (textStatus === 'success' && typeof response.error === 'undefined') {\n          var options = {\n            chart: {\n              fontFamily: \"system-ui, BlinkMacSystemFont, -apple-system, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif\",\n              height: 200,\n              type: 'bar',\n              toolbar: {\n                show: false\n              },\n              animations: {\n                speed: 350\n              }\n            },\n            colors: ['#0d78f2'],\n            series: response.series,\n            dataLabels: {\n              enabled: false //offsetY: -12,\n\n            },\n            plotOptions: {\n              bar: {\n                dataLabels: {\n                  position: 'top'\n                }\n              }\n            },\n            xaxis: {\n              categories: response.columns,\n              position: 'bottom',\n              labels: {\n                rotate: 0,\n                offsetY: 1,\n                show: response.columns.length < 15,\n                formatter: function formatter(val) {\n                  if (val === undefined) {\n                    return val;\n                  }\n\n                  var datePieces = val.split('-'); // YYYY-MM-DD\n\n                  var month = parseInt(datePieces[1]);\n                  var day = parseInt(datePieces[2]);\n                  return \"\".concat(month, \"/\").concat(day);\n                }\n              },\n              axisBorder: {\n                show: false\n              },\n              axisTicks: {\n                show: true,\n                height: 3,\n                color: '#e3e5e8'\n              }\n            },\n            yaxis: {\n              min: 0,\n              max: self.getYMax(response.series),\n              tickAmount: 5,\n              forceNiceScale: true,\n              axisBorder: {\n                show: false\n              },\n              axisTicks: {\n                show: false\n              },\n              labels: {\n                show: true,\n                offsetX: -22,\n                style: {\n                  color: '#8f98a3'\n                },\n                formatter: function formatter(val) {\n                  if (response.formats.currencySymbol !== undefined) {\n                    return response.formats.currencySymbol + val;\n                  }\n\n                  return val;\n                }\n              }\n            },\n            tooltip: {\n              enabled: true,\n              x: {\n                show: true\n              },\n              y: {\n                show: false\n              }\n            },\n            grid: {\n              borderColor: '#e3e5e8',\n              strokeDashArray: 1,\n              padding: {\n                left: -10,\n                right: 0,\n                top: 0,\n                bottom: 0\n              }\n            },\n            stroke: {\n              show: true,\n              curve: 'straight',\n              lineCap: 'round'\n            },\n            legend: {\n              show: false\n            }\n          };\n          var chart = new apexcharts__WEBPACK_IMPORTED_MODULE_0__[\"default\"](self.$chartContainer[0], options);\n          chart.render();\n        }\n      }));\n    }\n  });\n})(jQuery);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9qcy9PcmRlcnNXaWRnZXQuanMuanMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9qcy9PcmRlcnNXaWRnZXQuanM/NTM3NCJdLCJzb3VyY2VzQ29udGVudCI6WyIvKiBnbG9iYWwgQ3JhZnQgKi9cbi8qIGdsb2JhbCBHYXJuaXNoICovXG4vKiBnbG9iYWwgalF1ZXJ5ICovXG5cbmltcG9ydCBBcGV4Q2hhcnRzIGZyb20gJ2FwZXhjaGFydHMnXG5cbihmdW5jdGlvbigkKSB7XG4gICAgQ3JhZnQuT3JkZXJzV2lkZ2V0ID0gR2FybmlzaC5CYXNlLmV4dGVuZCh7XG4gICAgICAgIHNldHRpbmdzOiBudWxsLFxuICAgICAgICAkd2lkZ2V0OiBudWxsLFxuICAgICAgICAkY2hhcnRDb250YWluZXI6IG51bGwsXG5cbiAgICAgICAgaW5pdDogZnVuY3Rpb24od2lkZ2V0SWQpIHtcbiAgICAgICAgICAgIGNvbnN0IHdpZGdldFNlbGVjdG9yID0gJyN3aWRnZXQnICsgd2lkZ2V0SWQ7XG4gICAgICAgICAgICB0aGlzLiR3aWRnZXQgPSAkKHdpZGdldFNlbGVjdG9yKTtcbiAgICAgICAgICAgIHRoaXMuJGNoYXJ0Q29udGFpbmVyID0gdGhpcy4kd2lkZ2V0LmZpbmQoJy5vcmRlcnMtY2hhcnQnKTtcbiAgICAgICAgICAgIHRoaXMudXBkYXRlQ2hhcnQoKTtcbiAgICAgICAgfSxcblxuICAgICAgICBnZXRZTWF4OiBmdW5jdGlvbihzZXJpZXMpIHtcbiAgICAgICAgICAgIGxldCBtYXggPSAwO1xuXG4gICAgICAgICAgICBzZXJpZXMuZm9yRWFjaChmdW5jdGlvbihyb3cpIHtcbiAgICAgICAgICAgICAgICBjb25zdCBkYXRhID0gcm93LmRhdGE7XG4gICAgICAgICAgICAgICAgY29uc3Qgcm93TWF4ID0gTWF0aC5tYXgoLi4uZGF0YSlcbiAgICAgICAgICAgICAgICBpZiAocm93TWF4ID4gbWF4KSB7XG4gICAgICAgICAgICAgICAgICAgIG1heCA9IHJvd01heDtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcblxuICAgICAgICAgICAgLy8gcm91bmQgdXAgdG8gbmVhcmVzdCA1XG4gICAgICAgICAgICBjb25zdCByZXNvbHV0aW9uID0gNTtcbiAgICAgICAgICAgIG1heCA9IE1hdGgucm91bmQoKG1heCtyZXNvbHV0aW9uLzIpL3Jlc29sdXRpb24pICogcmVzb2x1dGlvbjtcblxuICAgICAgICAgICAgcmV0dXJuIG1heDtcbiAgICAgICAgfSxcblxuICAgICAgICB1cGRhdGVDaGFydDogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICB0aGlzLiRjaGFydENvbnRhaW5lci5hZGRDbGFzcygnc3Bpbm5lcicpO1xuICAgICAgICAgICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAgICAgICAgIENyYWZ0LnBvc3RBY3Rpb25SZXF1ZXN0KFxuICAgICAgICAgICAgICAgICdzbmlwY2FydC9jaGFydHMvZ2V0LW9yZGVycy1kYXRhJyxcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHR5cGU6IHRoaXMuJGNoYXJ0Q29udGFpbmVyLmRhdGEoJ2NoYXJ0LXR5cGUnKSxcbiAgICAgICAgICAgICAgICAgICAgcmFuZ2U6IHRoaXMuJGNoYXJ0Q29udGFpbmVyLmRhdGEoJ2NoYXJ0LXJhbmdlJyksXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAkLnByb3h5KGZ1bmN0aW9uKHJlc3BvbnNlLCB0ZXh0U3RhdHVzKSB7XG4gICAgICAgICAgICAgICAgICAgIC8vIFRPRE86IGdyYWNlZnVsbHkgaGFuZGxlIGVycm9yXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuJGNoYXJ0Q29udGFpbmVyLnJlbW92ZUNsYXNzKCdzcGlubmVyJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHRleHRTdGF0dXMgPT09ICdzdWNjZXNzJyAmJiB0eXBlb2YgKHJlc3BvbnNlLmVycm9yKSA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IG9wdGlvbnMgPSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY2hhcnQ6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZm9udEZhbWlseTogXCJzeXN0ZW0tdWksIEJsaW5rTWFjU3lzdGVtRm9udCwgLWFwcGxlLXN5c3RlbSwgJ1NlZ29lIFVJJywgJ1JvYm90bycsICdPeHlnZW4nLCAnVWJ1bnR1JywgJ0NhbnRhcmVsbCcsICdGaXJhIFNhbnMnLCAnRHJvaWQgU2FucycsICdIZWx2ZXRpY2EgTmV1ZScsIHNhbnMtc2VyaWZcIixcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaGVpZ2h0OiAyMDAsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU6ICdiYXInLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0b29sYmFyOiB7IHNob3c6IGZhbHNlIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGFuaW1hdGlvbnM6IHsgc3BlZWQ6IDM1MCB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb2xvcnM6IFsnIzBkNzhmMiddLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNlcmllczogcmVzcG9uc2Uuc2VyaWVzLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFMYWJlbHM6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZW5hYmxlZDogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC8vb2Zmc2V0WTogLTEyLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcGxvdE9wdGlvbnM6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYmFyOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBkYXRhTGFiZWxzOiB7IHBvc2l0aW9uOiAndG9wJyB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHhheGlzOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNhdGVnb3JpZXM6IHJlc3BvbnNlLmNvbHVtbnMsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHBvc2l0aW9uOiAnYm90dG9tJyxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGFiZWxzOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByb3RhdGU6IDAsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBvZmZzZXRZOiAxLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2hvdzogcmVzcG9uc2UuY29sdW1ucy5sZW5ndGggPCAxNSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZvcm1hdHRlcjogZnVuY3Rpb24gKHZhbCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmICh2YWwgPT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gdmFsO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBkYXRlUGllY2VzID0gdmFsLnNwbGl0KCctJyk7IC8vIFlZWVktTU0tRERcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBtb250aCA9IHBhcnNlSW50KGRhdGVQaWVjZXNbMV0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IGRheSA9IHBhcnNlSW50KGRhdGVQaWVjZXNbMl0pO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBgJHttb250aH0vJHtkYXl9YDtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYXhpc0JvcmRlcjogeyBzaG93OiBmYWxzZSB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBheGlzVGlja3M6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNob3c6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBoZWlnaHQ6IDMsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb2xvcjogJyNlM2U1ZTgnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgeWF4aXM6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbWluOiAwLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBtYXg6IHNlbGYuZ2V0WU1heChyZXNwb25zZS5zZXJpZXMpLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aWNrQW1vdW50OiA1LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBmb3JjZU5pY2VTY2FsZTogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYXhpc0JvcmRlcjogeyBzaG93OiBmYWxzZSB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBheGlzVGlja3M6IHsgc2hvdzogZmFsc2UgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgbGFiZWxzOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaG93OiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgb2Zmc2V0WDogLTIyLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc3R5bGU6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb2xvcjogJyM4Zjk4YTMnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZvcm1hdHRlcjogZnVuY3Rpb24odmFsKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHJlc3BvbnNlLmZvcm1hdHMuY3VycmVuY3lTeW1ib2wgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gcmVzcG9uc2UuZm9ybWF0cy5jdXJyZW5jeVN5bWJvbCArIHZhbDtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gdmFsO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0b29sdGlwOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVuYWJsZWQ6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHg6IHsgc2hvdzogdHJ1ZSB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB5OiB7IHNob3c6IGZhbHNlLCB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZ3JpZDoge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBib3JkZXJDb2xvcjogJyNlM2U1ZTgnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzdHJva2VEYXNoQXJyYXk6IDEsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHBhZGRpbmc6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxlZnQ6IC0xMCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJpZ2h0OiAwLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgdG9wOiAwLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYm90dG9tOiAwXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzdHJva2U6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2hvdzogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY3VydmU6ICdzdHJhaWdodCcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxpbmVDYXA6ICdyb3VuZCcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBsZWdlbmQ6IHsgc2hvdzogZmFsc2UgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICBcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciBjaGFydCA9IG5ldyBBcGV4Q2hhcnRzKFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNlbGYuJGNoYXJ0Q29udGFpbmVyWzBdLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9wdGlvbnNcbiAgICAgICAgICAgICAgICAgICAgICAgICk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIGNoYXJ0LnJlbmRlcigpO1xuICAgICAgICAgICAgICAgIFxuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfSlcbiAgICAgICAgICAgICk7XG4gICAgICAgIH0sXG4gICAgfSk7XG59KShqUXVlcnkpOyJdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7OztBQUFBO0FBQ0E7QUFBQTtBQUNBO0FBQUE7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBRUE7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUVBO0FBR0E7QUFDQTtBQUZBO0FBS0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFBQTtBQUFBO0FBQ0E7QUFBQTtBQUFBO0FBTEE7QUFPQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRkE7QUFJQTtBQUNBO0FBQ0E7QUFBQTtBQUFBO0FBREE7QUFEQTtBQUtBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUFBO0FBQ0E7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQVpBO0FBY0E7QUFBQTtBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFIQTtBQWxCQTtBQXdCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFBQTtBQUFBO0FBQ0E7QUFBQTtBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQURBO0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFaQTtBQVBBO0FBc0JBO0FBQ0E7QUFDQTtBQUFBO0FBQUE7QUFDQTtBQUFBO0FBQUE7QUFIQTtBQUtBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFKQTtBQUhBO0FBVUE7QUFDQTtBQUNBO0FBQ0E7QUFIQTtBQUtBO0FBQUE7QUFBQTtBQXJGQTtBQXdGQTtBQUtBO0FBRUE7QUFDQTtBQUVBO0FBL0lBO0FBaUpBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./src/assetbundles/src/js/OrdersWidget.js\n");

/***/ })

/******/ });