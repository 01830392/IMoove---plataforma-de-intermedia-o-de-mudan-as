const API = 'http://127.0.0.1:8000/api';

function getToken() {
    return localStorage.getItem('token');
}

function getUser() {
    return JSON.parse(localStorage.getItem('user') || '{}');
}

function logout() {
    fetch(`${API}/logout`, {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + getToken(),
            'Accept': 'application/json',
        }
    }).finally(() => {
        localStorage.clear();
        window.location.href = 'index.html';
    });
}

async function request(method, endpoint, body = null) {
    const options = {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + getToken(),
        }
    };
    if (body) options.body = JSON.stringify(body);

    const response = await fetch(`${API}${endpoint}`, options);
    const data = await response.json();
    return { ok: response.ok, status: response.status, data };
}

function showAlert(containerId, message, type = 'success') {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
    setTimeout(() => container.innerHTML = '', 4000);
}

function badgeStatus(status) {
    return `<span class="badge badge-${status}">${status}</span>`;
}