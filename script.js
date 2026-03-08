/**
 * M2B E-book Landing Page - JavaScript v2.0
 * Updated: February 2026 - 22 Chapters Edition
 * WhatsApp: +62 822 6184 6811
 */

// Configuration
const CONFIG = {
  whatsappNumber: "6282261846811",
  ebookPrice: 49000,
  bankName: "BCA",
  bankAccount: "8280424243",
  bankHolder: "Eka Mayang Sari Harahap",
  apiEndpoint: "api/process_order.php",
};

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
  initSmoothScroll();
  initNavbarScroll();
  initScrollAnimations();
  initCounterAnimation();
  initBackToTop();
  initMobileMenu();
  initFAQ();
  initOrderForm();
  initCountdownTimer();
  initStickyCta();
  initSocialProofPopup();
});

/**
 * Initialize Smooth Scroll
 */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href");
      const target = document.querySelector(targetId);

      if (target) {
        const navbarHeight = document.querySelector(".navbar").offsetHeight;
        const targetPosition = target.offsetTop - navbarHeight - 20;

        window.scrollTo({
          top: targetPosition,
          behavior: "smooth",
        });

        // Close mobile menu if open
        const navLinks = document.querySelector(".nav-links");
        if (navLinks) navLinks.classList.remove("active");
      }
    });
  });
}

/**
 * Initialize Navbar Scroll Effect
 */
function initNavbarScroll() {
  const navbar = document.querySelector(".navbar");
  if (!navbar) return;

  window.addEventListener("scroll", function () {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > 50) {
      navbar.classList.add("scrolled");
    } else {
      navbar.classList.remove("scrolled");
    }
  });
}

/**
 * Initialize Scroll Animations (Intersection Observer)
 */
function initScrollAnimations() {
  const elements = document.querySelectorAll(
    ".feature-card, .chapter-card, .audience-card, .testimonial-card, " +
    ".part-header, .infographic-wrapper, .pricing-card",
  );

  if (!elements.length) return;

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
          // Stagger animation based on index within view
          const siblings = Array.from(entry.target.parentElement.children);
          const siblingIndex = siblings.indexOf(entry.target);

          setTimeout(() => {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
          }, siblingIndex * 80);

          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    },
  );

  elements.forEach((el) => {
    el.style.opacity = "0";
    el.style.transform = "translateY(20px)";
    el.style.transition = "opacity 0.5s ease, transform 0.5s ease";
    observer.observe(el);
  });
}

/**
 * Initialize Counter Animation
 */
function initCounterAnimation() {
  const counters = document.querySelectorAll(".stat-number[data-target]");
  if (!counters.length) return;

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.5 },
  );

  counters.forEach((counter) => observer.observe(counter));
}

function animateCounter(element) {
  const target = parseInt(element.getAttribute("data-target"));
  const duration = 2000; // 2 seconds
  const steps = 60;
  const increment = target / steps;
  let current = 0;
  const stepTime = duration / steps;

  const timer = setInterval(() => {
    current += increment;
    if (current >= target) {
      element.textContent = target;
      clearInterval(timer);
    } else {
      element.textContent = Math.floor(current);
    }
  }, stepTime);
}

/**
 * Initialize Back to Top Button
 */
function initBackToTop() {
  const btn = document.getElementById("backToTop");
  if (!btn) return;

  window.addEventListener("scroll", function () {
    if (window.pageYOffset > 500) {
      btn.classList.add("visible");
    } else {
      btn.classList.remove("visible");
    }
  });

  btn.addEventListener("click", function () {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });
}

/**
 * Initialize Mobile Menu
 */
function initMobileMenu() {
  const menuBtn = document.getElementById("mobileMenuBtn");
  const navLinks = document.querySelector(".nav-links");

  if (!menuBtn || !navLinks) return;

  menuBtn.addEventListener("click", function () {
    navLinks.classList.toggle("active");

    // Animate hamburger
    const spans = menuBtn.querySelectorAll("span");
    if (navLinks.classList.contains("active")) {
      spans[0].style.transform = "rotate(45deg) translate(4px, 4px)";
      spans[1].style.opacity = "0";
      spans[2].style.transform = "rotate(-45deg) translate(4px, -4px)";
    } else {
      spans[0].style.transform = "none";
      spans[1].style.opacity = "1";
      spans[2].style.transform = "none";
    }
  });
}

/**
 * Initialize FAQ Accordion
 */
function initFAQ() {
  // Already handled by inline toggleFaq function, but add keyboard support
  const faqButtons = document.querySelectorAll(".faq-question");
  faqButtons.forEach((button) => {
    button.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        toggleFaq(this);
      }
    });
  });
}

/**
 * Toggle FAQ - Global function
 */
function toggleFaq(button) {
  const item = button.parentElement;
  const icon = button.querySelector(".faq-icon");
  const answer = item.querySelector(".faq-answer");
  const isActive = item.classList.contains("active");

  // Close all
  document.querySelectorAll(".faq-item").forEach((i) => {
    i.classList.remove("active");
    const ic = i.querySelector(".faq-icon");
    if (ic) ic.textContent = "+";
  });

  if (!isActive) {
    item.classList.add("active");
    if (icon) icon.textContent = "−";
  }
}

