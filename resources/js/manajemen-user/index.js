window.openModal = function(id) {
    document.getElementById(id).classList.add('show');
}

window.closeModal = function(id) {
    document.getElementById(id).classList.remove('show');
}

window.openEditModal = function(userId, user) {

    document.getElementById('edit-nama').value = user.nama ?? '';
    document.getElementById('edit-email').value = user.email ?? '';
    document.getElementById('edit-nomor-hp').value = user.nomor_hp ?? '';
    document.getElementById('edit-role').value = user.role ?? '';
    document.getElementById('edit-status').value = user.status ?? '';

    document.getElementById('form-edit').action = `/users/${userId}`;

    openModal('modal-edit');
}

window.openResetModal = function(userId, nama) {

    document.getElementById('reset-desc').innerHTML =
        `Set password baru untuk <strong>${nama}</strong>`;

    document.getElementById('form-reset').action =
        `/users/${userId}/reset-password`;

    openModal('modal-reset');
}

window.openDeleteModal = function(userId, nama) {

    document.getElementById('hapus-desc').innerHTML =
        `User <strong>${nama}</strong> akan dihapus permanen.`;

    document.getElementById('form-hapus').action =
        `/users/${userId}`;

    openModal('modal-hapus');
}

window.onclick = function(event) {

    document.querySelectorAll('.modal-backdrop').forEach(modal => {
        if(event.target === modal) {
            modal.classList.remove('show');
        }
    });
}

function showToast(message, type = 'success') {
    const existing = document.querySelector('.toast.toast--temp');
    if (existing) {
        existing.remove();
    }

    const toast = document.createElement('div');
    toast.className = `toast toast--${type} toast--temp show`;
    toast.innerHTML = `
        <span class="toast__icon">${type === 'success' ? '&#10003;' : '&#10007;'}</span>
        ${message}
    `;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.remove('show'), 3000);
    setTimeout(() => toast.remove(), 3800);
}

function updateSummaryCount(name, delta) {
    const el = document.querySelector(`[data-summary="${name}"]`);
    if (!el) return;
    const value = Number(el.textContent || 0) + delta;
    el.textContent = String(value >= 0 ? value : 0);
}

function updateStatusRow(form, newStatus) {
    const row = form.closest('tr');
    const badge = row?.querySelector('.status-badge');
    const toggleBtn = form.querySelector('button[type="submit"]');
    if (!row || !badge || !toggleBtn) return;

    badge.textContent = newStatus === 'aktif' ? 'Aktif' : 'Nonaktif';
    badge.classList.remove('status-badge--aktif', 'status-badge--nonaktif');
    badge.classList.add(`status-badge--${newStatus}`);
    row.classList.toggle('mu-table__row--disabled', newStatus === 'nonaktif');

    toggleBtn.classList.toggle('btn-icon--disable', newStatus === 'aktif');
    toggleBtn.classList.toggle('btn-icon--enable', newStatus === 'nonaktif');
    toggleBtn.title = newStatus === 'aktif' ? 'Nonaktifkan' : 'Aktifkan';
    toggleBtn.textContent = newStatus === 'aktif' ? '⏸' : '▶';
}

function initStatusToggle() {
    document.querySelectorAll('form[action*="/toggle-status"]').forEach(form => {
        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            const url = this.action;
            const token = this.querySelector('input[name="_token"]')?.value || '';
            const previousStatus = this.querySelector('.status-badge')?.textContent?.toLowerCase();

            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                const data = await response.json();
                if (!response.ok) {
                    const message = data.message || data.error || 'Gagal mengubah status pengguna.';
                    showToast(message, 'error');
                    return;
                }

                const newStatus = data.data?.status;
                if (!newStatus) {
                    showToast('Respons status tidak valid.', 'error');
                    return;
                }

                updateStatusRow(this, newStatus);

                if (previousStatus && previousStatus !== newStatus) {
                    if (newStatus === 'aktif') {
                        updateSummaryCount('aktif', 1);
                        updateSummaryCount('nonaktif', -1);
                    } else {
                        updateSummaryCount('aktif', -1);
                        updateSummaryCount('nonaktif', 1);
                    }
                }

                showToast(data.message || 'Status berhasil diubah.', 'success');
            } catch (error) {
                console.error(error);
                showToast('Terjadi kesalahan jaringan. Coba lagi.', 'error');
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initStatusToggle);