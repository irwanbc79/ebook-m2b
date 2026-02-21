/**
 * M2B Admin Panel - JavaScript
 * Payment Management Dashboard
 * Using localStorage for data persistence
 */

// ==================== CONFIG ====================
const ADMIN_CONFIG = {
    credentials: {
        username: 'admin',
        // In production, use proper auth — this is a demo
        password: 'admin123'
    },
    storageKeys: {
        orders: 'm2b_orders',
        session: 'm2b_admin_session'
    },
    perPage: 10,
    whatsappNumber: '6282261846811'
};

// ==================== STATE ====================
let currentPage = 1;
let filteredOrders = [];

// ==================== INIT ====================
document.addEventListener('DOMContentLoaded', function () {
    checkSession();
    setupLoginForm();
});

// ==================== AUTH ====================
function checkSession() {
    const session = localStorage.getItem(ADMIN_CONFIG.storageKeys.session);
    if (session) {
        try {
            const parsed = JSON.parse(session);
            const now = Date.now();
            // Session valid for 24 hours
            if (parsed.loggedIn && (now - parsed.timestamp) < 86400000) {
                showDashboard();
                return;
            }
        } catch (e) { }
    }
    showLogin();
}

function setupLoginForm() {
    const form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const username = document.getElementById('loginUsername').value.trim();
        const password = document.getElementById('loginPassword').value;

        if (username === ADMIN_CONFIG.credentials.username &&
            password === ADMIN_CONFIG.credentials.password) {
            localStorage.setItem(ADMIN_CONFIG.storageKeys.session, JSON.stringify({
                loggedIn: true,
                username: username,
                timestamp: Date.now()
            }));
            document.getElementById('loginError').style.display = 'none';
            showDashboard();
        } else {
            document.getElementById('loginError').style.display = 'block';
            document.getElementById('loginPassword').value = '';
        }
    });
}

function showLogin() {
    document.getElementById('loginScreen').style.display = 'flex';
    document.getElementById('adminDashboard').style.display = 'none';
}

function showDashboard() {
    document.getElementById('loginScreen').style.display = 'none';
    document.getElementById('adminDashboard').style.display = 'block';

    // Set admin user display
    const session = JSON.parse(localStorage.getItem(ADMIN_CONFIG.storageKeys.session) || '{}');
    const userDisplay = document.getElementById('adminUserDisplay');
    if (userDisplay && session.username) {
        userDisplay.textContent = `👤 ${session.username}`;
    }

    loadDashboard();
}

function handleLogout() {
    localStorage.removeItem(ADMIN_CONFIG.storageKeys.session);
    showLogin();
    showToast('Berhasil logout', 'success');
}

function togglePassword() {
    const input = document.getElementById('loginPassword');
    const btn = document.querySelector('.toggle-password');
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
    } else {
        input.type = 'password';
        btn.textContent = '👁️';
    }
}

// ==================== DASHBOARD ====================
function loadDashboard() {
    updateStats();
    filterOrders();
}

function getOrders() {
    try {
        const data = localStorage.getItem(ADMIN_CONFIG.storageKeys.orders);
        return data ? JSON.parse(data) : [];
    } catch (e) {
        return [];
    }
}

function saveOrders(orders) {
    localStorage.setItem(ADMIN_CONFIG.storageKeys.orders, JSON.stringify(orders));
}

function updateStats() {
    const orders = getOrders();
    const total = orders.length;
    const pending = orders.filter(o => o.status === 'pending').length;
    const paid = orders.filter(o => o.status === 'paid').length;
    const delivered = orders.filter(o => o.status === 'delivered').length;
    const revenue = orders.filter(o => o.status === 'paid' || o.status === 'delivered')
        .reduce((sum, o) => sum + (o.amount || 49000), 0);

    document.getElementById('statTotal').textContent = total;
    document.getElementById('statPending').textContent = pending;
    document.getElementById('statPaid').textContent = paid;
    document.getElementById('statDelivered').textContent = delivered;
    document.getElementById('statRevenue').textContent = formatCurrency(revenue);
}

// ==================== FILTER & SEARCH ====================
function filterOrders() {
    const orders = getOrders();
    const search = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
    const status = document.getElementById('filterStatus')?.value || 'all';

    filteredOrders = orders.filter(order => {
        const matchSearch = !search ||
            order.name?.toLowerCase().includes(search) ||
            order.email?.toLowerCase().includes(search) ||
            order.orderId?.toLowerCase().includes(search) ||
            order.phone?.toLowerCase().includes(search) ||
            order.city?.toLowerCase().includes(search);

        const matchStatus = status === 'all' || order.status === status;

        return matchSearch && matchStatus;
    });

    // Sort by date (newest first)
    filteredOrders.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));

    currentPage = 1;
    renderOrdersTable();
}

