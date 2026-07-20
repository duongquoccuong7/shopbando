// ================= Product & Topic Sliders =================
document.querySelectorAll(".slide-topic, .slide-product").forEach(initSlider);

function initSlider(section) {
  const slide = section.querySelector(".slide");
  const btnPrev = section.querySelector(".slide-btn-prev");
  const btnNext = section.querySelector(".slide-btn-next");

  if (!slide || !btnPrev || !btnNext) return;

  let current = 0;
  const itemWidth = 310;

  function getMax() {
    const visibleWidth = section.offsetWidth;
    const totalWidth = slide.scrollWidth;
    return Math.max(0, Math.ceil((totalWidth - visibleWidth) / itemWidth));
  }

  function update() {
    const max = getMax();
    const visibleWidth = section.offsetWidth;
    const totalWidth = slide.scrollWidth;

    btnPrev.classList.toggle("active", current > 0);
    btnNext.classList.toggle("active", current < max);

    let translateValue = current * itemWidth;
    const maxTranslate = totalWidth - visibleWidth;

    if (translateValue > maxTranslate) {
      translateValue = maxTranslate;
    }

    slide.style.transform = `translateX(-${translateValue}px)`;
  }

  btnNext.addEventListener("click", () => {
    const max = getMax();
    if (current < max) current++;
    update();
  });

  btnPrev.addEventListener("click", () => {
    if (current > 0) current--;
    update();
  });

  window.addEventListener("resize", update);
  update();
}

// ================= Infinite Sidebar Banner Slider =================
const sidebarList = document.querySelector(".sidebar-list");
const originalBanners = document.querySelectorAll(".sidebar-anchor");
const sideBtnPrev = document.querySelector(".sidebar-btn-prev");
const sideBtnNext = document.querySelector(".sidebar-btn-next");

if (sidebarList && originalBanners.length > 0) {
  let currentIndex = 1;
  let autoSlideInterval;
  let isTransitioning = false;
  const totalSlides = originalBanners.length;

  // Clone slides
  const firstClone = originalBanners[0].cloneNode(true);
  const lastClone = originalBanners[originalBanners.length - 1].cloneNode(true);

  sidebarList.appendChild(firstClone);
  sidebarList.insertBefore(lastClone, originalBanners[0]);

  sidebarList.style.transform = `translateX(-${currentIndex * 100}%)`;

  function showBanner(index, hasAnimation = true) {
    sidebarList.style.transition = hasAnimation
      ? "transform 0.6s cubic-bezier(0.25, 1, 0.5, 1)"
      : "none";
    sidebarList.style.transform = `translateX(-${index * 100}%)`;
  }

  sidebarList.addEventListener("transitionend", () => {
    isTransitioning = false;
    if (currentIndex === totalSlides + 1) {
      currentIndex = 1;
      showBanner(currentIndex, false);
    }
    if (currentIndex === 0) {
      currentIndex = totalSlides;
      showBanner(currentIndex, false);
    }
  });

  function nextSlide() {
    if (isTransitioning) return;
    isTransitioning = true;
    currentIndex++;
    showBanner(currentIndex);
  }

  function prevSlide() {
    if (isTransitioning) return;
    isTransitioning = true;
    currentIndex--;
    showBanner(currentIndex);
  }

  function startAutoSlide() {
    clearInterval(autoSlideInterval);
    autoSlideInterval = setInterval(nextSlide, 5000);
  }

  if (sideBtnNext)
    sideBtnNext.addEventListener("click", () => {
      nextSlide();
      startAutoSlide();
    });
  if (sideBtnPrev)
    sideBtnPrev.addEventListener("click", () => {
      prevSlide();
      startAutoSlide();
    });

  startAutoSlide();
}

// ================= Smooth Scrolling =================
document.querySelectorAll(".smooth-scroll").forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const targetId = this.getAttribute("href");
    const targetElement = document.querySelector(targetId);
    if (targetElement) {
      targetElement.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  });
});

// ================= Nike Search Overlay Handles =================
document.addEventListener("DOMContentLoaded", () => {
  const searchTrigger = document.getElementById("search_trigger");
  const searchMainInput = document.getElementById("search_main_input");
  const searchOverlay = document.getElementById("search_overlay");
  const searchBackdrop = document.getElementById("search_backdrop");
  const cancelButton = document.getElementById("cancel_search");

  function openNikeSearch(e) {
    if (e) e.preventDefault();
    if (searchOverlay && searchBackdrop) {
      searchOverlay.classList.add("open");
      searchBackdrop.classList.add("open");
      document.body.style.overflow = "hidden";
      setTimeout(() => {
        if (searchMainInput) searchMainInput.focus();
      }, 200);
    }
  }

  function closeNikeSearch(e) {
    if (e) e.preventDefault();
    if (searchOverlay && searchBackdrop) {
      searchOverlay.classList.remove("open");
      searchBackdrop.classList.remove("open");
      document.body.style.overflow = "auto";
      if (searchMainInput) searchMainInput.value = "";
    }
  }

  if (searchTrigger) searchTrigger.addEventListener("click", openNikeSearch);
  if (cancelButton) cancelButton.addEventListener("click", closeNikeSearch);
  if (searchBackdrop) searchBackdrop.addEventListener("click", closeNikeSearch);

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeNikeSearch();
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const coupons = document.querySelectorAll(".tile_cou");
  if (coupons.length === 0) return;

  let currentIndex = 0;

  // Hiện coupon đầu tiên ngay khi tải trang
  coupons[currentIndex].classList.add("active");

  // Cứ mỗi 10 giây (10000ms) thì đổi sang coupon tiếp theo
  setInterval(() => {
    coupons[currentIndex].classList.remove("active"); // Ẩn coupon cũ
    currentIndex = (currentIndex + 1) % coupons.length; // Chuyển sang index tiếp theo
    coupons[currentIndex].classList.add("active"); // Hiện coupon mới
  }, 10000);
});
