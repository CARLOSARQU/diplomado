<?php

namespace App\Controllers;

use App\Models\CursoModel;
use App\Models\ModuloModel;
use App\Models\SesionModel; // Necesario para el borrado en cascada

class ModuloController extends BaseController
{
    /**
     * Muestra la lista de módulos para un curso específico.
     */
    public function index($curso_id)
    {
        $cursoModel = new CursoModel();
        $moduloModel = new ModuloModel();

        $data = [
            'curso' => $cursoModel->find($curso_id),
            'modulos' => $moduloModel->where('curso_id', $curso_id)->orderBy('orden', 'ASC')->findAll(),
        ];

        // Si el curso no existe, redirigir o mostrar error
        if ($data['curso'] === null) {
            return redirect()->to('/cursos')->with('error', 'El curso no existe.');
        }

        return view('modulos/index', $data);
    }

    /**
     * Procesa la creación de un nuevo módulo.
     */
    public function create($curso_id)
    {
        $rules = [
            'nombre' => 'required|min_length[3]',
            'orden' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $moduloModel = new ModuloModel();
        $moduloModel->save([
            'curso_id' => $curso_id,
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
            'orden' => $this->request->getPost('orden'),
        ]);

        return redirect()->to('cursos/' . $curso_id . '/modulos')->with('success', 'Módulo creado exitosamente.');
    }

    /**
     * Procesa la actualización de un módulo.
     */
    public function update($id) // Recibe el ID del módulo
    {
        $rules = [
            'nombre' => 'required|min_length[3]',
            'orden' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $moduloModel = new ModuloModel();
        
        // Obtenemos el curso_id para poder redirigir correctamente
        $modulo = $moduloModel->find($id);
        if (!$modulo) {
            return redirect()->back()->with('error', 'El módulo no existe.');
        }

        $moduloModel->update($id, [
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
            'orden' => $this->request->getPost('orden'),
        ]);

        return redirect()->to('cursos/' . $modulo['curso_id'] . '/modulos')->with('success', 'Módulo actualizado exitosamente.');
    }

    /**
     * Elimina un módulo y sus sesiones asociadas.
     */
    public function delete($id)
    {
        $db = \Config\Database::connect();
        $moduloModel = new ModuloModel();
        $sesionModel = new SesionModel();

        $modulo = $moduloModel->find($id);
        if (!$modulo) {
            return redirect()->back()->with('error', 'El módulo no existe.');
        }

        $db->transStart();
        // Primero, borramos las sesiones que pertenecen a este módulo
        $sesionModel->where('modulo_id', $id)->delete();
        // Luego, borramos el módulo
        $moduloModel->delete($id);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('cursos/' . $modulo['curso_id'] . '/modulos')->with('error', 'No se pudo eliminar el módulo.');
        }

        return redirect()->to('cursos/' . $modulo['curso_id'] . '/modulos')->with('success', 'Módulo eliminado exitosamente.');
    }
}