// ==================== RENDER TABLE ====================
function renderOrdersTable() {
    const tbody = document.getElementById('ordersBody');
    const emptyState = document.getElementById('emptyState');
    const table = document.querySelector('.orders-table');

    if (!tbody) return;

    if (filteredOrders.length === 0) {
        table.style.display = 'none';
        emptyState.style.display = 'block';
        document.getElementById('pagination').innerHTML = '';
        return;
    }

    table.style.display = 'table';
    emptyState.style.display = 'none';

    // Pagination
    const totalPages = Math.ceil(filteredOrders.length / ADMIN_CONFIG.perPage);
    const start = (currentPage - 1) * ADMIN_CONFIG.perPage;
    const end = start + ADMIN_CONFIG.perPage;
    const pageOrders = filteredOrders.slice(start, end);

    tbody.innerHTML = pageOrders.map(order => `
        <tr>
            <td><span class="order-id">${escapeHtml(order.orderId)}</span></td>
            <td><span class="order-date">${formatDate(order.createdAt)}</span></td>
            <td><span class="order-name">${escapeHtml(order.name)}</span></td>
            <td><span class="order-email">${escapeHtml(order.email)}</span></td>
            <td><span class="order-phone">${escapeHtml(order.phone)}</span></td>
            <td>${escapeHtml(order.city || '-')}</td>
            <td>${escapeHtml(order.purpose || '-')}</td>
            <td><span class="status-badge status-${order.status}">${getStatusLabel(order.status)}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn btn-view" title="Lihat Detail" onclick="viewOrder('${order.orderId}')">👁️</button>
                    ${order.status === 'pending' ? `<button class="action-btn btn-confirm" title="Konfirmasi Bayar" onclick="updateOrderStatus('${order.orderId}', 'paid')">✅</button>` : ''}
                    ${order.status === 'paid' ? `<button class="action-btn btn-confirm" title="Tandai Terkirim" onclick="updateOrderStatus('${order.orderId}', 'delivered')">📧</button>` : ''}
                    <button class="action-btn btn-delete" title="Hapus" onclick="deleteOrder('${order.orderId}')">🗑️</button>
                </div>
            </td>
        </tr>
    `).join('');

    renderPagination(totalPages);
}

function renderPagination(totalPages) {
    const container = document.getElementById('pagination');
    if (!container || totalPages <= 1) {
        if (container) container.innerHTML = '';
        return;
    }

    let html = '';
    if (currentPage > 1) {
        html += `<button class="page-btn" onclick="goToPage(${currentPage - 1})">‹</button>`;
    }

    for (let i = 1; i <= totalPages; i++) {
        if (totalPages > 7 && i > 2 && i < totalPages - 1 && Math.abs(i - currentPage) > 1) {
            if (i === 3 || i === totalPages - 2) html += `<span style="padding: 0 4px; color: #9ca3af;">...</span>`;
            continue;
        }
        html += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
    }

    if (currentPage < totalPages) {
        html += `<button class="page-btn" onclick="goToPage(${currentPage + 1})">›</button>`;
    }

    container.innerHTML = html;
}

function goToPage(page) {
    currentPage = page;
    renderOrdersTable();
    // Scroll to top of table
    document.querySelector('.toolbar')?.scrollIntoView({ behavior: 'smooth' });
}