/**
 * Generate Order ID
 */
function generateOrderId() {
  const date = new Date();
  const dateStr =
    date.getFullYear().toString() +
    (date.getMonth() + 1).toString().padStart(2, "0") +
    date.getDate().toString().padStart(2, "0");
  const random = Math.random().toString(36).substring(2, 8).toUpperCase();
  return `M2B-${dateStr}-${random}`;
}

/**
 * Copy to Clipboard
 */
function copyToClipboard(text) {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(text).then(() => {
      alert("Berhasil disalin!");
    });
  } else {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand("copy");
    document.body.removeChild(textArea);
    alert("Berhasil disalin!");
  }
}

/**
 * Initialize Order Form
 */
function initOrderForm() {
  const form = document.getElementById("orderForm");
  if (!form) return;

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const fullName = document.getElementById("fullName").value.trim();
    const email = document.getElementById("email").value.trim();
    const whatsapp = document.getElementById("whatsapp").value.trim();
    const city = document.getElementById("city").value.trim();
    const ebookLang = document.getElementById("ebookLang").value;
    const purpose = document.getElementById("purpose").value;

    // Validation
    if (!fullName || !email || !whatsapp || !city) {
      alert("Mohon lengkapi semua field yang wajib diisi.");
      return;
    }

    if (!isValidEmail(email)) {
      alert("Format email tidak valid.");
      return;
    }

    // Validate WhatsApp format
    const waClean = whatsapp.replace(/[^0-9]/g, '');
    if (waClean.length < 10 || waClean.length > 15) {
      alert("Nomor WhatsApp tidak valid. Gunakan format 08xx atau 628xx.");
      return;
    }

    // Disable submit button to prevent double submit
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = "Memproses...";
    }

    // Try server-side API first
    let orderId = null;
    let waUrl = null;
    try {
      const response = await fetch(CONFIG.apiEndpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          name: fullName,
          email: email,
          whatsapp: whatsapp,
          city: city,
          ebook_lang: ebookLang,
          purpose: purpose,
        }),
      });
      const result = await response.json();
      if (result.success) {
        orderId = result.order_id;
        waUrl = result.whatsapp_url;
      }
    } catch (err) {
      console.warn("API call failed, using client-side fallback:", err);
    }

    // Fallback: generate client-side order ID if API failed
    if (!orderId) {
      orderId = generateOrderId();
    }

    // Create order object for localStorage (backup/admin panel)
    const order = {
      orderId: orderId,
      name: fullName,
      email: email,
      phone: whatsapp,
      city: city,
      purpose: purpose,
      amount: CONFIG.ebookPrice,
      status: "pending",
      createdAt: new Date().toISOString(),
      updatedAt: null,
    };

    // Save to localStorage as backup
    saveOrder(order);

    // Build WhatsApp URL (fallback if API didn't return one)
    if (!waUrl) {
      const waMessage = encodeURIComponent(
        `Halo M2B, saya ingin memesan E-book Ekspor Impor v2.0\n\n` +
        `📋 *Detail Pesanan*\n` +
        `Order ID: ${orderId}\n` +
        `Nama: ${fullName}\n` +
        `Email: ${email}\n` +
        `WhatsApp: ${whatsapp}\n` +
        `Kota: ${city}\n` +
        `Tujuan: ${formatPurposeLabel(purpose)}\n\n` +
        `Total: Rp 49.000\n\n` +
        `Saya akan segera melakukan pembayaran. Terima kasih! 🙏`,
      );
      waUrl = `https://wa.me/${CONFIG.whatsappNumber}?text=${waMessage}`;
    }

    // Show success feedback
    showOrderSuccess(orderId);

    // Open WhatsApp
    setTimeout(() => {
      window.open(waUrl, "_blank");
    }, 1500);

    // Reset form & re-enable button
    form.reset();
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.textContent = "Pesan Sekarang 💬";
    }
  });
}

/**
 * Save order to localStorage
 */
function saveOrder(order) {
  try {
    const orders = JSON.parse(localStorage.getItem("m2b_orders") || "[]");
    orders.push(order);
    localStorage.setItem("m2b_orders", JSON.stringify(orders));
  } catch (e) {
    console.error("Failed to save order:", e);
  }
}

/**
 * Show success message after order
 */
