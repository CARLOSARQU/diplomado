<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso de Participantes</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">

    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: url('<?= base_url("personalice/img/fondo-principal.png") ?>') no-repeat center center fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: #fff;
            border-radius: 1.2rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-wrap: wrap;
            overflow: hidden;
            max-width: 850px;
            width: 100%;
        }

        .login-image {
            flex: 1 1 40%;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-image img {
            max-width: 100%;
            border-radius: 0.8rem;
        }

        .login-form {
            flex: 1 1 60%;
            padding: 3rem;
        }

        .login-box h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1565c0;
            margin-bottom: 0.5rem;
        }

        .login-box p {
            color: #555;
            margin-bottom: 1.5rem;
        }

        .input-group .form-control {
            border-radius: 0.6rem 0 0 0.6rem;
        }

        .input-group .btn {
            border-radius: 0 0.6rem 0.6rem 0;
            background-color: #64b5f6;
            border: none;
            transition: 0.3s;
        }

        .input-group .btn:hover {
            background-color: #42a5f5;
        }

        .btn-primary {
            background-color: #1565c0;
            border: none;
            border-radius: 0.6rem;
            font-size: 1.05rem;
            padding: 0.75rem;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #0d47a1;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(21, 101, 192, 0.3);
        }

        .alert {
            border-radius: 0.5rem;
        }

        @media (max-width: 768px) {
            .login-form {
                padding: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-image d-none d-md-flex">
            <img src="<?= base_url('assets/img/logo-pos.png'); ?>" alt="Login Illustration" class="img-fluid">
        </div>
        <div class="login-form">
            <div class="login-box">
                <h1>Ingreso de Participantes</h1>
                <h4>Ingresa tu DNI para registrar tu asistencia y ver tus pagos.</h4>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger p-2"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form id="loginForm" method="post" action="<?= site_url('ingreso/entrar'); ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="dni" class="form-label fw-bold">Número de DNI:</label>
                        <div class="input-group">
                            <input type="text" id="dni" name="dni" class="form-control" required autofocus maxlength="8"
                                pattern="\d{8}">
                            <button class="btn btn-secondary" type="button" id="verifyBtn">Verificar</button>
                        </div>
                        <small id="dniError" class="text-danger d-none">DNI no encontrado.</small>
                    </div>
                    <div id="userInfo" class="text-center my-3 d-none">
                        <p class="mb-0">Bienvenido/a:</p>
                        <h4 id="userName" class="text-success fw-bold"></h4>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" id="submitBtn" class="btn btn-primary btn-lg" disabled>Registra tu
                            asistencia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dniInput = document.getElementById('dni');
            const verifyBtn = document.getElementById('verifyBtn');
            const submitBtn = document.getElementById('submitBtn');
            const userInfo = document.getElementById('userInfo');
            const userName = document.getElementById('userName');
            const dniError = document.getElementById('dniError');

            const checkDni = async () => {
                const dni = dniInput.value.trim();
                if (dni.length !== 8) return;
                const formData = new FormData();
                formData.append('dni', dni);
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                try {
                    const response = await fetch('<?= site_url('ingreso/verificar') ?>', { method: 'POST', body: formData });
                    const data = await response.json();
                    if (data.status === 'success') {
                        userName.textContent = data.nombre;
                        userInfo.classList.remove('d-none');
                        dniError.classList.add('d-none');
                        submitBtn.disabled = false;
                    } else {
                        userInfo.classList.add('d-none');
                        dniError.classList.remove('d-none');
                        dniError.textContent = data.message;
                        submitBtn.disabled = true;
                    }
                } catch {
                    dniError.textContent = 'Error de conexión.';
                    dniError.classList.remove('d-none');
                }
            };

            verifyBtn.addEventListener('click', checkDni);
            dniInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); checkDni(); } });
            dniInput.addEventListener('input', () => { submitBtn.disabled = true; userInfo.classList.add('d-none'); dniError.classList.add('d-none'); });
        });
    </script>
</body>

</html>