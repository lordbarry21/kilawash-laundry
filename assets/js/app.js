/**
 * ============================================================================
 * FILE: assets/js/app.js
 * FUNGSI: Interaksi UI utama:
 *         1. Modal <dialog> native (light-dismiss fallback & reset returnValue)
 *         2. Mobile Navigation Drawer (scroll-snap + IntersectionObserver)
 *         3. Kalkulator tarif otomatis pada Form Transaksi Baru
 *         4. Binding Modal CRUD Layanan & Konfirmasi Hapus
 *         5. Toggle lihat kata sandi & quick-fill demo login
 * ============================================================================
 */

(() => {
  'use strict';

  // --------------------------------------------------------------------------
  // 1. MODAL <dialog> NATIVE (modern-web-guidance: dialog & light-dismiss)
  // --------------------------------------------------------------------------
  const dialogs = document.querySelectorAll('dialog.modal');

  // Reset returnValue saat modal ditutup agar tidak ada state basi
  dialogs.forEach((dialog) => {
    dialog.addEventListener('close', () => {
      dialog.returnValue = '';
    });
  });

  // Fallback light-dismiss jika browser belum mendukung atribut `closedby`
  if (!('closedBy' in HTMLDialogElement.prototype)) {
    const isOutside = (e, dialog) => {
      const r = dialog.getBoundingClientRect();
      return (
        e.clientX < r.left ||
        e.clientX > r.right ||
        e.clientY < r.top ||
        e.clientY > r.bottom
      );
    };

    document.querySelectorAll('dialog.modal[closedby="any"]').forEach((dialog) => {
      let pressedOutside = false;
      dialog.addEventListener('pointerdown', (e) => {
        pressedOutside = isOutside(e, dialog);
      });
      dialog.addEventListener('pointerup', (e) => {
        if (pressedOutside && isOutside(e, dialog)) {
          dialog.close();
        }
      });
    });
  }

  // Tombol pembuka & penutup modal generik via data-open-modal / data-close-modal
  document.addEventListener('click', (event) => {
    const openTrigger = event.target.closest('[data-open-modal]');
    if (openTrigger) {
      const targetId = openTrigger.getAttribute('data-open-modal');
      const modal = document.getElementById(targetId);
      if (modal && typeof modal.showModal === 'function') {
        modal.showModal();
      }
      return;
    }

    const closeTrigger = event.target.closest('[data-close-modal]');
    if (closeTrigger) {
      const modal = closeTrigger.closest('dialog');
      if (modal) modal.close();
    }
  });

  // --------------------------------------------------------------------------
  // 2. MOBILE NAVIGATION DRAWER (modern-web-guidance: navigation-drawer)
  // --------------------------------------------------------------------------
  const drawer = document.getElementById('mobile-drawer');
  const openDrawerBtn = document.getElementById('drawer-open');
  const closeDrawerBtn = document.getElementById('drawer-close');

  if (drawer && openDrawerBtn && closeDrawerBtn) {
    const scroller = drawer.querySelector('.drawer-scroller');
    const sheet = drawer.querySelector('.drawer-sheet');

    const openDrawer = async () => {
      drawer.showModal();
      if (!CSS.supports('scroll-initial-target', 'nearest')) {
        scroller.scrollTo({ left: scroller.offsetWidth, behavior: 'instant' });
        await new Promise((resolve) =>
          requestAnimationFrame(() => requestAnimationFrame(resolve))
        );
      }
      scroller.scrollTo({ left: 0, behavior: 'auto' });
    };

    const closeDrawer = () => {
      scroller.scrollTo({ left: scroller.offsetWidth, behavior: 'auto' });
    };

    const visibleThreshold = 1 / Math.max(window.innerWidth, 320);
    const observer = new IntersectionObserver(
      (entries) => {
        const entry = entries.at(-1);
        if (!entry) return;
        if (entry.intersectionRatio < visibleThreshold && drawer.open) {
          drawer.close();
          openDrawerBtn.setAttribute('aria-expanded', 'false');
        }
        if (entry.intersectionRatio === 1) {
          openDrawerBtn.setAttribute('aria-expanded', 'true');
        }
      },
      { root: drawer, threshold: [visibleThreshold, 1] }
    );
    observer.observe(sheet);

    // Fallback opacity backdrop bila browser tidak mendukung animation-timeline
    if (!CSS.supports('animation-timeline: scroll()')) {
      scroller.addEventListener('scroll', () => {
        const width = sheet.offsetWidth || 1;
        const ratio = Math.max(0, Math.min(1, 1 - scroller.scrollLeft / width));
        drawer.style.setProperty('--drawer-backdrop', String(ratio));
      });
    }

    openDrawerBtn.addEventListener('click', openDrawer);
    closeDrawerBtn.addEventListener('click', closeDrawer);

    drawer.addEventListener('click', (event) => {
      if (!sheet.contains(event.target)) closeDrawer();
    });

    drawer.addEventListener('cancel', (event) => {
      if (event.cancelable) {
        event.preventDefault();
        closeDrawer();
      }
    });
  }

  // --------------------------------------------------------------------------
  // 3. KALKULATOR TARIF OTOMATIS (Form Transaksi Baru)
  // --------------------------------------------------------------------------
  const trxLayananSelect = document.getElementById('trx-layanan');
  const trxBeratInput = document.getElementById('trx-berat');
  const trxPreviewTotal = document.getElementById('trx-preview-total');
  const trxPreviewMeta = document.getElementById('trx-preview-meta');

  const formatIDR = (num) =>
    'Rp ' + Math.round(num).toLocaleString('id-ID');

  const updateTariffPreview = () => {
    if (!trxLayananSelect || !trxBeratInput || !trxPreviewTotal || !trxPreviewMeta) return;
    const option = trxLayananSelect.selectedOptions[0];
    const harga = Number(option?.dataset.harga || 0);
    const estimasi = option?.dataset.estimasi || '';
    const berat = Number(trxBeratInput.value || 0);

    if (harga > 0 && berat > 0) {
      const total = harga * berat;
      trxPreviewTotal.textContent = formatIDR(total);
      trxPreviewMeta.textContent = `${berat} kg × ${formatIDR(harga)}/kg • Estimasi ${estimasi}`;
    } else if (harga > 0) {
      trxPreviewTotal.textContent = formatIDR(0);
      trxPreviewMeta.textContent = `Tarif ${formatIDR(harga)}/kg • Estimasi ${estimasi}`;
    } else {
      trxPreviewTotal.textContent = 'Rp 0';
      trxPreviewMeta.textContent = 'Pilih jenis layanan dan masukkan berat cucian';
    }
  };

  if (trxLayananSelect && trxBeratInput) {
    trxLayananSelect.addEventListener('change', updateTariffPreview);
    trxBeratInput.addEventListener('input', updateTariffPreview);
    updateTariffPreview();
  }

  // --------------------------------------------------------------------------
  // 4. MODAL CRUD DATA LAYANAN (Tambah, Ubah, Konfirmasi Hapus)
  // --------------------------------------------------------------------------
  const modalLayanan = document.getElementById('modal-layanan');
  const formLayanan = document.getElementById('form-layanan');

  if (modalLayanan && formLayanan) {
    const titleEl = document.getElementById('modal-layanan-title');
    const actionEl = document.getElementById('layanan-action');
    const idEl = document.getElementById('layanan-id');
    const namaEl = document.getElementById('layanan-nama');
    const hargaEl = document.getElementById('layanan-harga');
    const estimasiEl = document.getElementById('layanan-estimasi');
    const deskripsiEl = document.getElementById('layanan-deskripsi');
    const submitBtn = document.getElementById('layanan-submit-btn');

    // Mode Tambah Layanan
    document.querySelectorAll('[data-add-layanan]').forEach((btn) => {
      btn.addEventListener('click', () => {
        formLayanan.reset();
        titleEl.textContent = 'Tambah Jenis Layanan';
        actionEl.value = 'create';
        idEl.value = '';
        submitBtn.textContent = 'Simpan Layanan';
        modalLayanan.showModal();
      });
    });

    // Mode Edit Layanan
    document.querySelectorAll('[data-edit-layanan]').forEach((btn) => {
      btn.addEventListener('click', () => {
        formLayanan.reset();
        titleEl.textContent = 'Ubah Data Layanan';
        actionEl.value = 'update';
        idEl.value = btn.dataset.id || '';
        namaEl.value = btn.dataset.nama || '';
        hargaEl.value = btn.dataset.harga || '';
        estimasiEl.value = btn.dataset.estimasi || '';
        deskripsiEl.value = btn.dataset.deskripsi || '';
        submitBtn.textContent = 'Simpan Perubahan';
        modalLayanan.showModal();
      });
    });
  }

  // Modal Konfirmasi Hapus Layanan (autofocus pada tombol Batal sesuai panduan)
  const modalHapusLayanan = document.getElementById('modal-hapus-layanan');
  if (modalHapusLayanan) {
    const hapusIdEl = document.getElementById('hapus-layanan-id');
    const hapusNamaEl = document.getElementById('hapus-layanan-nama');

    document.querySelectorAll('[data-delete-layanan]').forEach((btn) => {
      btn.addEventListener('click', () => {
        hapusIdEl.value = btn.dataset.id || '';
        hapusNamaEl.textContent = btn.dataset.nama || '';
        modalHapusLayanan.showModal();
      });
    });
  }

  // Modal Konfirmasi Hapus Transaksi (khusus Admin)
  const modalHapusTrx = document.getElementById('modal-hapus-transaksi');
  if (modalHapusTrx) {
    const hapusTrxIdEl = document.getElementById('hapus-transaksi-id');
    const hapusTrxKodeEl = document.getElementById('hapus-transaksi-kode');

    document.querySelectorAll('[data-delete-transaksi]').forEach((btn) => {
      btn.addEventListener('click', () => {
        hapusTrxIdEl.value = btn.dataset.id || '';
        hapusTrxKodeEl.textContent = btn.dataset.kode || '';
        modalHapusTrx.showModal();
      });
    });
  }

  // --------------------------------------------------------------------------
  // 5. TOGGLE LIHAT KATA SANDI & QUICK-FILL AKUN DEMO (Halaman Login)
  // --------------------------------------------------------------------------
  const togglePwdBtn = document.getElementById('toggle-password');
  const pwdInput = document.getElementById('password');

  if (togglePwdBtn && pwdInput) {
    togglePwdBtn.addEventListener('click', () => {
      const showing = pwdInput.type === 'text';
      pwdInput.type = showing ? 'password' : 'text';
      togglePwdBtn.setAttribute('aria-pressed', String(!showing));
      togglePwdBtn.setAttribute(
        'aria-label',
        showing ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi'
      );
    });
  }

  document.querySelectorAll('[data-fill-demo]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const userEl = document.getElementById('username');
      const passEl = document.getElementById('password');
      if (userEl && passEl) {
        userEl.value = btn.dataset.username || '';
        passEl.value = btn.dataset.password || '';
        userEl.dispatchEvent(new Event('input', { bubbles: true }));
        passEl.dispatchEvent(new Event('input', { bubbles: true }));
        userEl.focus();
      }
    });
  });

  // Tutup flash banner
  document.querySelectorAll('[data-dismiss-flash]').forEach((btn) => {
    btn.addEventListener('click', () => {
      btn.closest('.flash')?.remove();
    });
  });
})();
