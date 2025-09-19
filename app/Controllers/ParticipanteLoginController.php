<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;

class ParticipanteLoginController extends BaseController
{
    /**
     * Muestra la página de login por DNI.
     */
    public function index()
    {
        // Si ya está logueado, lo redirige a su panel.
        if (session()->get('isLoggedIn')) {
            return redirect()->to('mi-panel');
        }
        return view('auth/participante_login');
    }

    /**
     * Procesa la verificación del DNI (vía AJAX/Fetch desde la vista).
     * Devuelve el nombre del usuario si lo encuentra.
     */
    public function verifyDni()
    {
        $userModel = new UserModel();
        $dni = $this->request->getPost('dni');

        // Busca al usuario por DNI y que tenga el rol de 'usuario'
        $user = $userModel
            ->select('users.nombres, users.apellidos')
            ->join('roles', 'roles.id = users.rol_id')
            ->where('roles.nombre', 'usuario')
            ->where('users.dni', $dni)
            ->first();

        if ($user) {
            // Si encuentra al usuario, devuelve su nombre en formato JSON
            return $this->response->setJSON([
                'status' => 'success',
                'nombre' => $user['nombres'] . ' ' . $user['apellidos']
            ]);
        }

        // Si no lo encuentra, devuelve un error
        return $this->response->setJSON(['status' => 'error', 'message' => 'DNI no encontrado o no corresponde a un participante.']);
    }

    /**
     * Procesa el login final y crea la sesión.
     */
    public function attemptLogin()
    {
        $userModel = new UserModel();
        $roleModel = new RoleModel();
        $dni = $this->request->getPost('dni');

        $user = $userModel->where('dni', $dni)->first();
        
        // Doble verificación por si el usuario cambia el DNI después de verificar
        if (!$user) {
            return redirect()->to('ingreso')->with('error', 'Ha ocurrido un error. Intenta de nuevo.');
        }

        $role = $roleModel->find($user['rol_id']);
        $roleName = $role ? $role['nombre'] : 'desconocido';

        // Crea la sesión completa
        $sessionData = [
            'user_id'    => $user['id'],
            'nombres'    => $user['nombres'],
            'apellidos'  => $user['apellidos'],
            'correo'     => $user['correo'],
            'rol_id'     => $user['rol_id'],
            'rol_nombre' => $roleName,
            'isLoggedIn' => true,
        ];
        session()->set($sessionData);

        // Redirige al panel del participante
        return redirect()->to('mi-panel');
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/ingreso');
    }
}