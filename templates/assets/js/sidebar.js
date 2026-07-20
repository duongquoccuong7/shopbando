// dropdown menu
const items = document.querySelectorAll(".item-sidebar");
items.forEach((item) => {
  const parent = item.querySelector(".item-dash");
  parent.addEventListener("click", () => {
    item.classList.toggle("active");
  });
});
