<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel; // <-- PASO 1: Asegúrate de importar el RoleModel

class AuthController extends BaseController
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            // Si ya está logueado, redirigir según su rol
            if (session('rol_nombre') === 'usuario') {
                return redirect()->to('/mi-panel');
            }
            return redirect()->to('/dashboard');
        }
        return view('auth/login'); 
    }

    /**
     * Procesa el intento de inicio de sesión.
     */
    public function attemptLogin()
    {
        $userModel = new UserModel();
        $roleModel = new RoleModel();
        
        $email = $this->request->getPost('correo');
        $password = $this->request->getPost('password');

        $user = $userModel->where('correo', $email)->first();

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['estado'] != 1) {
                return redirect()->back()->with('error', 'Tu cuenta está desactivada.');
            }
            
            $role = $roleModel->find($user['rol_id']);
            $roleName = $role ? $role['nombre'] : 'desconocido';

            $sessionData = [
                'user_id'    => $user['id'],
                'nombres'    => $user['nombres'],
                'apellidos'  => $user['apellidos'],
                'correo'     => $user['correo'],
                'rol_id'     => $user['rol_id'],
                'rol_nombre' => $roleName,
                'isLoggedIn' => true,
            ];

            // --- AQUÍ ESTÁ LA CORRECCIÓN ---
            // PASO 1: Guarda los datos en la sesión.
            session()->set($sessionData);

            // PASO 2: AHORA, con la sesión ya guardada, decide a dónde redirigir.
            if ($roleName === 'usuario') {
                return redirect()->to('mi-panel');
            } else {
                return redirect()->to('dashboard');
            }
        }

        return redirect()->back()->withInput()->with('error', 'Correo o contraseña incorrectos.');
    }

    // ... (los métodos register y logout permanecen igual) ...

    /**
     * Muestra el formulario de registro.
     */
    public function register()
    {
        if (session()->get('isLoggedIn')) {
            if (session('rol_nombre') === 'usuario') {
                return redirect()->to('/mi-panel');
            }
            return redirect()->to('/dashboard');
        }
        return view('auth/register');
    }

    /**
     * Procesa el intento de registro de un nuevo usuario.
     */
    public function attemptRegister()
    {
        $userModel = new UserModel();
        $roleModel = new RoleModel();

        $userRole = $roleModel->where('nombre', 'usuario')->first();
        if (!$userRole) {
            return redirect()->back()->with('error', 'Error del sistema: No se pudo asignar el rol de usuario.');
        }

        $validationRules = [
            'nombres' => 'required|min_length[3]|max_length[50]',
            'apellidos' => 'required|min_length[3]|max_length[50]',
            'correo' => 'required|valid_email|is_unique[users.correo]',
            'dni' => 'required|numeric|exact_length[8]|is_unique[users.dni]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'matches[password]'
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nombres'             => $this->request->getPost('nombres'),
            'apellidos'           => $this->request->getPost('apellidos'),
            'correo'              => $this->request->getPost('correo'),
            'dni'                 => $this->request->getPost('dni'),
            'escuela_profesional' => $this->request->getPost('escuela_profesional'), // Asegúrate de tener este campo en tu form
            'password_hash'       => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'rol_id'              => $userRole['id'],
            'estado'              => 1,
        ];

        if ($userModel->insert($data)) {
            return redirect()->to('/login')->with('success', '¡Registro exitoso! Ahora puedes iniciar sesión.');
        } else {
            return redirect()->back()->withInput()->with('error', 'No se pudo completar el registro. Inténtalo de nuevo.');
        }
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}