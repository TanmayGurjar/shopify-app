// js/script.js

// console.log("Script is running");
// $(document).ready(function () {
//   console.log("Script is running in func");

//   $("body").prepend(
//     '<div class="header" id="header"><h3> hello there</h3></div>'
//   );
//   $("head").prepend(
//     "<style>#header { padding: 12px 18px; background: #555; color: #f1f1f1; } .content { padding: 16px; } .sticky {position: fixed; top: 0; width: 100%; } .sticky + .content { padding-top: 100px; } </style>"
//   );

//   var header = document.getElementById("header");
//   var sticky = header.offsetTop;

//   window.onscroll = function () {
//     if (window.scrollY > sticky) {
//       header.classList.add("sticky");
//     } else {
//       header.classList.remove("sticky");
//     }
//   };
// });

// console.log("Script is running");

// function myFunc() {
//   console.log("Script is running in func");

//   var header = document.createElement("div");
//   header.setAttribute("class", "header");
//   header.setAttribute("id", "header");
//   header.innerHTML = "<h3> hello there</h3>";
//   document.body.prepend(header);

//   var style = document.createElement("style");
//   style.innerHTML =
//     "#header { padding: 12px 18px; background: #555; color: #f1f1f1; z-index: 1000; } " +
//     ".content { padding: 16px; } " +
//     ".sticky { position: fixed; top: 0; width: 100%; z-index: 1000; } " +
//     ".sticky + .content { padding-top: 100px; }";
//   document.head.prepend(style);

//   var headerElement = document.getElementById("header");
//   var sticky = headerElement.offsetTop;

//   window.onscroll = function () {
//     if (window.scrollY > sticky) {
//       headerElement.classList.add("sticky");
//     } else {
//       headerElement.classList.remove("sticky");
//     }
//   };
// }
// myFunc();

console.log("Script is running");

function setupStickyHeader() {
  console.log("Setting up sticky header");

  var header = document.createElement("div");
  header.setAttribute("class", "header");
  header.setAttribute("id", "header");
  header.innerHTML = "<h3> hello there</h3>";
  document.body.prepend(header);

  var style = document.createElement("style");
  style.innerHTML =
    "#header { padding: 12px 18px; background: #555; color: #f1f1f1; z-index: 1000; } " +
    ".content { padding: 16px; } " +
    ".sticky { position: fixed; top: 0; width: 100%; z-index: 1000; } " +
    ".sticky + .content { padding-top: 100px; }";
  document.head.prepend(style);

  var headerElement = document.getElementById("header");
  var sticky = headerElement.offsetTop;

  window.onscroll = function () {
    if (window.scrollY > sticky) {
      headerElement.classList.add("sticky");
    } else {
      headerElement.classList.remove("sticky");
    }
  };
}

console.log("Script is running");

function setupCookieLogic() {
  console.log("Setting up cookie logic");

  const cookieAccepted = localStorage.getItem("cookieAccepted");

  if (!cookieAccepted) {
    showCookieMessage();
  }
}

function showCookieMessage() {
  const cookieContainer = document.createElement("div");
  cookieContainer.setAttribute("id", "cookieContainer");
  cookieContainer.innerHTML = `
      <div id="cookieMessage" style="position: fixed; bottom: 20px; right: 20px; background: #fff; border: 1px solid #ccc; padding: 10px;">
      <p>This website uses cookies to improve your experience.</p>
      <button onclick="acceptCookies()">Accept</button>
      <button onclick="cancelCookies()">Cancel</button>
    </div>
  `;

  document.body.appendChild(cookieContainer);
}

function acceptCookies() {
  localStorage.setItem("cookieAccepted", "true");
  document.getElementById("cookieContainer").remove();
  showToastMessage("Cookies accepted!");
}

function cancelCookies() {
  document.getElementById("cookieContainer").remove();
}

function showToastMessage(message) {
  const toastContainer = document.createElement("div");
  toastContainer.setAttribute("id", "toastContainer");
  toastContainer.innerHTML = `<div class="toast">${message}</div>`;

  document.body.appendChild(toastContainer);

  setTimeout(() => {
    document.getElementById("toastContainer").remove();
  }, 3000);
}

// Add the CSS styles for the toast
var toastStyles = document.createElement("style");
toastStyles.innerHTML = `
  #toastContainer {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
  }

  .toast {
    background-color: #333;
    color: #fff;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
  }
`;

document.head.appendChild(toastStyles);

document.addEventListener("DOMContentLoaded", setupCookieLogic);

setupStickyHeader();
setupCookieLogic();
