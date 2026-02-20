/**
 * M2B E-book Landing Page - JavaScript
 * Updated: January 2026 - Price: Rp 49.000
 * WhatsApp: +62 822 6184 6811
 */

// Configuration
const CONFIG = {
    whatsappNumber: '6282261846811',
    ebookPrice: 49000,
    bankName: 'BCA',
    bankAccount: '8280424243',
    bankHolder: 'Eka Mayang Sari Harahap',
    apiEndpoint: 'api/process_order.php'
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initOrderForm();
    initSmoothScroll();
    initNavbarScroll();
});

/**
 * Initialize Order Form
 */
function initOrderForm() {
    const form = document.getElementById('orderForm');
    if (!form) return;

    form.addEventListener('submit', handleFormSubmit);

    // Real-time validation
    const inputs = form.querySelectorAll('input[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', validateInput);
        input.addEventListener('input', clearError);
    });
}

/**
 * Handle Form Submission
 */
async function handleFormSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');

    // Validate form
    if (!validateForm(form)) {
        return;
    }

    // Get form data
    const formData = {
        name: document.getElementById('buyerName').value.trim(),
        email: document.getElementById('buyerEmail').value.trim(),
        whatsapp: cleanWhatsAppNumber(document.getElementById('buyerWhatsapp').value),
        city: document.getElementById('buyerCity').value.trim(),
        purpose: document.getElementById('buyerPurpose').value.trim()
    };

    // Show loading state
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline';

    try {
        // Try to submit to API
        const apiResult = await submitToAPI(formData);

        if (apiResult && apiResult.success) {
            // API success - use returned order ID
            showSuccess(`Order berhasil dibuat!\nOrder ID: ${apiResult.order_id}`);
            redirectToWhatsApp(formData, apiResult.order_id);
        } else {
            // API failed or not available - generate local order ID
            const orderId = generateOrderId();
            showSuccess(`Order berhasil dibuat!\nOrder ID: ${orderId}`);
            redirectToWhatsApp(formData, orderId);
        }

        // Reset form
        form.reset();

    } catch (error) {
        console.error('Form submission error:', error);
        // Still redirect to WhatsApp even if API fails
        const orderId = generateOrderId();
        showSuccess(`Order berhasil dibuat!\nOrder ID: ${orderId}`);
        redirectToWhatsApp(formData, orderId);
        form.reset();
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
    }
}

/**
 * Submit to Backend API
 */
async function submitToAPI(formData) {
    try {
        const response = await fetch(CONFIG.apiEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        if (!response.ok) {
            throw new Error('API request failed');
        }

        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return null;
    }
}

/**
 * Generate Order ID
 */
function generateOrderId() {
    const date = new Date();
    const dateStr = date.getFullYear().toString() +
                    (date.getMonth() + 1).toString().padStart(2, '0') +
                    date.getDate().toString().padStart(2, '0');
    const random = Math.random().toString(36).substring(2, 8).toUpperCase();
    return `M2B-${dateStr}-${random}`;
}

/**
 * Clean WhatsApp Number
 */
function cleanWhatsAppNumber(number) {
    // Remove all non-digits
    let cleaned = number.replace(/\D/g, '');

    // Convert 08xx to 628xx
    if (cleaned.startsWith('08')) {
        cleaned = '62' + cleaned.substring(1);
    }

    // Add 62 if not present
    if (!cleaned.startsWith('62')) {
        cleaned = '62' + cleaned;
    }

    return cleaned;
}

/**
 * Redirect to WhatsApp
 */
function redirectToWhatsApp(formData, orderId) {
    const message = buildWhatsAppMessage(formData, orderId);
    const waUrl = `https://wa.me/${CONFIG.whatsappNumber}?text=${encodeURIComponent(message)}`;

    // Open WhatsApp in new tab
    window.open(waUrl, '_blank');
}

/**
 * Build WhatsApp Message
 */
function buildWhatsAppMessage(formData, orderId) {
    return `🎉 *PESANAN E-BOOK M2B*

📋 *Order ID:* ${orderId}

👤 *Data Pembeli:*
• Nama: ${formData.name}
• Email: ${formData.email}
• WhatsApp: ${formData.whatsapp}
• Kota: ${formData.city}
• Tujuan: ${formData.purpose || '-'}

💳 *Informasi Pembayaran:*
• Bank: ${CONFIG.bankName}
• No. Rekening: ${CONFIG.bankAccount}
• A.N.: ${CONFIG.bankHolder}
• Jumlah: Rp ${CONFIG.ebookPrice.toLocaleString('id-ID')}

📌 *Langkah Selanjutnya:*
1. Transfer sejumlah Rp ${CONFIG.ebookPrice.toLocaleString('id-ID')}
2. Kirim bukti transfer + Order ID ini
3. E-book akan dikirim dalam 2 jam setelah verifikasi

Terima kasih! 🙏`;
}

/**
 * Validate Form
 */
function validateForm(form) {
    let isValid = true;
    const requiredInputs = form.querySelectorAll('input[required]');

    requiredInputs.forEach(input => {
        if (!validateInput({ target: input })) {
            isValid = false;
        }
    });

    return isValid;
}

/**
 * Validate Single Input
 */
function validateInput(e) {
    const input = e.target;
    const value = input.value.trim();
    let isValid = true;
    let errorMessage = '';

    // Check required
    if (input.required && !value) {
        isValid = false;
        errorMessage = 'Field ini wajib diisi';
    }

    // Check email format
    if (input.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Format email tidak valid';
        }
    }

    // Check phone format
    if (input.type === 'tel' && value) {
        const phoneRegex = /^[0-9+\-\s()]{10,15}$/;
        if (!phoneRegex.test(value)) {
            isValid = false;
            errorMessage = 'Format nomor telepon tidak valid';
        }
    }

    // Show/hide error
    if (!isValid) {
        showInputError(input, errorMessage);
    } else {
        clearInputError(input);
    }

    return isValid;
}

/**
 * Show Input Error
 */
function showInputError(input, message) {
    input.style.borderColor = '#ef4444';

    // Remove existing error message
    const existingError = input.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }

    // Add error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.cssText = 'color: #ef4444; font-size: 12px; margin-top: 4px;';
    errorDiv.textContent = message;
    input.parentNode.appendChild(errorDiv);
}

/**
 * Clear Input Error
 */
function clearInputError(input) {
    input.style.borderColor = '';
    const errorDiv = input.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
}

/**
 * Clear Error on Input
 */
function clearError(e) {
    const input = e.target;
    if (input.value.trim()) {
        clearInputError(input);
    }
}

/**
 * Show Success Message
 */
function showSuccess(message) {
    alert(message);
}

/**
 * Initialize Smooth Scroll
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);

            if (target) {
                const navbarHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = target.offsetTop - navbarHeight - 20;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * Initialize Navbar Scroll Effect
 */
function initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    let lastScrollTop = 0;

    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Add shadow on scroll
        if (scrollTop > 50) {
            navbar.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
        }

        lastScrollTop = scrollTop;
    });
}

/**
 * Toggle FAQ
 */
function toggleFaq(button) {
    const faqItem = button.parentElement;
    const isActive = faqItem.classList.contains('active');

    // Close all FAQ items
    document.querySelectorAll('.faq-item').forEach(item => {
        item.classList.remove('active');
    });

    // Toggle current item
    if (!isActive) {
        faqItem.classList.add('active');
    }
}

/**
 * Copy to Clipboard
 */
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showSuccess('Berhasil disalin!');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showSuccess('Berhasil disalin!');
    }
}
