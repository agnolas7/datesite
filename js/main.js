// ── No button runs away ──
function runAway(btn) {
  const maxX = window.innerWidth - 120;
  const maxY = window.innerHeight - 60;
  const x = Math.floor(Math.random() * maxX);
  const y = Math.floor(Math.random() * maxY);
  btn.style.position = "fixed";
  btn.style.left = x + "px";
  btn.style.top = y + "px";
  btn.style.zIndex = 9999;
  btn.style.transition = "left 0.2s, top 0.2s";
}

// attach event if device supports hover (desktop)
document.addEventListener("DOMContentLoaded", () => {
  const noBtn = document.getElementById("noBtn");
  if (!noBtn) return;

  // media query matches devices where the primary input can hover
  if (window.matchMedia("(hover: hover) and (pointer: fine)").matches) {
    noBtn.addEventListener("mouseover", () => runAway(noBtn));
  }
});

// ── Maybe flow ──
function showStep(id) {
  document
    .querySelectorAll(".center-card")
    .forEach((el) => el.classList.add("hidden"));
  const el = document.getElementById(id);
  if (el) {
    el.classList.remove("hidden");
    el.style.animation = "none";
    void el.offsetWidth; // reflow
    el.style.animation = "fadeUp 0.5s ease";
  }
}

function showProfile() {
  showStep("step-profile");
}
function showAreYouSure() {
  showStep("step-areyousure");
}
function showThinkAgain() {
  showStep("step-thinkAgain");
}

function startCountdown() {
  showStep("step-countdown");
  let count = 10;
  const display = document.getElementById("countdownDisplay");
  const msg = document.getElementById("countdownMsg");
  if (!display) return;

  const timer = setInterval(() => {
    count--;
    display.textContent = count;
    if (count <= 3) msg.textContent = "last chance...";
    if (count <= 0) {
      clearInterval(timer);
      showStep("step-whatAboutNow");
    }
  }, 1000);
}

// ── Easter egg (click logo 3 times) ──
let logoClickCount = 0;
const logo = document.getElementById("easterEggLogo");
const easterMsg = document.getElementById("easterEggMsg");

if (logo) {
  logo.addEventListener("click", () => {
    logoClickCount++;
    if (logoClickCount >= 3) {
      easterMsg.classList.add("show");
      logoClickCount = 0;
      setTimeout(() => easterMsg.classList.remove("show"), 5000);
    }
  });
}
