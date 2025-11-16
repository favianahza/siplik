  <?php 
  $list_petugas = Database::FetchAll("SELECT id,name FROM users WHERE role = 'Petugas Lapangan'");
  ?>
  <!-- Floating Change Password Modal -->
  <div class="modal" id="change-pass-modal" role="dialog" aria-modal="true" aria-labelledby="change-pass-title" aria-describedby="change-pass-desc" aria-hidden="true">
    <div class="modal-overlay" data-close-modal></div>

    <div class="modal-card" role="document">
      <button class="modal-close" type="button" aria-label="Tutup modal" data-close-modal>×</button>

      <header class="modal-header" style="justify-content: center">
        <h2 id="change-pass-title">Change Password</h2>
      </header>

      <form class="modal-form" id="change-form" action="#" method="post" novalidate>
        <div class="form-row">
          <label for="old-password" class="modal-label">Masukan Password Lama</label>
          <input id="old-password" name="old_password" type="password" autocomplete="new-password" required minlength="6" />
        </div>

        <div class="form-row">
          <label for="change-password" class="modal-label">Masukan Password Baru</label>
          <input id="change-password" name="new_password" type="password" autocomplete="new-password" required minlength="6" />
        </div>

        <div class="form-row">
          <label for="change-password-confirm" class="modal-label">Konfirmasi Password Baru</label>
          <input id="change-password-confirm" name="new_password_confirm" type="password" autocomplete="new-password" required minlength="6" />
          <small class="field-error" id="change-passmatch" aria-live="polite"></small>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" id="ganti" data-id="<?= $_SESSION["user_id"]; ?>">Ganti Password</button>
        </div>
      </form>

    </div>
  </div>

  <!-- Assign Petugas Modal -->
  <div class="modal" id="assign-petugas-modal" role="dialog" aria-modal="true" aria-labelledby="assign-petugas-title" aria-describedby="assign-petugas-desc" aria-hidden="true">
    <div class="modal-overlay" data-close-modal></div>

    <div class="modal-card" role="document">
      <button class="modal-close" type="button" aria-label="Tutup modal" data-close-modal>×</button>

      <header class="modal-header" style="justify-content: center">
        <h2 id="assign-petugas-title">Alokasi Petugas</h2>
      </header>

      <form id="form-assign-petugas" class="modal-body" style="text-align: center">
        <p id="assign-petugas-desc" class="text-muted">
          Pilih salah satu petugas untuk dialokasikan ke laporan.
        </p>
        <p id="kode_tiket_title">Kode Tiket : 12345</p>

        <!-- Scrollable container for petugas list -->
        <div class="petugas-list" style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; border-radius: 6px; padding: 0.75rem; margin-top: 0.5rem;">
        <?php foreach($list_petugas as $petugas): ?>
          <label class="d-flex align-items-center mb-2" style="gap: 0.5rem;">
            <input type="radio" name="petugas" value="<?= $petugas["id"]?>">
            <span><?= $petugas["name"]?></span>
          </label>
        <?php endforeach; ?>
        </div>

        <div class="form-actions mt-3">
          <button type="submit" class="btn btn-primary" id="assign_petugas" data-id="<?= $_SESSION["user_id"]; ?>">
            Simpan Alokasi
          </button>
        </div>
      </form>

    </div>
  </div>


  <!-- Tindak Lanjut -->
  <div class="modal" id="tindak-lanjut-modal" role="dialog" aria-modal="true" aria-labelledby="tindak-lanjut-title" aria-describedby="tindak-lanjut-desc" aria-hidden="true">
    <div class="modal-overlay" data-close-modal></div>

    <div class="modal-card" role="document">
      <button class="modal-close" type="button" aria-label="Tutup modal" data-close-modal>×</button>

      <header class="modal-header" style="justify-content: center">
        <h2 id="tindak-lanjut-title">LaporanTindak Lanjut</h2>
      </header>
      <p id="kode_tiket_tindak_lanjut" style="text-align: center; margin-top: 16px;">Kode Tiket : 12345</p>

      <form id="tindak_lanjut_form" class="modal-body" style="text-align: center">

        <!-- Catatan -->
        <div class="form-group mb-3" style="text-align: left;">
          <label for="catatan" class="fw-semibold">Catatan</label>
          <small class="d-block text-muted mb-1">Tuliskan hasil atau tindak lanjut yang telah dilakukan.</small>
          <textarea id="catatan" name="catatan" rows="12"  required
                    style="width: 100%; border: 1px solid #ccc; border-radius: 6px; padding: 0.6rem;"></textarea>
        </div>

        <!-- Upload Bukti -->
        <div class="form-group mb-3" style="text-align: left;">
          <label for="bukti_tindak_lanjut" class="fw-semibold">Upload Bukti</label>
          <small class="d-block text-muted mb-1">Unggah satu atau lebih foto sebagai bukti tindak lanjut.</small>
          <input type="file" id="bukti_tindak_lanjut" name="bukti_tindak_lanjut[]" multiple accept="image/*"
                style="width: 100%; border: 1px solid #ccc; border-radius: 6px; padding: 0.4rem; background-color: #fff;">
        </div>

        <input type="hidden" value="<?= $_SESSION["user_id"]; ?>" name="petugas_id">

        <div class="form-actions mt-3">
          <button type="submit" class="btn btn-primary" id="create_tindak_lanjut">
            Submit
          </button>
        </div>

      </form>

    </div>
  </div>  