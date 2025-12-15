const btn = document.getElementById("lang-btn");
let isFrench = true;

btn.addEventListener("click", () => {
  const elements = document.querySelectorAll("[data-fr]");

  elements.forEach(el => {
    el.textContent = isFrench ? el.getAttribute("data-en") : el.getAttribute("data-fr");
  });

  isFrench = !isFrench;
  btn.textContent = isFrench ? "EN" : "FR";
});
