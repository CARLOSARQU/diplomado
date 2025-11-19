<?php
namespace App\Controllers;
use App\Models\PagoComprobanteModel;
use App\Models\ModuloModel;
use App\Models\CursoParticipanteModel;

class PagoController extends BaseController
{
    /**
     * Muestra los pagos del participante
     */
    public function index()
    {
        if (session('rol_nombre') !== 'usuario') {
            return redirect()->to('/')->with('error', 'No tienes acceso a esta sección.');
        }

        $participante_id = session('user_id');
        $pagoModel = new PagoComprobanteModel();

        // Obtener pagos existentes
        $pagos_existentes = $pagoModel->getPagosByParticipante($participante_id);

        // Obtener módulos sin pago O con pagos rechazados
        $modulos_sin_pago = $pagoModel->getModulosSinPagoORechazo($participante_id);

        $data = [
            'pagos_existentes' => $pagos_existentes,
            'modulos_sin_pago' => $modulos_sin_pago
        ];

        return view('participante/mis-pagos', $data);
    }

    /**
     * Muestra el formulario para subir comprobante
     */
    public function subirComprobante($modulo_id)
    {
        if (session('rol_nombre') !== 'usuario') {
            return redirect()->to('/mi-panel')->with('error', 'No tienes acceso a esta sección.');
        }

        $participante_id = session('user_id');
        $moduloModel = new ModuloModel();
        $pagoModel = new PagoComprobanteModel();
        $cursoParticipanteModel = new CursoParticipanteModel();

        // Verificar que el módulo existe
        $modulo = $moduloModel->select('modulos.*, cursos.nombre as curso_nombre')
                             ->join('cursos', 'cursos.id = modulos.curso_id')
                             ->find($modulo_id);

        if (!$modulo) {
            return redirect()->back()->with('error', 'El módulo no existe.');
        }

        // Verificar que está inscrito en el curso
        $inscrito = $cursoParticipanteModel->where('curso_id', $modulo['curso_id'])
                                          ->where('participante_id', $participante_id)
                                          ->first();

        if (!$inscrito) {
            return redirect()->back()->with('error', 'No estás inscrito en este curso.');
        }

        // Verificar si existe un pago previo (para mostrar info del rechazo si aplica)
        $pago_anterior = $pagoModel->where('participante_id', $participante_id)
                                   ->where('modulo_id', $modulo_id)
                                   ->first();

        // Solo bloquear si tiene un pago APROBADO o EN REVISIÓN
        if ($pago_anterior && in_array($pago_anterior['estado'], ['aprobado', 'en_revision'])) {
            return redirect()->back()->with('error', 'Ya tienes un comprobante en proceso o aprobado para este módulo.');
        }

        $data = [
            'modulo' => $modulo,
            'pago_rechazado' => ($pago_anterior && $pago_anterior['estado'] === 'rechazado') ? $pago_anterior : null
        ];

        return view('participante/subir-comprobante', $data);
    }

    /**
     * Procesa la subida del comprobante
     */
    public function procesarComprobante()
    {
        if (session('rol_nombre') !== 'usuario') {
            return redirect()->to('/')->with('error', 'No tienes acceso a esta sección.');
        }

        $participante_id = session('user_id');
        $pagoModel = new PagoComprobanteModel();

        // Validar datos
        $rules = [
            'modulo_id' => 'required|integer',
            'monto' => 'required|decimal|greater_than[0]',
            'fecha_pago' => 'required|valid_date',
            'comprobante' => 'uploaded[comprobante]|max_size[comprobante,5120]|ext_in[comprobante,jpg,jpeg,png,pdf]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $modulo_id = $this->request->getPost('modulo_id');

        // Verificar si hay un pago anterior
        $pago_anterior = $pagoModel->where('participante_id', $participante_id)
                                   ->where('modulo_id', $modulo_id)
                                   ->first();

        // Solo bloquear si existe y está aprobado o en revisión
        if ($pago_anterior && in_array($pago_anterior['estado'], ['aprobado', 'en_revision'])) {
            return redirect()->back()->with('error', 'Ya tienes un comprobante en proceso o aprobado para este módulo.');
        }

        // Subir archivo
        $archivo = $this->request->getFile('comprobante');
        if ($archivo && $archivo->isValid()) {
            $nombreArchivo = $participante_id . '_' . $modulo_id . '_' . time() . '.' . $archivo->getExtension();
            
            // Crear directorio si no existe
            $uploadPath = FCPATH . 'uploads/comprobantes/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if ($archivo->move($uploadPath, $nombreArchivo)) {
                $data = [
                    'participante_id' => $participante_id,
                    'modulo_id' => $modulo_id,
                    'monto' => $this->request->getPost('monto'),
                    'identificador_pago' => null,
                    'metodo_pago' => null,
                    'archivo_comprobante' => $nombreArchivo,
                    'fecha_pago' => $this->request->getPost('fecha_pago'),
                    'observaciones' => $this->request->getPost('observaciones'),
                    'estado' => 'en_revision'
                ];

                // Si existe un pago rechazado, actualizarlo en lugar de crear uno nuevo
                if ($pago_anterior && $pago_anterior['estado'] === 'rechazado') {
                    // Eliminar archivo antiguo si existe
                    if (!empty($pago_anterior['archivo_comprobante'])) {
                        $archivoAntiguo = $uploadPath . $pago_anterior['archivo_comprobante'];
                        if (file_exists($archivoAntiguo)) {
                            @unlink($archivoAntiguo);
                        }
                    }

                    // Actualizar el registro existente
                    $data['motivo_rechazo'] = null; // Limpiar motivo de rechazo anterior
                    if ($pagoModel->update($pago_anterior['id'], $data)) {
                        return redirect()->to('participante/mis-pagos')
                                       ->with('success', 'Comprobante reenviado exitosamente. Está en revisión nuevamente.');
                    }
                } else {
                    // Crear nuevo registro
                    if ($pagoModel->insert($data)) {
                        return redirect()->to('participante/mis-pagos')
                                       ->with('success', 'Comprobante subido exitosamente. Está en revisión.');
                    }
                }
            }
        }

        return redirect()->back()->with('error', 'Error al subir el comprobante.');
    }
}