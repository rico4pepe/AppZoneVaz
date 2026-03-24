import Swal from 'sweetalert2';

const API = '/fanzone/api';

let upgradeShown = false;
let renewalShown = false;

function showUpgradePrompt(message) {
    if (upgradeShown) return;

    upgradeShown = true;

    Swal.fire({
        title: '🚀 Unlock More Power',
        text: message || 'Upgrade to enjoy unlimited access.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Upgrade Now',
        cancelButtonText: 'Later',
        confirmButtonColor: '#ff4500',
    }).then(() => {
        upgradeShown = false;
    });
}

function showRenewalPrompt() {
    if (renewalShown) return;

    renewalShown = true;

    Swal.fire({
        title: '⏳ Subscription Expired',
        text: 'Renew now to continue enjoying full access.',
        icon: 'warning',
        confirmButtonText: 'Renew Now',
        confirmButtonColor: '#ff0000',
        allowOutsideClick: false,
    }).then(() => {
        renewalShown = false;
    });
}

export async function apiFetch(path, options = {}) {
    const token = localStorage.getItem('auth_token');

    const res = await fetch(`${API}${path}`, {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
            ...(options.headers || {}),
        }
    });

    // 🔥 Handle non-JSON responses (your previous crash issue)
    const text = await res.text();

    let data;
    try {
        data = JSON.parse(text);
    } catch (e) {
        console.error('Non-JSON response:', text);
        throw new Error('Server returned invalid response (auth or server error)');
    }

    // 🔥 Global upgrade trigger
    if (data?.upgrade_required) {
        showUpgradePrompt(data.message);
    }

    // 🔥 Global renewal trigger
    if (data?.subscription?.expired) {
        showRenewalPrompt();
    }

    // 🔥 Preserve your existing error handling
    if (!res.ok) {
        throw new Error(data.message || `Request failed (${res.status})`);
    }

    return data;
}