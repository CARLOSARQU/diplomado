<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h4>Recuperar contraseña</h4>
                <p>Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>

                <form action="<?= base_url('auth/request-reset') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" id="correo" name="correo" class="form-control" required>
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary">Enviar enlace</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
