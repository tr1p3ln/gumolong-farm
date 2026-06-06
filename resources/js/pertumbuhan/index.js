function openTimbangModal(earTagId, nama) {
        const modal  = document.getElementById('timbangModal');
        const form   = document.getElementById('timbangForm');
        const sub    = document.getElementById('modalSub');
 
        form.action = `/tracking-pertumbuhan/${earTagId}/penimbangan`;
        sub.textContent = `Input berat timbangan untuk domba #${earTagId} – ${nama}`;
 
        modal.classList.add('open');
    }
 
    function closeModal() {
        document.getElementById('timbangModal').classList.remove('open');
    }
 
    // Close on backdrop click
    document.getElementById('timbangModal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });
 
    // ESC key
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });