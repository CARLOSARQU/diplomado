<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistema de Cursos</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/nifty.min.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('personalice/css/login.css'); ?>"> 
</head>
<body>
    <div class="login-container">
        <div class="login-image d-none d-md-block">
            <img src="<?= base_url('assets/img/logo-pos.png'); ?>" alt="Login Illustration" class="img-fluid">
        </div>

        <div class="login-form">
            <div class="login-box">
                <h1>Bienvenido</h1>
                <p>Inicia sesión para continuar</p>

                <form method="post" action="<?= site_url('auth/login'); ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" class="form-control" value="<?= old('correo') ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger text-center mt-3 p-2">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Iniciar Sesión</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p class="mb-1">¿Aún no tienes una cuenta?</p>
                    <a href="<?= site_url('auth/register'); ?>">Regístrate aquí</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>