function showOrderSuccess(orderId) {
  const formCard = document.querySelector(".order-form-card");
  if (!formCard) return;

  const originalContent = formCard.innerHTML;

  // Escape orderId to prevent XSS
  const safeOrderId = orderId.replace(/[<>"'&]/g, c => ({ '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '&': '&amp;' }[c]));

  formCard.innerHTML = `
        <div style="text-align:center; padding:60px 36px;">
            <div style="font-size:64px; margin-bottom:20px;">🎉</div>
            <h3 style="font-size:22px; font-weight:700; color:#1f2937; margin-bottom:8px;">Pesanan Berhasil!</h3>
            <p style="color:#6b7280; margin-bottom:16px;">Order ID: <strong style="color:#667eea;">${safeOrderId}</strong></p>
            <p style="color:#6b7280; font-size:14px; margin-bottom:24px;">
                Anda akan diarahkan ke WhatsApp untuk konfirmasi pembayaran.<br>
                Silakan transfer ke BCA: <strong>8280424243</strong> a/n Eka Mayang Sari Harahap
            </p>
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:14px; font-size:13px; color:#065f46;">
                📧 E-book akan dikirim ke email Anda setelah pembayaran terverifikasi (maks. 2 jam)
            </div>
        </div>
    `;

  // Restore form after 8 seconds
  setTimeout(() => {
    formCard.innerHTML = originalContent;
    initOrderForm(); // Re-attach event listener
  }, 8000);
}

/**
 * Validate email format
 */
function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/**
 * Format purpose label
 */
function formatPurposeLabel(purpose) {
  const map = {
    bisnis: "Memulai Bisnis Ekspor/Impor",
    umkm: "Scale Up UMKM",
    belajar: "Belajar / Riset",
    profesional: "Pengembangan Karir",
    lainnya: "Lainnya",
  };
  return map[purpose] || "Tidak dipilih";
}

/**
 * Countdown Timer — rolling 3-day deadline to create urgency
 */
function initCountdownTimer() {
  const container = document.getElementById('promoCountdown');
  if (!container) return;

  // Create a rolling deadline: always ~3 days from first visit
  const STORAGE_KEY = 'promo_deadline';
  let deadline;
  try {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored) {
      deadline = new Date(stored);
      // If deadline already passed, reset to new 3-day window
      if (deadline <= new Date()) {
        deadline = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000);
        localStorage.setItem(STORAGE_KEY, deadline.toISOString());
      }
    } else {
      deadline = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000);
      localStorage.setItem(STORAGE_KEY, deadline.toISOString());
    }
  } catch (e) {
    deadline = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000);
  }

  function updateTimer() {
    const now = new Date();
    const diff = Math.max(0, deadline - now);
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    const el = (id) => document.getElementById(id);
    if (el('countDays')) el('countDays').textContent = String(days).padStart(2, '0');
    if (el('countHours')) el('countHours').textContent = String(hours).padStart(2, '0');
    if (el('countMinutes')) el('countMinutes').textContent = String(minutes).padStart(2, '0');
    if (el('countSeconds')) el('countSeconds').textContent = String(seconds).padStart(2, '0');
  }

  updateTimer();
  setInterval(updateTimer, 1000);
}

/**
 * Sticky Bottom CTA Bar — appears after scrolling past hero
 */
function initStickyCta() {
  const bar = document.getElementById('stickyCta');
  const orderSection = document.getElementById('order');
  if (!bar) return;

  window.addEventListener('scroll', function () {
    const scrollY = window.pageYOffset || document.documentElement.scrollTop;
    const heroEnd = 600;
    // Hide when order section is visible
    const orderTop = orderSection ? orderSection.offsetTop - window.innerHeight : Infinity;
    const orderBottom = orderSection ? orderSection.offsetTop + orderSection.offsetHeight : Infinity;

    if (scrollY > heroEnd && (scrollY < orderTop || scrollY > orderBottom)) {
      bar.classList.add('visible');
    } else {
      bar.classList.remove('visible');
    }
  });
}

/**
 * Social Proof Notification Popup — shows fake recent purchases
 */
function initSocialProofPopup() {
  const popup = document.getElementById('socialProofPopup');
  if (!popup) return;

  const buyers = [
    { name: 'Budi S.', city: 'Jakarta', initial: 'B' },
    { name: 'Sari W.', city: 'Bandung', initial: 'S' },
    { name: 'Andi P.', city: 'Surabaya', initial: 'A' },
    { name: 'Dewi R.', city: 'Medan', initial: 'D' },
    { name: 'Rizky F.', city: 'Makassar', initial: 'R' },
    { name: 'Hendra L.', city: 'Semarang', initial: 'H' },
    { name: 'Putri M.', city: 'Yogyakarta', initial: 'P' },
    { name: 'Agus T.', city: 'Bali', initial: 'A' },
    { name: 'Lina K.', city: 'Palembang', initial: 'L' },
    { name: 'Farhan Z.', city: 'Bekasi', initial: 'F' },
  ];

  let index = Math.floor(Math.random() * buyers.length);

  function showNotification() {
    const buyer = buyers[index];
    const nameEl = document.getElementById('proofName');
    const cityEl = document.getElementById('proofCity');
    const avatarEl = document.getElementById('proofAvatar');
    if (nameEl) nameEl.textContent = buyer.name;
    if (cityEl) cityEl.textContent = buyer.city;
    if (avatarEl) avatarEl.textContent = buyer.initial;

    popup.classList.add('show');

    setTimeout(() => {
      popup.classList.remove('show');
      index = (index + 1) % buyers.length;
    }, 4000);
  }

  // First show after 15 seconds, then every 25-40 seconds (randomized)
  setTimeout(() => {
    showNotification();
    setInterval(() => {
      showNotification();
    }, 25000 + Math.random() * 15000);
  }, 15000);
}
