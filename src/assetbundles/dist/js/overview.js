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
/******/ 		"overview": 0
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
/******/ 	deferredModules.push(["./src/assetbundles/src/js/overview.js","vendors"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assetbundles/src/js/overview.js":
/*!*********************************************!*\
  !*** ./src/assetbundles/src/js/overview.js ***!
  \*********************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var apexcharts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! apexcharts */ \"./node_modules/apexcharts/dist/apexcharts.esm.js\");\n/* global Craft */\n\nvar statPanels = document.getElementById('stat-panels');\nvar updateStatsBtn = document.getElementById('update-stats-button');\nvar startDateField = document.querySelector('input[name=\"startDate[date]\"]');\nvar endDateField = document.querySelector('input[name=\"endDate[date]\"]');\nvar chartContainer = document.getElementById('overview-chart');\nvar chart;\n\nif (statPanels) {\n  fetchStatPanels();\n  updateChart();\n\n  updateStatsBtn.onclick = function (e) {\n    e.preventDefault();\n    fetchStatPanels();\n    updateChart();\n  };\n}\n\nfunction fetchStatPanels() {\n  var ordersCount = document.getElementById('stat-ordersCount');\n  var ordersSales = document.getElementById('stat-ordersSales');\n  var averageOrdersValue = document.getElementById('stat-averageOrdersValue');\n  var newCustomers = document.getElementById('stat-newCustomers');\n  var returningCustomers = document.getElementById('stat-returningCustomers');\n  var averageCustomerValue = document.getElementById('stat-averageCustomerValue');\n  var spinnerMarkup = '<div class=\"spinner\"></div>';\n  ordersCount.innerHTML = spinnerMarkup;\n  ordersSales.innerHTML = spinnerMarkup;\n  averageOrdersValue.innerHTML = spinnerMarkup;\n  newCustomers.innerHTML = spinnerMarkup;\n  returningCustomers.innerHTML = spinnerMarkup;\n  averageCustomerValue.innerHTML = spinnerMarkup;\n  Craft.postActionRequest('snipcart/overview/get-stats', {\n    startDate: startDateField.value,\n    endDate: endDateField.value\n  }, function (response, textStatus) {\n    if (textStatus === 'success' && typeof response.error === 'undefined') {\n      ordersCount.innerHTML = response.stats.ordersCount;\n      ordersSales.innerHTML = response.stats.ordersSales;\n      averageOrdersValue.innerHTML = response.stats.averageOrdersValue;\n      newCustomers.innerHTML = response.stats.customers.newCustomers;\n      returningCustomers.innerHTML = response.stats.customers.returningCustomers;\n      averageCustomerValue.innerHTML = response.stats.averageCustomerValue;\n    }\n  });\n}\n\nArray.prototype.max = function () {\n  return Math.max.apply(null, this);\n};\n\nfunction updateChart() {\n  chartContainer.classList.add('spinner');\n  Craft.postActionRequest('snipcart/charts/get-combined-data', {\n    startDate: startDateField.value,\n    endDate: endDateField.value\n  }, function (response, textStatus) {\n    // TODO: gracefully handle error\n    chartContainer.classList.remove('spinner');\n\n    if (textStatus === 'success' && typeof response.error === 'undefined') {\n      var maxOrders = response.series[0].data.max();\n      var maxSales = response.series[1].data.max();\n      var options = {\n        chart: {\n          fontFamily: \"system-ui, BlinkMacSystemFont, -apple-system, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif\",\n          height: 220,\n          type: 'line',\n          toolbar: {\n            show: true\n          },\n          animations: {\n            speed: 350,\n            easing: 'easeout',\n            animateGradually: {\n              enabled: false\n            }\n          }\n        },\n        colors: ['#8f98a3', '#0d78f2'],\n        dataLabels: {\n          enabled: false\n        },\n        markers: {\n          size: 2,\n          hover: {\n            size: 4\n          },\n          strokeWidth: 0,\n          fillOpacity: 0.5\n        },\n        fill: {\n          type: ['solid', 'gradient'],\n          colors: ['#8f98a3', '#0d78f2'],\n          opacity: 1,\n          gradient: {\n            type: \"vertical\",\n            shadeIntensity: 0.3,\n            opacityFrom: 0.5,\n            opacityTo: 0,\n            stops: [0, 90],\n            colorStops: []\n          }\n        },\n        series: response.series,\n        xaxis: {\n          categories: response.columns,\n          labels: {\n            show: false,\n            formatter: function formatter(val) {\n              if (val === undefined) {\n                return val;\n              }\n\n              var datePieces = val.split('-'); // YYYY-MM-DD\n\n              var year = parseInt(datePieces[0]);\n              var month = parseInt(datePieces[1]);\n              var day = parseInt(datePieces[2]);\n\n              if (year && month && !day) {\n                return \"\".concat(month, \"/\").concat(year);\n              }\n\n              return \"\".concat(month, \"/\").concat(day);\n            }\n          },\n          axisBorder: {\n            show: false\n          },\n          axisTicks: {\n            show: false\n          }\n        },\n        yaxis: [{\n          seriesName: 'Orders',\n          min: 0,\n          max: maxOrders * 2,\n          decimalsInFloat: 0,\n          axisBorder: {\n            show: false\n          },\n          axisTicks: {\n            show: true\n          },\n          labels: {\n            show: false\n          }\n        }, {\n          min: 0,\n          max: getRoundedMaxForChart(maxSales),\n          seriesName: 'Sales',\n          axisBorder: {\n            show: false\n          },\n          axisTicks: {\n            show: false\n          },\n          labels: {\n            show: true,\n            offsetX: -20,\n            style: {\n              color: '#8f98a3'\n            },\n            formatter: function formatter(val) {\n              if (response.formats.currencySymbol !== undefined) {\n                return formatCurrencyValue(response.formats.currencySymbol, val);\n              }\n\n              return val;\n            }\n          }\n        }],\n        tooltip: {\n          enabled: true,\n          x: {\n            show: false\n          },\n          y: {\n            show: false\n          }\n        },\n        grid: {\n          borderColor: '#e3e5e8',\n          strokeDashArray: 1,\n          padding: {\n            top: 10,\n            left: -10\n          }\n        },\n        stroke: {\n          width: 2,\n          show: true,\n          curve: 'straight',\n          lineCap: 'round'\n        },\n        legend: {\n          horizontalAlign: 'right'\n        }\n      };\n\n      if (chart) {\n        chart.updateOptions(options);\n      } else {\n        chart = new apexcharts__WEBPACK_IMPORTED_MODULE_0__[\"default\"](chartContainer, options);\n        chart.render();\n      }\n    }\n  });\n}\n\nfunction formatCurrencyValue(symbol, value) {\n  var floatValue = parseFloat(value);\n  var formattedNumber = floatValue.toLocaleString(undefined, {\n    maximumFractionDigits: 2\n  }).replace('.00', '');\n  return symbol + formattedNumber;\n}\n\nfunction getRoundedMaxForChart(value) {\n  var intValue = parseInt(value);\n  var roundString = '1'; // round to the nearest second digit\n\n  var roundTarget = getNumberOfDigits(intValue) - 1;\n\n  while (roundString.length < roundTarget) {\n    roundString += '0';\n  }\n\n  var roundAdjuster = parseInt(roundString);\n  var rounded = Math.ceil(value / roundAdjuster) * roundAdjuster;\n  return rounded;\n}\n\nfunction getNumberOfDigits(n) {\n  if (n < 0) {\n    return 0;\n  }\n\n  if (n < 10) {\n    return 1;\n  }\n\n  if (n < 100) {\n    return 2;\n  }\n\n  if (n < 1000) {\n    return 3;\n  }\n\n  if (n < 10000) {\n    return 4;\n  }\n\n  if (n < 100000) {\n    return 5;\n  }\n\n  if (n < 1000000) {\n    return 6;\n  }\n\n  if (n < 10000000) {\n    return 7;\n  }\n\n  if (n < 100000000) {\n    return 8;\n  }\n\n  if (n < 1000000000) {\n    return 9;\n  }\n  /*      2147483647 is 2^31-1 - add more ifs as needed\n     and adjust this final return as well. */\n\n\n  return 10;\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9qcy9vdmVydmlldy5qcy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL3NyYy9hc3NldGJ1bmRsZXMvc3JjL2pzL292ZXJ2aWV3LmpzPzhjOWQiXSwic291cmNlc0NvbnRlbnQiOlsiLyogZ2xvYmFsIENyYWZ0ICovXG5cbmltcG9ydCBBcGV4Q2hhcnRzIGZyb20gJ2FwZXhjaGFydHMnXG5cbmNvbnN0IHN0YXRQYW5lbHMgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc3RhdC1wYW5lbHMnKTtcbmNvbnN0IHVwZGF0ZVN0YXRzQnRuID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3VwZGF0ZS1zdGF0cy1idXR0b24nKTtcbmNvbnN0IHN0YXJ0RGF0ZUZpZWxkID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignaW5wdXRbbmFtZT1cInN0YXJ0RGF0ZVtkYXRlXVwiXScpO1xuY29uc3QgZW5kRGF0ZUZpZWxkID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignaW5wdXRbbmFtZT1cImVuZERhdGVbZGF0ZV1cIl0nKTtcbmNvbnN0IGNoYXJ0Q29udGFpbmVyID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ292ZXJ2aWV3LWNoYXJ0Jyk7XG5cbnZhciBjaGFydDtcblxuaWYgKHN0YXRQYW5lbHMpIHtcblxuICAgIGZldGNoU3RhdFBhbmVscygpO1xuICAgIHVwZGF0ZUNoYXJ0KCk7XG5cbiAgICB1cGRhdGVTdGF0c0J0bi5vbmNsaWNrID0gZnVuY3Rpb24oZSkge1xuICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgIGZldGNoU3RhdFBhbmVscygpO1xuICAgICAgICB1cGRhdGVDaGFydCgpO1xuICAgIH1cbn1cblxuZnVuY3Rpb24gZmV0Y2hTdGF0UGFuZWxzKCkge1xuICAgIGNvbnN0IG9yZGVyc0NvdW50ID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3N0YXQtb3JkZXJzQ291bnQnKTtcbiAgICBjb25zdCBvcmRlcnNTYWxlcyA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzdGF0LW9yZGVyc1NhbGVzJyk7XG4gICAgY29uc3QgYXZlcmFnZU9yZGVyc1ZhbHVlID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3N0YXQtYXZlcmFnZU9yZGVyc1ZhbHVlJyk7XG4gICAgY29uc3QgbmV3Q3VzdG9tZXJzID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3N0YXQtbmV3Q3VzdG9tZXJzJyk7XG4gICAgY29uc3QgcmV0dXJuaW5nQ3VzdG9tZXJzID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3N0YXQtcmV0dXJuaW5nQ3VzdG9tZXJzJyk7XG4gICAgY29uc3QgYXZlcmFnZUN1c3RvbWVyVmFsdWUgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc3RhdC1hdmVyYWdlQ3VzdG9tZXJWYWx1ZScpO1xuXG4gICAgY29uc3Qgc3Bpbm5lck1hcmt1cCA9ICc8ZGl2IGNsYXNzPVwic3Bpbm5lclwiPjwvZGl2Pic7XG5cbiAgICBvcmRlcnNDb3VudC5pbm5lckhUTUwgPSBzcGlubmVyTWFya3VwO1xuICAgIG9yZGVyc1NhbGVzLmlubmVySFRNTCA9IHNwaW5uZXJNYXJrdXA7XG4gICAgYXZlcmFnZU9yZGVyc1ZhbHVlLmlubmVySFRNTCA9IHNwaW5uZXJNYXJrdXA7XG4gICAgbmV3Q3VzdG9tZXJzLmlubmVySFRNTCA9IHNwaW5uZXJNYXJrdXA7XG4gICAgcmV0dXJuaW5nQ3VzdG9tZXJzLmlubmVySFRNTCA9IHNwaW5uZXJNYXJrdXA7XG4gICAgYXZlcmFnZUN1c3RvbWVyVmFsdWUuaW5uZXJIVE1MID0gc3Bpbm5lck1hcmt1cDtcblxuICAgIENyYWZ0LnBvc3RBY3Rpb25SZXF1ZXN0KFxuICAgICAgICAnc25pcGNhcnQvb3ZlcnZpZXcvZ2V0LXN0YXRzJyxcbiAgICAgICAge1xuICAgICAgICAgICAgc3RhcnREYXRlOiBzdGFydERhdGVGaWVsZC52YWx1ZSxcbiAgICAgICAgICAgIGVuZERhdGU6IGVuZERhdGVGaWVsZC52YWx1ZSxcbiAgICAgICAgfSxcbiAgICAgICAgZnVuY3Rpb24ocmVzcG9uc2UsIHRleHRTdGF0dXMpIHtcbiAgICAgICAgICAgIGlmICh0ZXh0U3RhdHVzID09PSAnc3VjY2VzcycgJiYgdHlwZW9mIChyZXNwb25zZS5lcnJvcikgPT09ICd1bmRlZmluZWQnKSB7XG4gICAgICAgICAgICAgICAgb3JkZXJzQ291bnQuaW5uZXJIVE1MID0gcmVzcG9uc2Uuc3RhdHMub3JkZXJzQ291bnQ7XG4gICAgICAgICAgICAgICAgb3JkZXJzU2FsZXMuaW5uZXJIVE1MID0gcmVzcG9uc2Uuc3RhdHMub3JkZXJzU2FsZXM7XG4gICAgICAgICAgICAgICAgYXZlcmFnZU9yZGVyc1ZhbHVlLmlubmVySFRNTCA9IHJlc3BvbnNlLnN0YXRzLmF2ZXJhZ2VPcmRlcnNWYWx1ZTtcbiAgICAgICAgICAgICAgICBuZXdDdXN0b21lcnMuaW5uZXJIVE1MID0gcmVzcG9uc2Uuc3RhdHMuY3VzdG9tZXJzLm5ld0N1c3RvbWVycztcbiAgICAgICAgICAgICAgICByZXR1cm5pbmdDdXN0b21lcnMuaW5uZXJIVE1MID0gcmVzcG9uc2Uuc3RhdHMuY3VzdG9tZXJzLnJldHVybmluZ0N1c3RvbWVycztcbiAgICAgICAgICAgICAgICBhdmVyYWdlQ3VzdG9tZXJWYWx1ZS5pbm5lckhUTUwgPSByZXNwb25zZS5zdGF0cy5hdmVyYWdlQ3VzdG9tZXJWYWx1ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICk7XG59XG5cblxuXG5BcnJheS5wcm90b3R5cGUubWF4ID0gZnVuY3Rpb24oKSB7XG4gICAgcmV0dXJuIE1hdGgubWF4LmFwcGx5KG51bGwsIHRoaXMpO1xufTtcblxuZnVuY3Rpb24gdXBkYXRlQ2hhcnQoKSB7XG4gICAgY2hhcnRDb250YWluZXIuY2xhc3NMaXN0LmFkZCgnc3Bpbm5lcicpO1xuXG4gICAgQ3JhZnQucG9zdEFjdGlvblJlcXVlc3QoXG4gICAgICAgICdzbmlwY2FydC9jaGFydHMvZ2V0LWNvbWJpbmVkLWRhdGEnLFxuICAgICAgICB7XG4gICAgICAgICAgICBzdGFydERhdGU6IHN0YXJ0RGF0ZUZpZWxkLnZhbHVlLFxuICAgICAgICAgICAgZW5kRGF0ZTogZW5kRGF0ZUZpZWxkLnZhbHVlLFxuICAgICAgICB9LFxuICAgICAgICBmdW5jdGlvbihyZXNwb25zZSwgdGV4dFN0YXR1cykge1xuICAgICAgICAgICAgLy8gVE9ETzogZ3JhY2VmdWxseSBoYW5kbGUgZXJyb3JcbiAgICAgICAgICAgIGNoYXJ0Q29udGFpbmVyLmNsYXNzTGlzdC5yZW1vdmUoJ3NwaW5uZXInKTtcblxuICAgICAgICAgICAgaWYgKHRleHRTdGF0dXMgPT09ICdzdWNjZXNzJyAmJiB0eXBlb2YgKHJlc3BvbnNlLmVycm9yKSA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgICAgICAgICBjb25zdCBtYXhPcmRlcnMgPSByZXNwb25zZS5zZXJpZXNbMF0uZGF0YS5tYXgoKTtcbiAgICAgICAgICAgICAgICBjb25zdCBtYXhTYWxlcyA9IHJlc3BvbnNlLnNlcmllc1sxXS5kYXRhLm1heCgpO1xuXG4gICAgICAgICAgICAgICAgY29uc3Qgb3B0aW9ucyA9IHtcbiAgICAgICAgICAgICAgICAgICAgY2hhcnQ6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGZvbnRGYW1pbHk6IFwic3lzdGVtLXVpLCBCbGlua01hY1N5c3RlbUZvbnQsIC1hcHBsZS1zeXN0ZW0sICdTZWdvZSBVSScsICdSb2JvdG8nLCAnT3h5Z2VuJywgJ1VidW50dScsICdDYW50YXJlbGwnLCAnRmlyYSBTYW5zJywgJ0Ryb2lkIFNhbnMnLCAnSGVsdmV0aWNhIE5ldWUnLCBzYW5zLXNlcmlmXCIsXG4gICAgICAgICAgICAgICAgICAgICAgICBoZWlnaHQ6IDIyMCxcbiAgICAgICAgICAgICAgICAgICAgICAgIHR5cGU6ICdsaW5lJyxcbiAgICAgICAgICAgICAgICAgICAgICAgIHRvb2xiYXI6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaG93OiB0cnVlXG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgYW5pbWF0aW9uczoge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNwZWVkOiAzNTAsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZWFzaW5nOiAnZWFzZW91dCcsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgYW5pbWF0ZUdyYWR1YWxseToge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBlbmFibGVkOiBmYWxzZVxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgY29sb3JzOiBbJyM4Zjk4YTMnLCAnIzBkNzhmMiddLFxuICAgICAgICAgICAgICAgICAgICBkYXRhTGFiZWxzOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBlbmFibGVkOiBmYWxzZVxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBtYXJrZXJzOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBzaXplOiAyLFxuICAgICAgICAgICAgICAgICAgICAgICAgaG92ZXI6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaXplOiA0LFxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIHN0cm9rZVdpZHRoOiAwLFxuICAgICAgICAgICAgICAgICAgICAgICAgZmlsbE9wYWNpdHk6IDAuNSxcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgZmlsbDoge1xuICAgICAgICAgICAgICAgICAgICAgICAgdHlwZTogWydzb2xpZCcsICdncmFkaWVudCddLFxuICAgICAgICAgICAgICAgICAgICAgICAgY29sb3JzOiBbJyM4Zjk4YTMnLCAnIzBkNzhmMiddLFxuICAgICAgICAgICAgICAgICAgICAgICAgb3BhY2l0eTogMSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGdyYWRpZW50OiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdHlwZTogXCJ2ZXJ0aWNhbFwiLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNoYWRlSW50ZW5zaXR5OiAwLjMsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgb3BhY2l0eUZyb206IDAuNSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBvcGFjaXR5VG86IDAsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc3RvcHM6IFswLCA5MF0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY29sb3JTdG9wczogW11cbiAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIHNlcmllczogcmVzcG9uc2Uuc2VyaWVzLFxuICAgICAgICAgICAgICAgICAgICB4YXhpczoge1xuICAgICAgICAgICAgICAgICAgICAgICAgY2F0ZWdvcmllczogcmVzcG9uc2UuY29sdW1ucyxcbiAgICAgICAgICAgICAgICAgICAgICAgIGxhYmVsczoge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNob3c6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGZvcm1hdHRlcjogZnVuY3Rpb24gKHZhbCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiAodmFsID09PSB1bmRlZmluZWQpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB2YWw7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgZGF0ZVBpZWNlcyA9IHZhbC5zcGxpdCgnLScpOyAvLyBZWVlZLU1NLUREXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IHllYXIgPSBwYXJzZUludChkYXRlUGllY2VzWzBdKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgbW9udGggPSBwYXJzZUludChkYXRlUGllY2VzWzFdKTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgZGF5ID0gcGFyc2VJbnQoZGF0ZVBpZWNlc1syXSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHllYXIgJiYgbW9udGggJiYgISBkYXkpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBgJHttb250aH0vJHt5ZWFyfWA7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cblxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICByZXR1cm4gYCR7bW9udGh9LyR7ZGF5fWA7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGF4aXNCb3JkZXI6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaG93OiBmYWxzZVxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgICAgIGF4aXNUaWNrczoge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNob3c6IGZhbHNlXG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB5YXhpczogW1xuICAgICAgICAgICAgICAgICAgICAgICAge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNlcmllc05hbWU6ICdPcmRlcnMnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG1pbjogMCxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBtYXg6IG1heE9yZGVycyAqIDIsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGVjaW1hbHNJbkZsb2F0OiAwLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGF4aXNCb3JkZXI6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2hvdzogZmFsc2VcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGF4aXNUaWNrczoge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaG93OiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbGFiZWxzOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNob3c6IGZhbHNlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbWluOiAwLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG1heDogZ2V0Um91bmRlZE1heEZvckNoYXJ0KG1heFNhbGVzKSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBzZXJpZXNOYW1lOiAnU2FsZXMnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGF4aXNCb3JkZXI6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgc2hvdzogZmFsc2VcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGF4aXNUaWNrczoge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaG93OiBmYWxzZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGxhYmVsczoge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBzaG93OiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBvZmZzZXRYOiAtMjAsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHN0eWxlOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBjb2xvcjogJyM4Zjk4YTMnLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBmb3JtYXR0ZXI6IGZ1bmN0aW9uKHZhbCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHJlc3BvbnNlLmZvcm1hdHMuY3VycmVuY3lTeW1ib2wgIT09IHVuZGVmaW5lZCkge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBmb3JtYXRDdXJyZW5jeVZhbHVlKHJlc3BvbnNlLmZvcm1hdHMuY3VycmVuY3lTeW1ib2wsIHZhbCk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB2YWw7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgICAgIF0sXG4gICAgICAgICAgICAgICAgICAgIHRvb2x0aXA6IHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGVuYWJsZWQ6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgICAgICB4OiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2hvdzogZmFsc2VcbiAgICAgICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgICAgICB5OiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgc2hvdzogZmFsc2UsXG4gICAgICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgICAgICAgICBncmlkOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICBib3JkZXJDb2xvcjogJyNlM2U1ZTgnLFxuICAgICAgICAgICAgICAgICAgICAgICAgc3Ryb2tlRGFzaEFycmF5OiAxLFxuICAgICAgICAgICAgICAgICAgICAgICAgcGFkZGluZzoge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRvcDogMTAsXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgbGVmdDogLTEwLFxuICAgICAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAgICAgc3Ryb2tlOiB7XG4gICAgICAgICAgICAgICAgICAgICAgICB3aWR0aDogMixcbiAgICAgICAgICAgICAgICAgICAgICAgIHNob3c6IHRydWUsXG4gICAgICAgICAgICAgICAgICAgICAgICBjdXJ2ZTogJ3N0cmFpZ2h0JyxcbiAgICAgICAgICAgICAgICAgICAgICAgIGxpbmVDYXA6ICdyb3VuZCcsXG4gICAgICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAgICAgICAgIGxlZ2VuZDoge1xuICAgICAgICAgICAgICAgICAgICAgICAgaG9yaXpvbnRhbEFsaWduOiAncmlnaHQnXG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9XG5cbiAgICAgICAgICAgICAgICBpZiAoY2hhcnQpIHtcbiAgICAgICAgICAgICAgICAgICAgY2hhcnQudXBkYXRlT3B0aW9ucyhvcHRpb25zKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBjaGFydCA9IG5ldyBBcGV4Q2hhcnRzKFxuICAgICAgICAgICAgICAgICAgICAgICAgY2hhcnRDb250YWluZXIsXG4gICAgICAgICAgICAgICAgICAgICAgICBvcHRpb25zXG4gICAgICAgICAgICAgICAgICAgICk7XG5cbiAgICAgICAgICAgICAgICAgICAgY2hhcnQucmVuZGVyKCk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgKTtcbn1cblxuZnVuY3Rpb24gZm9ybWF0Q3VycmVuY3lWYWx1ZShzeW1ib2wsIHZhbHVlKVxue1xuICAgIGNvbnN0IGZsb2F0VmFsdWUgPSBwYXJzZUZsb2F0KHZhbHVlKTtcbiAgICBjb25zdCBmb3JtYXR0ZWROdW1iZXIgPSBmbG9hdFZhbHVlLnRvTG9jYWxlU3RyaW5nKHVuZGVmaW5lZCwge21heGltdW1GcmFjdGlvbkRpZ2l0czoyfSkucmVwbGFjZSgnLjAwJywgJycpO1xuXG4gICAgcmV0dXJuIHN5bWJvbCArIGZvcm1hdHRlZE51bWJlcjtcbn1cblxuZnVuY3Rpb24gZ2V0Um91bmRlZE1heEZvckNoYXJ0KHZhbHVlKVxue1xuICAgIGNvbnN0IGludFZhbHVlID0gcGFyc2VJbnQodmFsdWUpO1xuICAgIGxldCByb3VuZFN0cmluZyA9ICcxJztcblxuICAgIC8vIHJvdW5kIHRvIHRoZSBuZWFyZXN0IHNlY29uZCBkaWdpdFxuICAgIGNvbnN0IHJvdW5kVGFyZ2V0ID0gZ2V0TnVtYmVyT2ZEaWdpdHMoaW50VmFsdWUpIC0gMTtcblxuICAgIHdoaWxlIChyb3VuZFN0cmluZy5sZW5ndGggPCByb3VuZFRhcmdldCkge1xuICAgICAgICByb3VuZFN0cmluZyArPSAnMCc7XG4gICAgfVxuXG4gICAgY29uc3Qgcm91bmRBZGp1c3RlciA9IHBhcnNlSW50KHJvdW5kU3RyaW5nKTtcbiAgICBjb25zdCByb3VuZGVkID0gTWF0aC5jZWlsKHZhbHVlIC8gcm91bmRBZGp1c3RlcikgKiByb3VuZEFkanVzdGVyO1xuXG4gICAgcmV0dXJuIHJvdW5kZWQ7XG59XG5cbmZ1bmN0aW9uIGdldE51bWJlck9mRGlnaXRzKG4pXG57XG4gICAgaWYgKG4gPCAwKSB7IHJldHVybiAwOyB9XG4gICAgaWYgKG4gPCAxMCkgeyByZXR1cm4gMTsgfVxuICAgIGlmIChuIDwgMTAwKSB7IHJldHVybiAyOyB9XG4gICAgaWYgKG4gPCAxMDAwKSB7IHJldHVybiAzOyB9XG4gICAgaWYgKG4gPCAxMDAwMCkgeyByZXR1cm4gNDsgfVxuICAgIGlmIChuIDwgMTAwMDAwKSB7IHJldHVybiA1OyB9XG4gICAgaWYgKG4gPCAxMDAwMDAwKSB7IHJldHVybiA2OyB9XG4gICAgaWYgKG4gPCAxMDAwMDAwMCkgeyByZXR1cm4gNzsgfVxuICAgIGlmIChuIDwgMTAwMDAwMDAwKSB7IHJldHVybiA4OyB9XG4gICAgaWYgKG4gPCAxMDAwMDAwMDAwKSB7IHJldHVybiA5OyB9XG4gICAgLyogICAgICAyMTQ3NDgzNjQ3IGlzIDJeMzEtMSAtIGFkZCBtb3JlIGlmcyBhcyBuZWVkZWRcbiAgICAgICBhbmQgYWRqdXN0IHRoaXMgZmluYWwgcmV0dXJuIGFzIHdlbGwuICovXG4gICAgcmV0dXJuIDEwO1xufSJdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQTtBQUFBO0FBRUE7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFHQTtBQUNBO0FBRkE7QUFLQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFHQTtBQUNBO0FBRkE7QUFLQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQURBO0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQURBO0FBSEE7QUFQQTtBQWVBO0FBQ0E7QUFDQTtBQURBO0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFEQTtBQUdBO0FBQ0E7QUFOQTtBQVFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFOQTtBQUpBO0FBY0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFBQTtBQUNBO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFoQkE7QUFrQkE7QUFDQTtBQURBO0FBR0E7QUFDQTtBQURBO0FBdkJBO0FBMkJBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBREE7QUFHQTtBQUNBO0FBREE7QUFHQTtBQUNBO0FBREE7QUFYQTtBQWdCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBREE7QUFHQTtBQUNBO0FBREE7QUFHQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBREE7QUFHQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQVpBO0FBVkE7QUEwQkE7QUFDQTtBQUNBO0FBQ0E7QUFEQTtBQUdBO0FBQ0E7QUFEQTtBQUxBO0FBU0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRkE7QUFIQTtBQVFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFKQTtBQU1BO0FBQ0E7QUFEQTtBQXZJQTtBQUNBO0FBMklBO0FBQ0E7QUFDQTtBQUNBO0FBS0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQUE7QUFBQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFBQTtBQUFBO0FBQ0E7QUFBQTtBQUFBO0FBQUE7QUFDQTtBQUFBO0FBQUE7QUFBQTtBQUNBO0FBQUE7QUFBQTtBQUFBO0FBQ0E7QUFBQTtBQUFBO0FBQUE7QUFDQTtBQUFBO0FBQUE7QUFBQTtBQUNBO0FBQUE7QUFBQTtBQUFBO0FBQ0E7QUFBQTtBQUFBO0FBQUE7QUFDQTtBQUFBO0FBQUE7QUFBQTtBQUNBO0FBQUE7QUFBQTtBQUFBO0FBQ0E7Ozs7QUFFQTtBQUNBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./src/assetbundles/src/js/overview.js\n");

/***/ })

/******/ });