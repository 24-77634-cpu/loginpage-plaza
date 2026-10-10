// Show / hide password buttons
document.querySelectorAll(".toggle").forEach(function (btn) {
  btn.addEventListener("click", function () {
    var input = btn.parentElement.querySelector("input");
    var show = input.type === "password";
    input.type = show ? "text" : "password";
    btn.textContent = show ? "Hide" : "Show";
  });
});

// If the browser shows an old copy of a page (Back/Forward button),
// reload it so the server can check the session again.
window.addEventListener("pageshow", function (e) {
  if (e.persisted) location.reload();
});

// Protected pages: when the idle time is over, reload so the server
// logs the user out and shows "session expired".
var seconds = parseInt(document.body.dataset.timeout, 10);
if (seconds) {
  setTimeout(function () { location.reload(); }, (seconds + 1) * 1000);
}