// ==================== ORDER ACTIONS ====================
function viewOrder(orderId) {
    const orders = getOrders();
    const order = orders.find(o => o.orderId === orderId);
    if (!order) return;

    const modalBody = document.getElementById('modalBody');
    const modalFooter = document.getElementById('modalFooter');

    modalBody.innerHTML = `
        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Order ID</span>
                <span class="detail-value"><span class="order-id">${escapeHtml(order.orderId)}</span></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Tanggal</span>
                <span class="detail-value">${formatDate(order.createdAt)}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Nama</span>
                <span class="detail-value">${escapeHtml(order.name)}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Email</span>
                <span class="detail-value"><a href="mailto:${escapeHtml(order.email)}">${escapeHtml(order.email)}</a></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">WhatsApp</span>
                <span class="detail-value"><a href="https://wa.me/${order.phone?.replace(/[^0-9]/g, '')}" target="_blank">${escapeHtml(order.phone)}</a></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Kota</span>
                <span class="detail-value">${escapeHtml(order.city || '-')}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Tujuan Pembelian</span>
                <span class="detail-value">${escapeHtml(formatPurpose(order.purpose))}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Status</span>
                <span class="detail-value"><span class="status-badge status-${order.status}">${getStatusLabel(order.status)}</span></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Jumlah</span>
                <span class="detail-value" style="font-weight:700; color: var(--primary);">${formatCurrency(order.amount || 49000)}</span>
            </div>
            ${order.updatedAt ? `
            <div class="detail-item">
                <span class="detail-label">Terakhir Update</span>
                <span class="detail-value">${formatDate(order.updatedAt)}</span>
            </div>` : ''}
        </div>
    `;

    let footerHtml = '';
    if (order.status === 'pending') {
        footerHtml += `<button class="btn btn-sm btn-success" onclick="updateOrderStatus('${order.orderId}', 'paid'); closeModal();">✅ Konfirmasi Bayar</button>`;
        footerHtml += `<button class="btn btn-sm btn-danger" onclick="updateOrderStatus('${order.orderId}', 'cancelled'); closeModal();">❌ Batalkan</button>`;
    }
    if (order.status === 'paid') {
        footerHtml += `<button class="btn btn-sm btn-info" onclick="updateOrderStatus('${order.orderId}', 'delivered'); closeModal();">📧 Tandai Terkirim</button>`;
    }
    footerHtml += `<button class="btn btn-sm btn-outline" onclick="sendWhatsApp('${order.orderId}')">💬 WhatsApp</button>`;
    footerHtml += `<button class="btn btn-sm btn-outline" onclick="closeModal()">Tutup</button>`;

    modalFooter.innerHTML = footerHtml;
    document.getElementById('orderModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('orderModal').style.display = 'none';
}

function updateOrderStatus(orderId, newStatus) {
    const orders = getOrders();
    const index = orders.findIndex(o => o.orderId === orderId);
    if (index === -1) return;

    orders[index].status = newStatus;
    orders[index].updatedAt = new Date().toISOString();
    saveOrders(orders);
    updateStats();
    filterOrders();

    const statusLabels = {
        pending: 'Menunggu Bayar',
        paid: 'Sudah Bayar',
        delivered: 'Terkirim',
        cancelled: 'Dibatalkan'
    };
    showToast(`Pesanan ${orderId} diupdate ke "${statusLabels[newStatus]}"`, 'success');
}

function deleteOrder(orderId) {
    if (!confirm(`Hapus pesanan ${orderId}? Tindakan ini tidak bisa dibatalkan.`)) return;

    const orders = getOrders();
    const filtered = orders.filter(o => o.orderId !== orderId);
    saveOrders(filtered);
    updateStats();
    filterOrders();
    showToast(`Pesanan ${orderId} telah dihapus`, 'warning');
}

function sendWhatsApp(orderId) {
    const orders = getOrders();
    const order = orders.find(o => o.orderId === orderId);
    if (!order) return;

    const phone = order.phone?.replace(/[^0-9]/g, '');
    const waPhone = phone.startsWith('0') ? '62' + phone.substring(1) : phone;
    const text = encodeURIComponent(
        `Halo ${order.name},\n\nTerima kasih telah memesan E-book Panduan Ekspor Impor M2B v2.0.\n\n` +
        `Order ID: ${order.orderId}\nTotal: Rp 49.000\n\n` +
        `Silakan transfer ke:\nBCA: 8280424243\nA/n Eka Mayang Sari Harahap\n\n` +
        `Kirim bukti transfer ke chat ini ya.\nTerima kasih! 🙏`
    );

    window.open(`https://wa.me/${waPhone}?text=${text}`, '_blank');
}

// ==================== EXPORT CSV ====================
function exportCSV() {
    const orders = filteredOrders.length > 0 ? filteredOrders : getOrders();
    if (orders.length === 0) {
        showToast('Tidak ada data untuk di-export', 'warning');
        return;
    }

    const headers = ['Order ID', 'Tanggal', 'Nama', 'Email', 'WhatsApp', 'Kota', 'Tujuan', 'Status', 'Jumlah'];
    const rows = orders.map(o => [
        o.orderId,
        formatDate(o.createdAt),
        o.name,
        o.email,
        o.phone,
        o.city || '',
        formatPurpose(o.purpose),
        getStatusLabel(o.status),
        o.amount || 49000
    ]);

    let csv = '\uFEFF'; // BOM for Excel
    csv += headers.join(',') + '\n';
    csv += rows.map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')).join('\n');

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `m2b_orders_${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();
    URL.revokeObjectURL(link.href);

    showToast(`${orders.length} pesanan berhasil di-export`, 'success');
}

// ==================== TOAST ====================
function showToast(message, type = 'info') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<span>${icons[type] || 'ℹ️'}</span> ${escapeHtml(message)}`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'toastOut 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// ==================== HELPERS ====================
function generateOrderId() {
    const date = new Date();
    const dateStr = date.getFullYear().toString() +
        String(date.getMonth() + 1).padStart(2, '0') +
        String(date.getDate()).padStart(2, '0');
    const random = Math.random().toString(36).substring(2, 8).toUpperCase();
    return `M2B-${dateStr}-${random}`;
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    } catch (e) {
        return dateStr;
    }
}

function formatCurrency(amount) {
    return 'Rp ' + Number(amount).toLocaleString('id-ID');
}

function getStatusLabel(status) {
    const labels = {
        pending: '⏳ Menunggu Bayar',
        paid: '✅ Sudah Bayar',
        delivered: '📧 Terkirim',
        cancelled: '❌ Dibatalkan'
    };
    return labels[status] || status;
}

function formatPurpose(purpose) {
    const map = {
        bisnis: 'Memulai Bisnis Ekspor/Impor',
        umkm: 'Scale Up UMKM ke Pasar Global',
        belajar: 'Belajar / Riset',
        profesional: 'Pengembangan Karir Profesional',
        lainnya: 'Lainnya'
    };
    return map[purpose] || purpose || '-';
}

function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Close modal on outside click
document.addEventListener('click', function (e) {
    if (e.target.id === 'orderModal') {
        closeModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
