"use strict";!function(){var e=document.getElementById("{formAnchor}").parentElement;e.addEventListener("submit",function(){var t=e.querySelectorAll("[type=submit]:not([name={prevButtonName}])");for(var n in t)if(t.hasOwnProperty(n)){var r=t[n];return r.disabled=!0,!0}})}();