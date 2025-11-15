  <!-- Floating Register Modal -->
  <div class="modal" id="register-modal" role="dialog" aria-modal="true" aria-labelledby="register-title" aria-describedby="register-desc" aria-hidden="true">
    <div class="modal-overlay" data-close-modal></div>

    <div class="modal-card" role="document">
      <button class="modal-close" type="button" aria-label="Tutup modal" data-close-modal>×</button>

      <header class="modal-header">
        <h2 id="register-title">Daftar Akun SIPLIK</h2>
      </header>

      <form class="modal-form" id="register-form" action="#" method="post" novalidate>
        <div class="form-row">
          <label for="reg-fullname" class="modal-label">Nama Lengkap</label>
          <input id="reg-fullname" name="fullname" type="text" autocomplete="name" required />
        </div>

        <div class="form-row">
          <label for="reg-email" class="modal-label">Email</label>
          <input id="reg-email" name="email" type="email" autocomplete="email" inputmode="email" maxlength="254" required/>
        </div>

        <div class="form-row">
          <label for="reg-password" class="modal-label">Password</label>
          <input id="reg-password" name="password" type="password" autocomplete="new-password" required minlength="6" />
        </div>

        <div class="form-row">
          <label for="reg-password-confirm" class="modal-label">Konfirmasi Password</label>
          <input id="reg-password-confirm" name="password_confirm" type="password" autocomplete="new-password" required minlength="6" />
          <small class="field-error" id="reg-passmatch" aria-live="polite"></small>
        </div>

        <div class="form-group">
          <center><label>Daftar Sebagai</label></center>
          <div class="radio-inline-group">
            <label>
              <input type="radio" name="role" value="Petugas Lapangan" required>
              Petugas Lapangan
            </label>
            <label>
              <input type="radio" name="role" value="Petugas DLHK" required>
              Petugas DLHK
            </label>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" id="register" >Buat Akun</button>
        </div>

        <div class="form-footnote">
          <p><strong>Sudah punya akun? <a href="#login" class="modal-link" data-open-login>Dapatkan akses</a></strong></p>
        </div>
      </form>
    </div>
  </div>

  <!-- Floating Login Modal -->
  <div class="modal" id="login-modal" role="dialog" aria-modal="true" aria-labelledby="login-title" aria-describedby="login-desc" aria-hidden="true">
    <div class="modal-overlay" data-close-modal></div>

    <div class="modal-card" role="document">
      <button class="modal-close" type="button" aria-label="Tutup modal" data-close-modal>×</button>

      <header class="modal-header">
        <h2 id="login-title">Masuk ke SIPLIK</h2>
        <p id="login-desc">Gunakan akun Anda untuk melaporkan dan memantau pengaduan lingkungan kota.</p>
      </header>

      <form class="modal-form" action="#" method="post" novalidate>
        <div class="form-row">
          <label for="login-email" class="modal-label">Email</label>
          <input id="login-email" name="login-email" type="email" autocomplete="email" inputmode="email" maxlength="254" required />
        </div>      

        <div class="form-row">
          <label for="login-password" class="modal-label">Password</label>
          <input id="login-password" name="login-password" type="password" autocomplete="current-password" required />
        </div>

        <div class="form-row form-row-inline">
          <label class="checkbox">
            <input id="login-remember" name="remember" type="checkbox" />
            <span>Ingat saya</span>
          </label>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" id="login">Login / Continue</button>
        </div>

        <div class="form-footnote">
          <p><strong>Tidak punya akun? <a href="#daftar" class="modal-link" data-close-modal>Daftar sekarang</a></strong></p>
        </div>
      </form>

    </div>
  </div>