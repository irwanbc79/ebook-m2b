/**
 * M2B Admin Panel v2.0 — Database-Driven
 * Full integration with MySQL backend + One-Click Verify & Deliver
 * Updated: February 2026
 */

// ==================== CONFIG ====================
const API = {
  login: "api/admin_login.php",
  orders: "api/admin_orders.php",
  verify: "api/verify_payment.php",
};

// ==================== STATE ====================
let apiKey = "";
let allOrders = [];
let currentPage = 1;
const perPage = 15;
let searchTimer = null;

// ==================== INIT ====================
document.addEventListener("DOMContentLoaded", function () {
  checkSession();
  setupLoginForm();
});

// ==================== AUTH ====================
function checkSession() {
  const session = sessionStorage.getItem("m2b_admin");
  if (session) {
    try {
      const parsed = JSON.parse(session);
      if (parsed.api_key && Date.now() - parsed.ts < 86400000) {
        apiKey = parsed.api_key;
        showDashboard();
        return;
      }
    } catch (e) {
      /* expired */
    }
  }
  showLogin();
}

function setupLoginForm() {
  const form = document.getElementById("loginForm");
  if (!form) return;

  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    const username = document.getElementById("loginUsername").value.trim();
    const password = document.getElementById("loginPassword").value;
    const errEl = document.getElementById("loginError");
    const btn = form.querySelector('button[type="submit"]');

    btn.disabled = true;
    btn.textContent = "Memproses...";

    try {
      const res = await fetch(API.login, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password }),
      });
      const data = await res.json();

      if (data.success) {
        apiKey = data.api_key;
        sessionStorage.setItem(
          "m2b_admin",
          JSON.stringify({
            api_key: data.api_key,
            username: username,
            ts: Date.now(),
          }),
        );
        errEl.style.display = "none";
        showDashboard();
      } else {
        errEl.style.display = "block";
        errEl.textContent = data.message || "Login gagal";
      }
    } catch (err) {
      errEl.style.display = "block";
      errEl.textContent = "Gagal terhubung ke server";
    }

    btn.disabled = false;
    btn.textContent = "Masuk";
  });
}

function showLogin() {
  document.getElementById("loginScreen").style.display = "flex";
  document.getElementById("adminDashboard").style.display = "none";
}

function showDashboard() {
  document.getElementById("loginScreen").style.display = "none";
  document.getElementById("adminDashboard").style.display = "block";

  const session = JSON.parse(sessionStorage.getItem("m2b_admin") || "{}");
  const userDisplay = document.getElementById("adminUserDisplay");
  if (userDisplay)
    userDisplay.textContent = `👤 ${session.username || "Admin"}`;

  loadOrders();
}

function handleLogout() {
  sessionStorage.removeItem("m2b_admin");
  apiKey = "";
  showLogin();
  showToast("Berhasil logout", "success");
}

function togglePassword() {
  const input = document.getElementById("loginPassword");
  const btn = document.querySelector(".toggle-password");
  if (input.type === "password") {
    input.type = "text";
    btn.textContent = "🙈";
  } else {
    input.type = "password";
    btn.textContent = "👁️";
  }
}

