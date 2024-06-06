document.querySelector("form").addEventListener("submit", function (event) {
  event.target.querySelector('input[type="submit"]').value = "送信中";
});

document.querySelector("form").addEventListener("load", function (event) {
  event.target.querySelector('input[type="submit"]').value = "送信";
});

document.querySelector("form").addEventListener("error", function (event) {
  event.target.querySelector('input[type="submit"]').value = "送信";
});
