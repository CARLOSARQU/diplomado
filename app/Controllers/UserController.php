<?php

namespace App\Controllers;

use App\Models\UserModel; // Asegúrate de crear este modelo
use App\Models\RoleModel; // Asegúrate de crear este modelo
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends BaseController
{
    // Muestra la lista de usuarios
    public function __construct()
    {
        if (session('rol_nombre') !== 'superadmin') {
            // Si no es superadmin, muestra un error 404 para no revelar la existencia de la página.
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    /**
     * Muestra la lista de todos los usuarios.
     */
    public function index()
    {
        $userModel = new UserModel();
        $roleModel = new RoleModel();

        $data = [
            'usuarios' => $userModel->getUsersWithRoles(),
            'roles' => $roleModel->findAll() // Para el dropdown de los modales
        ];

        return view('usuarios/index', $data);
    }

    /**
     * Procesa la creación de un nuevo usuario desde el modal.
     */
    public function create()
    {
        $rules = [
            'nombres' => 'required',
            'apellidos' => 'required',
            'correo' => 'required|valid_email|is_unique[users.correo]',
            'dni' => 'required|numeric|exact_length[8]|is_unique[users.dni]',
            'rol_id' => 'required|is_not_unique[roles.id]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userModel->save([
            'nombres' => $this->request->getPost('nombres'),
            'apellidos' => $this->request->getPost('apellidos'),
            'correo' => $this->request->getPost('correo'),
            'dni' => $this->request->getPost('dni'),
            'escuela_profesional' => $this->request->getPost('escuela_profesional'),
            'rol_id' => $this->request->getPost('rol_id'),
            'estado' => $this->request->getPost('estado'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to('usuarios')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Procesa la actualización de un usuario.
     */
    public function update($id)
    {
        $rules = [
            'nombres' => 'required',
            'apellidos' => 'required',
            'correo' => "required|valid_email|is_unique[users.correo,id,{$id}]",
            'dni' => "required|numeric|exact_length[8]|is_unique[users.dni,id,{$id}]",
            'rol_id' => 'required|is_not_unique[roles.id]',
            'password' => 'permit_empty|min_length[6]', // La contraseña es opcional
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $data = [
            'nombres' => $this->request->getPost('nombres'),
            'apellidos' => $this->request->getPost('apellidos'),
            'correo' => $this->request->getPost('correo'),
            'dni' => $this->request->getPost('dni'),
            'escuela_profesional' => $this->request->getPost('escuela_profesional'),
            'rol_id' => $this->request->getPost('rol_id'),
            'estado' => $this->request->getPost('estado'),
        ];
        
        // Solo actualiza la contraseña si se ha proporcionado una nueva
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $userModel->update($id, $data);
        return redirect()->to('usuarios')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Elimina un usuario.
     */
    public function delete($id)
    {
        // Medida de seguridad: un superadmin no puede eliminarse a sí mismo.
        if ($id == session('user_id')) {
            return redirect()->to('usuarios')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $userModel = new UserModel();
        $userModel->delete($id);
        
        return redirect()->to('usuarios')->with('success', 'Usuario eliminado exitosamente.');
    }

    public function exportarExcel()
    {
        $usuarioModel = new UserModel();
        $usuarios = $usuarioModel->findAll();

        // Crear documento Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'Apellido');
        $sheet->setCellValue('B1', 'Nombre');

        // Rellenar datos
        $fila = 2;
        foreach ($usuarios as $usuario) {
            $sheet->setCellValue('A' . $fila, $usuario['apellidos']);
            $sheet->setCellValue('B' . $fila, $usuario['nombres']);
            $fila++;
        }

        // Descargar archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'usuarios_' . date('Ymd_His') . '.xlsx';

        // Cabeceras para forzar la descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}