// ==================== API HELPERS ====================
async function apiFetch(url, options = {}) {
  const defaults = {
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${apiKey}`,
    },
  };
  const merged = {
    ...defaults,
    ...options,
    headers: { ...defaults.headers, ...(options.headers || {}) },
  };
  const res = await fetch(url, merged);

  if (res.status === 401) {
    showToast("Sesi expired, silakan login ulang", "error");
    handleLogout();
    throw new Error("Unauthorized");
  }
  return res.json();
}

// ==================== LOAD ORDERS ====================
async function loadOrders() {
  const status = document.getElementById("filterStatus")?.value || "all";
  const search = document.getElementById("searchInput")?.value?.trim() || "";

  let url = `${API.orders}?page=${currentPage}&per_page=${perPage}`;
  if (status !== "all") url += `&status=${status}`;
  if (search) url += `&search=${encodeURIComponent(search)}`;

  // Show loading
  const tbody = document.getElementById("ordersBody");
  if (tbody) {
    tbody.innerHTML =
      '<tr><td colspan="9" style="text-align:center;padding:40px;color:#9ca3af;">⏳ Memuat data...</td></tr>';
  }

  try {
    const data = await apiFetch(url);
    if (data.success) {
      allOrders = data.orders;
      updateStats(data.stats);
      renderOrdersTable(data.orders, data.pagination);
    } else {
      showToast("Gagal memuat data: " + (data.message || ""), "error");
    }
  } catch (err) {
    if (err.message !== "Unauthorized") {
      showToast("Gagal terhubung ke server", "error");
      console.error(err);
    }
  }
}

function debounceSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    currentPage = 1;
    loadOrders();
  }, 400);
}

// ==================== STATS ====================
function updateStats(stats) {
  if (!stats) return;
  document.getElementById("statTotal").textContent = stats.total || 0;
  document.getElementById("statPending").textContent = stats.pending || 0;
  document.getElementById("statPaid").textContent = stats.verified || 0;
  document.getElementById("statDelivered").textContent = stats.failed || 0;
  document.getElementById("statRevenue").textContent = formatCurrency(
    stats.revenue || 0,
  );
}

// ==================== RENDER TABLE ====================
function renderOrdersTable(orders, pagination) {
  const tbody = document.getElementById("ordersBody");
  const emptyState = document.getElementById("emptyState");
  const table = document.querySelector(".orders-table");

  if (!tbody) return;

  if (!orders || orders.length === 0) {
    table.style.display = "none";
    emptyState.style.display = "block";
    document.getElementById("pagination").innerHTML = "";
    return;
  }

  table.style.display = "table";
  emptyState.style.display = "none";

  tbody.innerHTML = orders
    .map(
      (order) => `
        <tr>
            <td><span class="order-id">${esc(order.order_id)}</span></td>
            <td><span class="order-date">${formatDate(order.created_at)}</span></td>
            <td><span class="order-name">${esc(order.buyer_name)}</span></td>
            <td><span class="order-email">${esc(order.buyer_email)}</span></td>
            <td><span class="order-phone">${esc(order.buyer_whatsapp)}</span></td>
            <td>${esc(order.buyer_city || "-")}</td>
            <td>${esc(formatPurpose(order.buyer_purpose))}</td>
            <td><span class="status-badge status-${order.payment_status}">${getStatusLabel(order.payment_status)}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn btn-view" title="Detail" onclick="viewOrder('${order.order_id}')">👁️</button>
                    ${
                      order.payment_status === "pending"
                        ? `
                        <button class="action-btn btn-confirm" title="✅ Verifikasi & Kirim E-book" onclick="verifyAndDeliver('${order.order_id}')">🚀</button>
                        <button class="action-btn btn-delete" title="Tolak" onclick="rejectOrder('${order.order_id}')">❌</button>
                    `
                        : ""
                    }
                    ${
                      order.payment_status === "verified"
                        ? `
                        <button class="action-btn btn-confirm" title="Kirim WhatsApp" onclick="sendDeliveryWA('${order.order_id}')">💬</button>
                    `
                        : ""
                    }
                    <button class="action-btn btn-delete" title="Hapus" onclick="deleteOrder('${order.order_id}')">🗑️</button>
                </div>
            </td>
        </tr>
    `,
    )
    .join("");

  renderPagination(pagination);
}

function renderPagination(pag) {
  const container = document.getElementById("pagination");
  if (!container || !pag || pag.total_pages <= 1) {
    if (container) container.innerHTML = "";
    return;
  }

  let html = "";
  if (currentPage > 1)
    html += `<button class="page-btn" onclick="goToPage(${currentPage - 1})">‹</button>`;

  for (let i = 1; i <= pag.total_pages; i++) {
    if (
      pag.total_pages > 7 &&
      i > 2 &&
      i < pag.total_pages - 1 &&
      Math.abs(i - currentPage) > 1
    ) {
      if (i === 3 || i === pag.total_pages - 2)
        html += `<span style="padding:0 4px;color:#9ca3af;">...</span>`;
      continue;
    }
    html += `<button class="page-btn ${i === currentPage ? "active" : ""}" onclick="goToPage(${i})">${i}</button>`;
  }

  if (currentPage < pag.total_pages)
    html += `<button class="page-btn" onclick="goToPage(${currentPage + 1})">›</button>`;

  container.innerHTML = html;
}

function goToPage(page) {
  currentPage = page;
  loadOrders();
}

// ==================== VERIFY & DELIVER (One-Click!) ====================
async function verifyAndDeliver(orderId) {
  if (
    !confirm(
      `Verifikasi pembayaran & kirim e-book untuk ${orderId}?\n\n` +
        `Ini akan:\n` +
        `• Mark sebagai terverifikasi di database\n` +
        `• Kirim email otomatis dengan link download\n` +
        `• Buka WhatsApp notifikasi ke pembeli`,
    )
  ) {
    return;
  }

  showToast("⏳ Memproses verifikasi...", "info");

  try {
    const data = await apiFetch(API.verify, {
      method: "POST",
      body: JSON.stringify({ order_id: orderId, action: "approve" }),
    });

    if (data.success) {
      showToast(
        `✅ ${orderId} terverifikasi! Email terkirim: ${data.email_sent ? "Ya" : "Belum (cek manual)"}`,
        "success",
      );

      // Auto-open WhatsApp to buyer with delivery message
      if (data.whatsapp_url) {
        setTimeout(() => window.open(data.whatsapp_url, "_blank"), 800);
      }

      // Refresh table
      loadOrders();
    } else {
      showToast(`❌ Gagal: ${data.message}`, "error");
    }
  } catch (err) {
    showToast("❌ Gagal terhubung ke server", "error");
    console.error(err);
  }
}

async function rejectOrder(orderId) {
  if (!confirm(`Tolak pembayaran ${orderId}?`)) return;

  try {
    const data = await apiFetch(API.verify, {
      method: "POST",
      body: JSON.stringify({ order_id: orderId, action: "reject" }),
    });

    if (data.success) {
      showToast(`Pesanan ${orderId} ditolak`, "warning");
      loadOrders();
    } else {
      showToast(`Gagal: ${data.message}`, "error");
    }
  } catch (err) {
    showToast("Gagal terhubung ke server", "error");
  }
}

// ==================== VIEW ORDER DETAIL ====================
function viewOrder(orderId) {
  const order = allOrders.find((o) => o.order_id === orderId);
  if (!order) return;

  const modalBody = document.getElementById("modalBody");
  const modalFooter = document.getElementById("modalFooter");

  const emailBadge = order.email_sent
    ? '<span style="color:#10b981;font-weight:600;">✅ Email terkirim</span>'
    : '<span style="color:#f59e0b;">⏳ Belum terkirim</span>';

  modalBody.innerHTML = `
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Order ID</span>
                <span class="detail-value"><span class="order-id">${esc(order.order_id)}</span></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Tanggal Order</span>
                <span class="detail-value">${formatDate(order.created_at)}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Nama</span>
                <span class="detail-value">${esc(order.buyer_name)}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Email</span>
                <span class="detail-value"><a href="mailto:${esc(order.buyer_email)}">${esc(order.buyer_email)}</a></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">WhatsApp</span>
                <span class="detail-value"><a href="https://wa.me/${order.buyer_whatsapp}" target="_blank">${esc(order.buyer_whatsapp)}</a></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Kota</span>
                <span class="detail-value">${esc(order.buyer_city || "-")}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Tujuan</span>
                <span class="detail-value">${esc(formatPurpose(order.buyer_purpose))}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Status</span>
                <span class="detail-value"><span class="status-badge status-${order.payment_status}">${getStatusLabel(order.payment_status)}</span></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Email Delivery</span>
                <span class="detail-value">${emailBadge}</span>
            </div>
            ${
              order.verified_at
                ? `
            <div class="detail-item">
                <span class="detail-label">Diverifikasi</span>
                <span class="detail-value">${formatDate(order.verified_at)}</span>
            </div>`
                : ""
            }
            ${
              order.ebook_url
                ? `
            <div class="detail-item" style="grid-column:1/-1;">
                <span class="detail-label">Download URL</span>
                <span class="detail-value"><a href="${esc(order.ebook_url)}" target="_blank">${esc(order.ebook_url)}</a></span>
            </div>`
                : ""
            }
            ${
              order.notes
                ? `
            <div class="detail-item" style="grid-column:1/-1;">
                <span class="detail-label">Catatan</span>
                <span class="detail-value" style="white-space:pre-wrap;">${esc(order.notes)}</span>
            </div>`
                : ""
            }
        </div>
    `;

  let footerHtml = "";
  if (order.payment_status === "pending") {
    footerHtml += `<button class="btn btn-sm btn-success" onclick="verifyAndDeliver('${order.order_id}'); closeModal();">🚀 Verifikasi & Kirim</button>`;
    footerHtml += `<button class="btn btn-sm btn-danger" onclick="rejectOrder('${order.order_id}'); closeModal();">❌ Tolak</button>`;
  }
  if (order.payment_status === "verified") {
    footerHtml += `<button class="btn btn-sm btn-info" onclick="sendDeliveryWA('${order.order_id}')">💬 Kirim WhatsApp</button>`;
  }
  footerHtml += `<button class="btn btn-sm btn-outline" onclick="addNote('${order.order_id}')">📝 Catatan</button>`;
  footerHtml += `<button class="btn btn-sm btn-outline" onclick="closeModal()">Tutup</button>`;

  modalFooter.innerHTML = footerHtml;
  document.getElementById("orderModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("orderModal").style.display = "none";
}

// ==================== ORDER ACTIONS ====================
function sendDeliveryWA(orderId) {
  const order = allOrders.find((o) => o.order_id === orderId);
  if (!order) return;

  const waPhone = order.buyer_whatsapp;
  const ebookUrl = order.ebook_url || "https://ebook.m2b.co.id";
  const text = encodeURIComponent(
    `✅ *PEMBAYARAN TERVERIFIKASI*\n\n` +
      `Halo ${order.buyer_name},\n\n` +
      `Terima kasih! Pembayaran Anda untuk:\n` +
      `📋 Order ID: *${order.order_id}*\n\n` +
      `telah kami verifikasi.\n\n` +
      `📥 *Download E-book:*\n${ebookUrl}\n\n` +
      `Jika ada pertanyaan, hubungi kami via:\n` +
      `📧 ebook@m2b.co.id\n` +
      `💬 https://t.me/+vLwFWh-xg54wMzNl\n\n` +
      `Selamat belajar! 📚🚀`,
  );
  window.open(`https://wa.me/${waPhone}?text=${text}`, "_blank");
}

