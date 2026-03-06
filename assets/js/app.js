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
    setTimeout(() => toastEl.classList.remove('show'), 2500);
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

window.initMap = function () {
    const mapEl = document.getElementById('map');
    if (!mapEl || !window.google || !window.google.maps) {
        return;
    }
    const fallback = { lat: 16.8409, lng: 96.1735 };
    const map = new google.maps.Map(mapEl, {
        center: fallback,
        zoom: 13,
    });
    const marker = new google.maps.Marker({ position: fallback, map });

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const loc = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                map.setCenter(loc);
                marker.setPosition(loc);
            },
            () => {}
        );
    }
};
