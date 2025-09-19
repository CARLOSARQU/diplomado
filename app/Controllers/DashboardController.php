<?php

namespace App\Controllers;

use App\Models\CursoModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    /**
     * Muestra la página principal o dashboard del usuario autenticado.
     * El contenido varía según el rol del usuario.
     */
    public function index()
    {
        $data = [];
        $userRole = session('rol_nombre');
        $userId = session('user_id');

        $cursoModel = new CursoModel();

        if ($userRole === 'superadmin') {
            // Lógica para el Superadmin: ve las estadísticas globales.
            $userModel = new UserModel();
            $data['total_usuarios'] = $userModel->countAllResults();
            $data['total_cursos_activos'] = $cursoModel->where('estado', '1')->countAllResults();

        } elseif ($userRole === 'admin') {
            // Lógica para el Admin/Docente: ve solo sus cursos asignados.
            $cursosAsignados = $cursoModel
                ->select('cursos.id, cursos.nombre, cursos.descripcion')
                ->join('curso_docente', 'curso_docente.curso_id = cursos.id')
                ->where('curso_docente.docente_id', $userId)
                ->where('cursos.estado', '1') // Mostramos solo los cursos activos
                ->findAll();
            
            $data['cursos_asignados'] = $cursosAsignados;
            $data['total_cursos_asignados'] = count($cursosAsignados);
        }
        // Nota: Un 'usuario' normal es redirigido a '/mi-panel' por el AuthController,
        // por lo que no debería llegar aquí.

        return view('dashboard', $data);
    }
}