async function addNote(orderId) {
  const note = prompt("Tambahkan catatan untuk pesanan ini:");
  if (!note) return;

  try {
    const data = await apiFetch(API.orders, {
      method: "POST",
      body: JSON.stringify({ action: "add_note", order_id: orderId, note }),
    });
    if (data.success) {
      showToast("Catatan ditambahkan", "success");
      closeModal();
      loadOrders();
    }
  } catch (err) {
    showToast("Gagal menambah catatan", "error");
  }
}

async function deleteOrder(orderId) {
  if (!confirm(`Hapus pesanan ${orderId}? Tindakan ini tidak bisa dibatalkan.`))
    return;

  try {
    const data = await apiFetch(API.orders, {
      method: "POST",
      body: JSON.stringify({ action: "delete", order_id: orderId }),
    });
    if (data.success) {
      showToast(`Pesanan ${orderId} dihapus`, "warning");
      loadOrders();
    }
  } catch (err) {
    showToast("Gagal menghapus", "error");
  }
}

// ==================== EXPORT CSV ====================
function exportCSV() {
  if (allOrders.length === 0) {
    showToast("Tidak ada data untuk di-export", "warning");
    return;
  }

  const headers = [
    "Order ID",
    "Tanggal",
    "Nama",
    "Email",
    "WhatsApp",
    "Kota",
    "Tujuan",
    "Status",
    "Email Sent",
    "Verified At",
  ];
  const rows = allOrders.map((o) => [
    o.order_id,
    formatDate(o.created_at),
    o.buyer_name,
    o.buyer_email,
    o.buyer_whatsapp,
    o.buyer_city || "",
    formatPurpose(o.buyer_purpose),
    getStatusLabel(o.payment_status),
    o.email_sent ? "Ya" : "Tidak",
    o.verified_at || "-",
  ]);

  let csv = "\uFEFF";
  csv += headers.join(",") + "\n";
  csv += rows
    .map((row) =>
      row.map((cell) => `"${String(cell).replace(/"/g, '""')}"`).join(","),
    )
    .join("\n");

  const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = `m2b_orders_${new Date().toISOString().slice(0, 10)}.csv`;
  link.click();
  URL.revokeObjectURL(link.href);

  showToast(`${allOrders.length} pesanan berhasil di-export`, "success");
}

