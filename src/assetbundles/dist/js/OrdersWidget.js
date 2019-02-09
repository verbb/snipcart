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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/assetbundles/src/js/OrdersWidget.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/assetbundles/src/js/OrdersWidget.js":
/*!*************************************************!*\
  !*** ./src/assetbundles/src/js/OrdersWidget.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("/* global Craft */\n\n/* global Garnish */\n\n/* global jQuery */\n(function ($) {\n  Craft.OrdersWidget = Garnish.Base.extend({\n    settings: null,\n    $widget: null,\n    $chartContainer: null,\n    init: function init(widgetId) {\n      var widgetSelector = '#widget' + widgetId;\n      this.$widget = $(widgetSelector);\n      this.$chartContainer = this.$widget.find('.orders-chart');\n      this.updateChart();\n    },\n    updateChart: function updateChart() {\n      this.$chartContainer.addClass('spinner');\n      var self = this;\n      Craft.postActionRequest('snipcart/charts/get-orders-data', {\n        type: this.$chartContainer.data('chart-type'),\n        range: this.$chartContainer.data('chart-range')\n      }, $.proxy(function (response, textStatus) {\n        // TODO: gracefully handle error\n        self.$chartContainer.removeClass('spinner');\n\n        if (textStatus === 'success' && typeof response.error === 'undefined') {\n          var chart = new Craft.charts.Area(self.$chartContainer);\n          var chartDataTable = new Craft.charts.DataTable(response.dataTable);\n          var chartSettings = {\n            orientation: response.orientation,\n            dataScale: response.scale,\n            formats: response.formats\n          };\n          chart.draw(chartDataTable, chartSettings);\n        }\n      }));\n    }\n  });\n})(jQuery);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9qcy9PcmRlcnNXaWRnZXQuanMuanMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvYXNzZXRidW5kbGVzL3NyYy9qcy9PcmRlcnNXaWRnZXQuanM/NTM3NCJdLCJzb3VyY2VzQ29udGVudCI6WyIvKiBnbG9iYWwgQ3JhZnQgKi9cbi8qIGdsb2JhbCBHYXJuaXNoICovXG4vKiBnbG9iYWwgalF1ZXJ5ICovXG5cbihmdW5jdGlvbigkKSB7XG4gICAgQ3JhZnQuT3JkZXJzV2lkZ2V0ID0gR2FybmlzaC5CYXNlLmV4dGVuZCh7XG4gICAgICAgIHNldHRpbmdzOiBudWxsLFxuICAgICAgICAkd2lkZ2V0OiBudWxsLFxuICAgICAgICAkY2hhcnRDb250YWluZXI6IG51bGwsXG5cbiAgICAgICAgaW5pdDogZnVuY3Rpb24od2lkZ2V0SWQpIHtcbiAgICAgICAgICAgIGNvbnN0IHdpZGdldFNlbGVjdG9yID0gJyN3aWRnZXQnICsgd2lkZ2V0SWQ7XG4gICAgICAgICAgICB0aGlzLiR3aWRnZXQgPSAkKHdpZGdldFNlbGVjdG9yKTtcbiAgICAgICAgICAgIHRoaXMuJGNoYXJ0Q29udGFpbmVyID0gdGhpcy4kd2lkZ2V0LmZpbmQoJy5vcmRlcnMtY2hhcnQnKTtcbiAgICAgICAgICAgIHRoaXMudXBkYXRlQ2hhcnQoKTtcbiAgICAgICAgfSxcblxuICAgICAgICB1cGRhdGVDaGFydDogZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICB0aGlzLiRjaGFydENvbnRhaW5lci5hZGRDbGFzcygnc3Bpbm5lcicpO1xuICAgICAgICAgICAgY29uc3Qgc2VsZiA9IHRoaXM7XG5cbiAgICAgICAgICAgIENyYWZ0LnBvc3RBY3Rpb25SZXF1ZXN0KFxuICAgICAgICAgICAgICAgICdzbmlwY2FydC9jaGFydHMvZ2V0LW9yZGVycy1kYXRhJyxcbiAgICAgICAgICAgICAgICB7XG4gICAgICAgICAgICAgICAgICAgIHR5cGU6IHRoaXMuJGNoYXJ0Q29udGFpbmVyLmRhdGEoJ2NoYXJ0LXR5cGUnKSxcbiAgICAgICAgICAgICAgICAgICAgcmFuZ2U6IHRoaXMuJGNoYXJ0Q29udGFpbmVyLmRhdGEoJ2NoYXJ0LXJhbmdlJyksXG4gICAgICAgICAgICAgICAgfSxcbiAgICAgICAgICAgICAgICAkLnByb3h5KGZ1bmN0aW9uKHJlc3BvbnNlLCB0ZXh0U3RhdHVzKSB7XG4gICAgICAgICAgICAgICAgICAgIC8vIFRPRE86IGdyYWNlZnVsbHkgaGFuZGxlIGVycm9yXG4gICAgICAgICAgICAgICAgICAgIHNlbGYuJGNoYXJ0Q29udGFpbmVyLnJlbW92ZUNsYXNzKCdzcGlubmVyJyk7XG5cbiAgICAgICAgICAgICAgICAgICAgaWYgKHRleHRTdGF0dXMgPT09ICdzdWNjZXNzJyAmJiB0eXBlb2YgKHJlc3BvbnNlLmVycm9yKSA9PT0gJ3VuZGVmaW5lZCcpIHtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IGNoYXJ0ID0gbmV3IENyYWZ0LmNoYXJ0cy5BcmVhKHNlbGYuJGNoYXJ0Q29udGFpbmVyKTtcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IGNoYXJ0RGF0YVRhYmxlID0gbmV3IENyYWZ0LmNoYXJ0cy5EYXRhVGFibGUocmVzcG9uc2UuZGF0YVRhYmxlKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgY29uc3QgY2hhcnRTZXR0aW5ncyA9IHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBvcmllbnRhdGlvbjogcmVzcG9uc2Uub3JpZW50YXRpb24sXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgZGF0YVNjYWxlOiByZXNwb25zZS5zY2FsZSxcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBmb3JtYXRzOiByZXNwb25zZS5mb3JtYXRzXG4gICAgICAgICAgICAgICAgICAgICAgICB9O1xuXG4gICAgICAgICAgICAgICAgICAgICAgICBjaGFydC5kcmF3KGNoYXJ0RGF0YVRhYmxlLCBjaGFydFNldHRpbmdzKTtcbiAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIH0pXG4gICAgICAgICAgICApO1xuICAgICAgICB9LFxuICAgIH0pO1xufSkoalF1ZXJ5KTsiXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFBQTtBQUNBO0FBQUE7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFDQTtBQUNBO0FBRUE7QUFHQTtBQUNBO0FBRkE7QUFLQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUhBO0FBTUE7QUFDQTtBQUNBO0FBRUE7QUF4Q0E7QUEwQ0EiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/assetbundles/src/js/OrdersWidget.js\n");

/***/ })

/******/ });