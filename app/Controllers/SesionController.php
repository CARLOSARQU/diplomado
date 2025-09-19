<?php

namespace App\Controllers;

use App\Models\CursoModel;
use App\Models\ModuloModel;
use App\Models\SesionModel;
use App\Models\AsistenciaModel; // Para borrado en cascada

class SesionController extends BaseController
{
    /**
     * Muestra la lista de sesiones para un módulo específico.
     */
    public function index($modulo_id)
    {
        $moduloModel = new ModuloModel();
        $cursoModel = new CursoModel();
        $sesionModel = new SesionModel();

        $modulo = $moduloModel->find($modulo_id);
        if (!$modulo) {
            return redirect()->to('/cursos')->with('error', 'El módulo no existe.');
        }

        $data = [
            'modulo' => $modulo,
            'curso' => $cursoModel->find($modulo['curso_id']),
            'sesiones' => $sesionModel->where('modulo_id', $modulo_id)->orderBy('fecha', 'ASC')->orderBy('hora_inicio', 'ASC')->findAll(),
        ];

        return view('sesiones/index', $data);
    }

    /**
     * Procesa la creación de una nueva sesión.
     */
    public function create($modulo_id)
    {
        $rules = [
            'titulo' => 'required|min_length[3]',
            'fecha' => 'required|valid_date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'asistencia_habilitada' => 'required|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Verificación manual de la hora
        if (strtotime($this->request->getPost('hora_fin')) <= strtotime($this->request->getPost('hora_inicio'))) {
            return redirect()->back()->withInput()->with('errors', ['hora_fin' => 'La hora de fin debe ser posterior a la hora de inicio.']);
        }

        $sesionModel = new SesionModel();
        $sesionModel->save($this->request->getPost());

        return redirect()->to('modulos/' . $modulo_id . '/sesiones')->with('success', 'Sesión creada exitosamente.');
    }

    /**
     * Procesa la actualización de una sesión.
     */
    public function update($id) // Recibe el ID de la sesión
    {
        $rules = [ 'titulo' => 'required', 'fecha' => 'required|valid_date' ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        if (strtotime($this->request->getPost('hora_fin')) <= strtotime($this->request->getPost('hora_inicio'))) {
            return redirect()->back()->withInput()->with('errors', ['hora_fin' => 'La hora de fin debe ser posterior a la hora de inicio.']);
        }
        
        $sesionModel = new SesionModel();
        $sesion = $sesionModel->find($id);

        $sesionModel->update($id, $this->request->getPost());

        return redirect()->to('modulos/' . $sesion['modulo_id'] . '/sesiones')->with('success', 'Sesión actualizada exitosamente.');
    }

    /**
     * Elimina una sesión y sus asistencias asociadas.
     */
    public function delete($id)
    {
        $db = \Config\Database::connect();
        $sesionModel = new SesionModel();
        $asistenciaModel = new AsistenciaModel();

        $sesion = $sesionModel->find($id);
        if (!$sesion) {
            return redirect()->back()->with('error', 'La sesión no existe.');
        }

        $db->transStart();
        $asistenciaModel->where('sesion_id', $id)->delete();
        $sesionModel->delete($id);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('modulos/' . $sesion['modulo_id'] . '/sesiones')->with('error', 'No se pudo eliminar la sesión.');
        }

        return redirect()->to('modulos/' . $sesion['modulo_id'] . '/sesiones')->with('success', 'Sesión eliminada exitosamente.');
    }
}