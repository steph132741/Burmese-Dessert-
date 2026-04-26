document.querySelectorAll('.animate').forEach((el, index) => {
    el.style.animationDelay = `${index * 120}ms`;
});

const toastEl = document.getElementById('toast');
function showToast(message) {
    if (!toastEl) {
        return;
    }
    toastEl.textContent = message;
    toastEl.classList.add('show');
    setTimeout(() => toastEl.classList.remove('show'), 5000);
}

document.querySelectorAll('.flash').forEach((flash) => {
    setTimeout(() => {
        flash.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        flash.style.opacity = '0';
        flash.style.transform = 'translateY(-6px)';
        setTimeout(() => flash.remove(), 350);
    }, 5000);
});

const navToggle = document.querySelector('.nav-toggle');
const navLinks = document.querySelector('.nav-links');

if (navToggle && navLinks) {
    const closeNav = () => {
        navToggle.setAttribute('aria-expanded', 'false');
        navLinks.classList.remove('is-open');
    };

    navToggle.addEventListener('click', () => {
        const isOpen = navToggle.getAttribute('aria-expanded') === 'true';
        navToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        navLinks.classList.toggle('is-open', !isOpen);
    });

    navLinks.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 760) {
                closeNav();
            }
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 760) {
            closeNav();
        }
    });
}

document.querySelectorAll('form.add-to-cart').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(form);
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await response.json();
            if (data && data.success) {
                const cartPill = document.querySelector('.cart-pill span');
                if (cartPill) {
                    cartPill.textContent = data.cart_count;
                }
                showToast(data.message || 'Added to cart.');
            } else {
                showToast('Could not add to cart.');
            }
        } catch (e) {
            showToast('Could not add to cart.');
        }
    });
});

const methodInputs = document.querySelectorAll('input[name="delivery_method"]');
const feeEl = document.getElementById('delivery-fee');
const totalEl = document.getElementById('grand-total');
const subtotalEl = document.getElementById('subtotal');

function updateTotals() {
    if (!feeEl || !totalEl || !subtotalEl) {
        return;
    }
    const subtotal = parseFloat(subtotalEl.dataset.value || '0');
    const method = document.querySelector('input[name="delivery_method"]:checked')?.value || 'pickup';
    let fee = 0;
    if (method === 'delivery') {
        const threshold = parseFloat(feeEl.dataset.threshold || '0');
        const standardFee = parseFloat(feeEl.dataset.fee || '0');
        fee = subtotal >= threshold ? 0 : standardFee;
    }
    const grandTotal = subtotal + fee;
    feeEl.textContent = feeEl.dataset.currency + ' ' + fee.toLocaleString();
    totalEl.textContent = totalEl.dataset.currency + ' ' + grandTotal.toLocaleString();
}

methodInputs.forEach((input) => {
    input.addEventListener('change', updateTotals);
});
updateTotals();

function toggleDeliveryFields() {
    const method = document.querySelector('input[name="delivery_method"]:checked')?.value || 'pickup';
    document.querySelectorAll('.delivery-field input, .delivery-field textarea').forEach((field) => {
        if (method === 'pickup') {
            field.dataset.required = field.required ? '1' : '0';
            field.required = false;
            field.disabled = true;
            field.value = '';
        } else {
            field.disabled = false;
            if (field.dataset.required === '1') {
                field.required = true;
            }
        }
    });
}

methodInputs.forEach((input) => {
    input.addEventListener('change', toggleDeliveryFields);
});
toggleDeliveryFields();

const messageButton = document.querySelector('[data-demo-message]');
if (messageButton) {
    messageButton.addEventListener('click', () => {
        showToast(messageButton.dataset.demoMessage || 'Message sent.');
    });
}

const imageInput = document.getElementById('image');
const imagePreview = document.getElementById('product-image-preview');
let previewObjectUrl = null;

function updateImagePreview(src) {
    if (!imagePreview || !src) {
        return;
    }
    imagePreview.src = src;
}

if (imageInput && imagePreview) {
    imageInput.addEventListener('change', () => {
        const [file] = imageInput.files || [];
        if (!file) {
            return;
        }
        if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
        }
        previewObjectUrl = URL.createObjectURL(file);
        updateImagePreview(previewObjectUrl);
    });
}

document.querySelectorAll('[data-copy-text]').forEach((button) => {
    button.addEventListener('click', async () => {
        const text = button.dataset.copyText || '';
        if (!text) {
            return;
        }
        try {
            await navigator.clipboard.writeText(text);
            showToast('Tracking link copied.');
        } catch (error) {
            showToast('Copy failed.');
        }
    });
});
 
const salesChart = document.getElementById('sales-chart');
if (salesChart) {
    salesChart.width = Math.max(salesChart.clientWidth, 320);
    const ctx = salesChart.getContext('2d');
    const labels = JSON.parse(salesChart.dataset.labels || '[]');
    const values = JSON.parse(salesChart.dataset.values || '[]');
    const width = salesChart.width;
    const height = salesChart.height;
    const maxValue = Math.max(...values, 1);
    const leftPad = 28;
    const bottomPad = 34;
    const chartHeight = height - bottomPad - 20;
    const barWidth = labels.length ? Math.min(72, (width - leftPad - 24) / labels.length - 12) : 0;

    ctx.clearRect(0, 0, width, height);
    ctx.font = '12px DM Sans';
    ctx.fillStyle = '#6d544a';
    ctx.strokeStyle = 'rgba(43, 26, 22, 0.12)';
    ctx.beginPath();
    ctx.moveTo(leftPad, 16);
    ctx.lineTo(leftPad, height - bottomPad);
    ctx.lineTo(width - 10, height - bottomPad);
    ctx.stroke();

    labels.forEach((label, index) => {
        const value = values[index] || 0;
        const x = leftPad + 16 + index * (barWidth + 12);
        const barHeight = (value / maxValue) * chartHeight;
        const y = height - bottomPad - barHeight;

        ctx.fillStyle = '#d77a5f';
        ctx.fillRect(x, y, barWidth, barHeight);

        ctx.fillStyle = '#2b1a16';
        ctx.fillText(String(value), x + 6, y - 8);

        const shortLabel = label.length > 12 ? `${label.slice(0, 12)}…` : label;
        ctx.save();
        ctx.translate(x + 6, height - bottomPad + 12);
        ctx.rotate(-0.35);
        ctx.fillStyle = '#6d544a';
        ctx.fillText(shortLabel, 0, 0);
        ctx.restore();
    });
}

document.querySelectorAll('.shop-toolbar select, .shop-toolbar input[type="checkbox"]').forEach((field) => {
    field.addEventListener('change', () => {
        const form = field.closest('form');
        if (form) {
            form.requestSubmit();
        }
    });
});

const shopSearch = document.getElementById('shop-search');
if (shopSearch) {
    let timer;
    shopSearch.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const form = shopSearch.closest('form');
            if (form) {
                form.requestSubmit();
            }
        }, 400);
    });
}
