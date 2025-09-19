<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Sistema de Cursos</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/nifty.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('personalice/css/login.css'); ?>"> 
</head>
<body>
    <div class="login-container">
        <div class="login-image d-none d-md-block">
            <img src="<?= base_url('assets/img/login-cursos.jpg'); ?>" alt="Register Illustration" class="img-fluid">
        </div>

        <div class="login-form">
            <div class="login-box">
                <h1>Crear Cuenta</h1>
                <p>Completa el formulario para registrarte</p>

                <form method="post" action="<?= site_url('auth/register'); ?>">
                    <?= csrf_field() ?>
                    
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger p-2">
                            <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="nombres" class="form-label">Nombres</label>
                            <input type="text" name="nombres" class="form-control" value="<?= old('nombres') ?>" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="apellidos" class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" value="<?= old('apellidos') ?>" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="dni" class="form-label">DNI</label>
                        <input type="text" name="dni" class="form-control" value="<?= old('dni') ?>" required>
                    </div>

                    <div class="mb-2">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" value="<?= old('correo') ?>" required>
                    </div>

                    <div class="mb-2">
                        <label for="password" class="form-label">Contraseña</small></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                        <input type="password" name="password_confirm" class="form-control" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Registrarse</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p class="mb-1">¿Ya tienes una cuenta?</p>
                    <a href="<?= site_url('auth/login'); ?>">Inicia Sesión</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>