// ==================== TOAST ====================
function showToast(message, type = "info") {
  let container = document.querySelector(".toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container";
    document.body.appendChild(container);
  }

  const icons = { success: "✅", error: "❌", warning: "⚠️", info: "ℹ️" };
  const toast = document.createElement("div");
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `<span>${icons[type] || "ℹ️"}</span> ${esc(message)}`;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = "toastOut 0.3s ease forwards";
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}

// ==================== HELPERS ====================
function formatDate(dateStr) {
  if (!dateStr) return "-";
  try {
    const d = new Date(dateStr);
    return d.toLocaleDateString("id-ID", {
      day: "2-digit",
      month: "short",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  } catch (e) {
    return dateStr;
  }
}

function formatCurrency(amount) {
  return "Rp " + Number(amount).toLocaleString("id-ID");
}

function getStatusLabel(status) {
  const labels = {
    pending: "⏳ Menunggu",
    verified: "✅ Verified",
    failed: "❌ Ditolak",
  };
  return labels[status] || status;
}

function formatPurpose(purpose) {
  const map = {
    bisnis: "Bisnis Ekspor/Impor",
    umkm: "Scale Up UMKM",
    belajar: "Belajar / Riset",
    profesional: "Pengembangan Karir",
    lainnya: "Lainnya",
  };
  return map[purpose] || purpose || "-";
}

function esc(str) {
  if (!str) return "";
  const div = document.createElement("div");
  div.textContent = str;
  return div.innerHTML;
}

// ── Modal & keyboard shortcuts ──
document.addEventListener("click", (e) => {
  if (e.target.id === "orderModal") closeModal();
});
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") closeModal();
});
