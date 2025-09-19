<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso de Participantes</title>
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
                <h1>Ingreso de Participantes</h1>
                <p>Ingresa tu DNI para registrar tu asistencia y ver tus pagos.</p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger p-2"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form id="loginForm" method="post" action="<?= site_url('ingreso/entrar'); ?>">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="dni" class="form-label fw-bold"><h4>Número de DNI:</h4></label>
                        <div class="input-group">
                            <input type="text" id="dni" name="dni" class="form-control" required autofocus maxlength="8" pattern="\d{8}">
                            <button class="btn btn-secondary" type="button" id="verifyBtn">Verificar</button>
                        </div>
                        <small id="dniError" class="text-danger d-none">DNI no encontrado.</small>
                    </div>

                    <div id="userInfo" class="text-center my-3 d-none">
                        <p class="mb-0">Bienvenido/a:</p>
                        <h4 id="userName" class="text-success fw-bold"></h4>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" id="submitBtn" class="btn btn-primary btn-lg" disabled>Registra tu asistencia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dniInput = document.getElementById('dni');
            const verifyBtn = document.getElementById('verifyBtn');
            const submitBtn = document.getElementById('submitBtn');
            const userInfo = document.getElementById('userInfo');
            const userName = document.getElementById('userName');
            const dniError = document.getElementById('dniError');
            const loginForm = document.getElementById('loginForm');

            // Función para verificar el DNI
            const checkDni = async () => {
                const dni = dniInput.value;
                if (dni.length !== 8) return; // Solo verifica si tiene 8 dígitos

                // Prepara los datos para enviar
                const formData = new FormData();
                formData.append('dni', dni);
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>'); // CSRF protection

                try {
                    // Envía la petición al controlador
                    const response = await fetch('<?= site_url('ingreso/verificar') ?>', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.status === 'success') {
                        // Si es exitoso, muestra el nombre y habilita el botón de entrar
                        userName.textContent = data.nombre;
                        userInfo.classList.remove('d-none');
                        dniError.classList.add('d-none');
                        submitBtn.disabled = false;
                    } else {
                        // Si falla, muestra error y deshabilita el botón
                        userInfo.classList.add('d-none');
                        dniError.classList.remove('d-none');
                        dniError.textContent = data.message;
                        submitBtn.disabled = true;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    dniError.textContent = 'Error de conexión.';
                    dniError.classList.remove('d-none');
                }
            };

            // Event Listeners
            verifyBtn.addEventListener('click', checkDni);
            dniInput.addEventListener('keydown', (event) => {
                // Permite verificar con la tecla Enter
                if (event.key === 'Enter') {
                    event.preventDefault();
                    checkDni();
                }
            });
            dniInput.addEventListener('input', () => {
                 // Si el usuario cambia el DNI, resetea el estado
                submitBtn.disabled = true;
                userInfo.classList.add('d-none');
                dniError.classList.add('d-none');
            });
        });
    </script>
</body>
</html>