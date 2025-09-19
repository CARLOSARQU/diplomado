<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PagoComprobanteModel;
use App\Models\CursoModel;
use App\Models\UserModel;

class PagoAdminController extends BaseController
{
    protected $pagoModel;
    protected $cursoModel;
    protected $userModel;

    public function __construct()
    {
        $this->pagoModel = new PagoComprobanteModel();
        $this->cursoModel = new CursoModel();
        $this->userModel = new UserModel();
    }

    protected function esAdmin()
    {
        // Ajusta la condición a tu sesión (rol_nombre o rol_id)
        return session('rol_nombre') === 'superadmin' || session('rol_id') === 1;
    }

    public function index()
    {
        if (!$this->esAdmin()) {
            return redirect()->to('/mi-panel')->with('error', 'No tienes acceso a esta sección.');
        }

        // Obtener filtros desde GET
        $filtros = [
            'estado' => $this->request->getGet('estado'),
            'curso_id' => $this->request->getGet('curso_id'),
            'metodo_pago' => $this->request->getGet('metodo_pago'),
            'fecha_desde' => $this->request->getGet('fecha_desde'),
            'fecha_hasta' => $this->request->getGet('fecha_hasta'),
        ];

        // Transformar fechas a filtro del modelo (si las usas en getPagosAdmin)
        $pagos = $this->pagoModel->getPagosAdmin($filtros);
        $cursos = $this->cursoModel->orderBy('nombre')->findAll();

        $data = [
            'pagos' => $pagos,
            'cursos' => $cursos,
            'filtros' => $filtros
        ];

        return view('pagos/index', $data);
    }

    public function revisar($id)
    {
        if (!$this->esAdmin()) {
            return redirect()->to('/mi-panel')->with('error', 'No tienes acceso a esta sección.');
        }

        $pago = $this->pagoModel->getPagoDetalle($id);
        if (!$pago) {
            return redirect()->back()->with('error', 'Comprobante no encontrado.');
        }

        $metodosPago = $this->pagoModel->getMetodosPago();

        return view('pagos/revisar', [
            'pago' => $pago,
            'metodosPago' => $metodosPago
        ]);
    }

    public function aprobar($id)
    {
        if (!$this->esAdmin()) {
            return redirect()->to('/mi-panel')->with('error', 'No tienes acceso a esta sección.');
        }

        $observaciones = $this->request->getPost('observaciones_admin');
        $admin_id = session('user_id');

        $this->pagoModel->aprobarPago($id, $admin_id, $observaciones);

        return redirect()->back()->with('success', 'Pago aprobado correctamente.');
    }

    public function rechazar($id)
    {
        if (!$this->esAdmin()) {
            return redirect()->to('/mi-panel')->with('error', 'No tienes acceso a esta sección.');
        }

        $observaciones = $this->request->getPost('observaciones_admin');
        if (empty(trim($observaciones))) {
            return redirect()->back()->with('error', 'Debes indicar el motivo del rechazo.');
        }

        $admin_id = session('user_id');
        $this->pagoModel->rechazarPago($id, $admin_id, $observaciones);

        return redirect()->back()->with('success', 'Pago rechazado y participante notificado.');
    }
    public function editarDatos($id)
    {
        if (!$this->esAdmin()) {
            return redirect()->to('/mi-panel')->with('error', 'No tienes acceso a esta sección.');
        }

        $pago = $this->pagoModel->find($id);
        if (!$pago) {
            return redirect()->back()->with('error', 'Comprobante no encontrado.');
        }

        $metodo_pago = $this->request->getPost('metodo_pago');
        $identificador_pago = $this->request->getPost('identificador_pago');
        $monto = $this->request->getPost('monto');

        // Validar datos
        $datos = [];
        $errores = [];

        // Validar método de pago
        if (!empty($metodo_pago)) {
            $metodos_validos = array_keys($this->pagoModel->getMetodosPago());
            if (in_array($metodo_pago, $metodos_validos)) {
                $datos['metodo_pago'] = $metodo_pago;
            } else {
                $errores[] = 'Método de pago no válido.';
            }
        }

        // Validar monto
        if (!empty($monto)) {
            $monto = floatval($monto);
            
            if ($monto <= 0) {
                $errores[] = 'El monto debe ser mayor a 0.';
            } elseif ($monto > 99999.99) {
                $errores[] = 'El monto no puede ser mayor a S/ 99,999.99.';
            } else {
                $datos['monto'] = $monto;
            }
        }

        // Validar identificador de pago
        if (!empty($identificador_pago)) {
            $identificador_pago = trim($identificador_pago);
            
            if (strlen($identificador_pago) < 5 || strlen($identificador_pago) > 20) {
                $errores[] = 'El identificador debe tener entre 5 y 20 caracteres.';
            } elseif (!ctype_alnum($identificador_pago)) {
                $errores[] = 'El identificador solo puede contener letras y números.';
            } elseif ($this->pagoModel->identificadorExiste($identificador_pago, $id)) {
                $errores[] = 'Este identificador ya existe para otro pago.';
            } else {
                $datos['identificador_pago'] = $identificador_pago;
            }
        }

        // Validar fecha de pago
        $fecha_pago = $this->request->getPost('fecha_pago');
        if (!empty($fecha_pago)) {
            $fecha_pago = trim($fecha_pago);
            
            // Validar formato de fecha
            $fecha_obj = \DateTime::createFromFormat('Y-m-d', $fecha_pago);
            if (!$fecha_obj || $fecha_obj->format('Y-m-d') !== $fecha_pago) {
                $errores[] = 'La fecha de pago debe tener un formato válido (YYYY-MM-DD).';
            } elseif ($fecha_obj > new \DateTime()) {
                $errores[] = 'La fecha de pago no puede ser futura.';
            } elseif ($fecha_obj < new \DateTime('2020-01-01')) {
                $errores[] = 'La fecha de pago no puede ser anterior al año 2020.';
            } else {
                $datos['fecha_pago'] = $fecha_pago;
            }
        }

        if (!empty($errores)) {
            return redirect()->back()->with('error', implode(' ', $errores));
        }

        if (empty($datos)) {
            return redirect()->back()->with('error', 'No se proporcionaron datos para actualizar.');
        }

        // Actualizar datos
        if ($this->pagoModel->editarDatosPago($id, $datos)) {
            return redirect()->back()->with('success', 'Datos del pago actualizados correctamente.');
        } else {
            return redirect()->back()->with('error', 'Error al actualizar los datos del pago.');
        }
    }

    public function reportes()
    {
        if (!$this->esAdmin()) {
            return redirect()->to('/mi-panel')->with('error', 'No tienes acceso a esta sección.');
        }

        $ingresos = $this->pagoModel->ingresosPorPeriodo();
        $estadoCursoModulo = $this->pagoModel->estadoPorCursoModulo();
        $participantesPendientes = $this->pagoModel->participantesConPendientes();

        return view('pagos/reportes', [
            'ingresos' => $ingresos,
            'estadoCursoModulo' => $estadoCursoModulo,
            'participantesPendientes' => $participantesPendientes
        ]);
    }
}
