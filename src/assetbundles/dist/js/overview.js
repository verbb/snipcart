!function(e){function t(t){for(var s,a,i=t[0],l=t[1],u=t[2],d=0,p=[];d<i.length;d++)a=i[d],n[a]&&p.push(n[a][0]),n[a]=0;for(s in l)Object.prototype.hasOwnProperty.call(l,s)&&(e[s]=l[s]);for(c&&c(t);p.length;)p.shift()();return o.push.apply(o,u||[]),r()}function r(){for(var e,t=0;t<o.length;t++){for(var r=o[t],s=!0,i=1;i<r.length;i++){var l=r[i];0!==n[l]&&(s=!1)}s&&(o.splice(t--,1),e=a(a.s=r[0]))}return e}var s={},n={4:0},o=[];function a(t){if(s[t])return s[t].exports;var r=s[t]={i:t,l:!1,exports:{}};return e[t].call(r.exports,r,r.exports,a),r.l=!0,r.exports}a.m=e,a.c=s,a.d=function(e,t,r){a.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},a.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},a.t=function(e,t){if(1&t&&(e=a(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(a.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var s in e)a.d(r,s,function(t){return e[t]}.bind(null,s));return r},a.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return a.d(t,"a",t),t},a.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},a.p="";var i=window.webpackJsonp=window.webpackJsonp||[],l=i.push.bind(i);i.push=t,i=i.slice();for(var u=0;u<i.length;u++)t(i[u]);var c=l;o.push([8,0]),r()}({8:function(e,t,r){"use strict";r.r(t);var s=r(0);document.getElementById("stat-panels")&&Craft.postActionRequest("snipcart/overview/get-stats",{},function(e,t){if("success"===t&&void 0===e.error){var r=document.getElementById("stat-ordersCount");r.innerHTML=e.stats.ordersCount;var s=document.getElementById("stat-ordersSales");s.innerHTML=e.stats.ordersSales;var n=document.getElementById("stat-averageOrdersValue");n.innerHTML=e.stats.averageOrdersValue;var o=document.getElementById("stat-newCustomers");o.innerHTML=e.stats.customers.newCustomers;var a=document.getElementById("stat-returningCustomers");a.innerHTML=e.stats.customers.returningCustomers;var i=document.getElementById("stat-averageCustomerValue");i.innerHTML=e.stats.averageCustomerValue}});var n=document.getElementById("overview-chart");function o(e){return e<0?0:e<10?1:e<100?2:e<1e3?3:e<1e4?4:e<1e5?5:e<1e6?6:e<1e7?7:e<1e8?8:e<1e9?9:10}n.classList.add("spinner"),Craft.postActionRequest("snipcart/charts/get-combined-data",{},function(e,t){if(n.classList.remove("spinner"),"success"===t&&void 0===e.error){var r=e.series[0].data.max(),a=e.series[1].data.max(),i={chart:{fontFamily:"system-ui, BlinkMacSystemFont, -apple-system, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif",height:220,type:"line",toolbar:{show:!0},animations:{speed:350,easing:"easeout",animateGradually:{enabled:!1}}},colors:["#8f98a3","#0d78f2"],dataLabels:{enabled:!1},markers:{size:2,hover:{size:4},strokeWidth:0,fillOpacity:.5},fill:{type:["solid","gradient"],colors:["#8f98a3","#0d78f2"],opacity:1,gradient:{type:"vertical",shadeIntensity:.3,opacityFrom:.5,opacityTo:0,stops:[0,90],colorStops:[]}},series:e.series,xaxis:{categories:e.columns,labels:{show:!1,formatter:function(e){if(void 0===e)return e;var t=e.split("-"),r=parseInt(t[1]),s=parseInt(t[2]);return"".concat(r,"/").concat(s)}},axisBorder:{show:!1},axisTicks:{show:!1}},yaxis:[{seriesName:"Orders",min:0,max:2*r,decimalsInFloat:0,axisBorder:{show:!1},axisTicks:{show:!0},labels:{show:!1}},{min:0,max:(u=a,c=o(u)-1,Math.ceil(u/c)*c),seriesName:"Sales",axisBorder:{show:!1},axisTicks:{show:!1},labels:{show:!0,offsetX:-20,style:{color:"#8f98a3"},formatter:function(t){return void 0!==e.formats.currencySymbol?(r=e.formats.currencySymbol,s=r+parseFloat(t).toFixed(2),String(s).replace(".00","")):t;var r,s}}}],tooltip:{enabled:!0,x:{show:!1},y:{show:!1}},grid:{borderColor:"#e3e5e8",strokeDashArray:1,padding:{top:10,left:-10}},stroke:{width:2,show:!0,curve:"smooth",lineCap:"round"},legend:{horizontalAlign:"right"}},l=new s.a(n,i);l.render()}var u,c}),Array.prototype.max=function(){return Math.max.apply(null,this)}}});