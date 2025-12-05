<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CursoParticipanteModel;
use App\Models\ModuloModel;
use App\Models\NotaModuloModel;
use App\Models\PagoComprobanteModel;
use App\Models\CursoModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ReporteCursoController extends BaseController
{
    public function detalleCurso($curso_id)
    {
        // 1. Instancias
        $cursoModel = new CursoModel();
        $cursoPartModel = new CursoParticipanteModel();
        $moduloModel = new ModuloModel();
        $notaModel = new NotaModuloModel();
        $pagoModel = new PagoComprobanteModel();

        // 2. Información del Curso
        $curso = $cursoModel->find($curso_id);
        if (!$curso) {
            return redirect()->back()->with('error', 'Curso no encontrado');
        }

        // 3. Obtener Módulos
        $modulos = $moduloModel->where('curso_id', $curso_id)
            ->orderBy('orden', 'ASC')
            ->findAll();

        // 4. Obtener Participantes
        $participantes = $cursoPartModel->getParticipantesByCurso($curso_id);

        // 5. Estructurar Datos y KPIs
        $reporteData = [];

        $totalRecaudado = 0;
        $totalAprobados = 0;
        $totalNotasRegistradas = 0;

        foreach ($participantes as $index => $p) {
            $uid = $p['participante_id'];

            // Verificamos si la propiedad existe, sino asumimos activo (1)
            $is_active = isset($p['is_active']) ? $p['is_active'] : 1;

            $fila = [
                'numero' => $index + 1,
                'participante_id' => $uid,
                'dni' => $p['dni'],
                'nombre' => $p['apellidos'] . ', ' . $p['nombres'],
                'activo' => $is_active, // Pasamos el estado a la vista
                'modulos' => []
            ];

            foreach ($modulos as $mod) {
                $mid = $mod['id'];

                // a. Obtener Nota
                $notaReg = $notaModel->getNotaParticipanteByModulo($uid, $mid);
                $valorNota = $notaReg ? $notaReg['nota'] : null;

                // KPI Nota (Solo contamos para KPIs si está activo, opcionalmente)
                if ($valorNota !== null && $is_active == 1) {
                    $totalNotasRegistradas++;
                    if ($valorNota >= 11)
                        $totalAprobados++;
                }

                // b. Obtener Pago
                $pagoReg = $pagoModel->where('participante_id', $uid)
                    ->where('modulo_id', $mid)
                    ->where('estado', 'aprobado')
                    ->first();

                $monto = $pagoReg ? $pagoReg['monto'] : 0;

                // Sumamos al recaudado incluso si se retiró (el dinero ya ingresó)
                $totalRecaudado += $monto;

                $fila['modulos'][] = [
                    'monto' => $monto,
                    'nota' => $valorNota,
                    'estado_pago' => $pagoReg ? 'aprobado' : 'pendiente'
                ];
            }
            $reporteData[] = $fila;
        }

        $porcentajeAprobacion = ($totalNotasRegistradas > 0)
            ? round(($totalAprobados / $totalNotasRegistradas) * 100, 1)
            : 0;

        $data = [
            'titulo' => 'Reporte Académico y Financiero',
            'curso' => $curso,
            'modulos' => $modulos,
            'reporte' => $reporteData,
            'total_participantes' => count($participantes),
            'total_recaudado' => $totalRecaudado,
            'porcentaje_aprobacion' => $porcentajeAprobacion,
            'total_modulos' => count($modulos)
        ];

        return view('reportes/curso_completo', $data);
    }
    public function cambiarEstadoParticipante()
    {
        $curso_id = $this->request->getPost('curso_id');
        $participante_id = $this->request->getPost('participante_id');
        $nuevo_estado = $this->request->getPost('nuevo_estado'); // 0 para retirar, 1 para activar

        if (!$curso_id || !$participante_id) {
            return redirect()->back()->with('error', 'Datos incompletos.');
        }

        $cursoPartModel = new CursoParticipanteModel();

        // Actualizamos el campo is_active
        $cursoPartModel->where('curso_id', $curso_id)
            ->where('participante_id', $participante_id)
            ->set(['is_active' => $nuevo_estado])
            ->update();

        $mensaje = ($nuevo_estado == 0) ? 'Participante retirado correctamente.' : 'Participante reactivado correctamente.';

        return redirect()->back()->with('success', $mensaje);
    }
    public function exportarExcel($curso_id)
    {
        $cursoModel = new CursoModel();
        $cursoPartModel = new CursoParticipanteModel();
        $moduloModel = new ModuloModel();
        $notaModel = new NotaModuloModel();
        $pagoModel = new PagoComprobanteModel();

        $curso = $cursoModel->find($curso_id);
        if (!$curso)
            return redirect()->back();

        $modulos = $moduloModel->where('curso_id', $curso_id)->orderBy('orden', 'ASC')->findAll();
        $participantes = $cursoPartModel->getParticipantesByCurso($curso_id);

        // CREAR DOCUMENTO
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ESTILOS
        $styleHeader = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $styleModuleHeader = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FF000000']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDCE6F1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $styleBorder = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]];

        $styleRetirado = [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFEAEA']],
            'font' => ['color' => ['argb' => 'FFB02A37'], 'strikethrough' => true],
        ];

        $styleDesaprobado = ['font' => ['bold' => true, 'color' => ['argb' => 'FFFF0000']]];
        $styleAprobado = ['font' => ['bold' => true, 'color' => ['argb' => 'FF0000FF']]];

        // ENCABEZADO
        $sheet->setCellValue('A1', 'REPORTE DE NOTAS Y PAGOS - ' . mb_strtoupper($curso['nombre']));
        $sheet->setCellValue('A2', 'Generado el: ' . date('d/m/Y H:i'));

        $totalModulos = count($modulos);
        $lastColIndex = 3 + ($totalModulos * 2);
        $lastColStr = Coordinate::stringFromColumnIndex($lastColIndex); // Corrección v2.0

        $sheet->mergeCells("A1:{$lastColStr}1");
        $sheet->mergeCells("A2:{$lastColStr}2");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // TABLA HEADERS
        $rowHead = 4;

        $sheet->setCellValue('A' . $rowHead, 'N°');
        $sheet->setCellValue('B' . $rowHead, 'DNI');
        $sheet->setCellValue('C' . $rowHead, 'PARTICIPANTE');

        $sheet->mergeCells("A{$rowHead}:A" . ($rowHead + 1));
        $sheet->mergeCells("B{$rowHead}:B" . ($rowHead + 1));
        $sheet->mergeCells("C{$rowHead}:C" . ($rowHead + 1));

        $col = 4;
        foreach ($modulos as $mod) {
            $colLetraInicio = Coordinate::stringFromColumnIndex($col);
            $colLetraFin = Coordinate::stringFromColumnIndex($col + 1);

            // Usamos setCellValue con coordenadas tipo 'D4'
            $sheet->setCellValue($colLetraInicio . $rowHead, 'MÓDULO ' . $mod['orden']);
            $sheet->mergeCells("{$colLetraInicio}{$rowHead}:{$colLetraFin}{$rowHead}");

            $sheet->setCellValue($colLetraInicio . ($rowHead + 1), 'MONTO');
            $sheet->setCellValue($colLetraFin . ($rowHead + 1), 'NOTA');

            $col += 2;
        }

        $sheet->getStyle("A{$rowHead}:C" . ($rowHead + 1))->applyFromArray($styleHeader);
        $sheet->getStyle("D{$rowHead}:{$lastColStr}" . ($rowHead + 1))->applyFromArray($styleModuleHeader);

        // CONTENIDO
        $currentRow = 6;
        $num = 1;

        foreach ($participantes as $p) {
            $uid = $p['participante_id'];
            $is_active = isset($p['is_active']) ? $p['is_active'] : 1;

            $sheet->setCellValue('A' . $currentRow, $num++);
            $sheet->setCellValue('B' . $currentRow, $p['dni']);

            $nombreDisplay = $p['apellidos'] . ', ' . $p['nombres'];
            if ($is_active == 0)
                $nombreDisplay .= " (RETIRADO)";
            $sheet->setCellValue('C' . $currentRow, $nombreDisplay);

            $range = "A{$currentRow}:{$lastColStr}{$currentRow}";
            $sheet->getStyle($range)->applyFromArray($styleBorder);

            if ($is_active == 0) {
                $sheet->getStyle($range)->applyFromArray($styleRetirado);
            }

            $col = 4;
            foreach ($modulos as $mod) {
                $mid = $mod['id'];
                $colLetraMonto = Coordinate::stringFromColumnIndex($col);
                $colLetraNota = Coordinate::stringFromColumnIndex($col + 1);

                // Pago
                $pagoReg = $pagoModel->where('participante_id', $uid)
                    ->where('modulo_id', $mid)
                    ->where('estado', 'aprobado')
                    ->first();
                $monto = $pagoReg ? $pagoReg['monto'] : 0;

                // Nota
                $notaReg = $notaModel->getNotaParticipanteByModulo($uid, $mid);
                $nota = $notaReg ? $notaReg['nota'] : null;

                // Monto
                if ($monto > 0) {
                    $sheet->setCellValue($colLetraMonto . $currentRow, $monto);
                    $sheet->getStyle($colLetraMonto . $currentRow)->getNumberFormat()->setFormatCode('#,##0.00');
                } else {
                    $sheet->setCellValue($colLetraMonto . $currentRow, '-');
                    $sheet->getStyle($colLetraMonto . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Nota
                if ($nota !== null) {
                    $sheet->setCellValue($colLetraNota . $currentRow, $nota);

                    if ($nota < 11) {
                        $sheet->getStyle($colLetraNota . $currentRow)->applyFromArray($styleDesaprobado);
                    } else {
                        $sheet->getStyle($colLetraNota . $currentRow)->applyFromArray($styleAprobado);
                    }
                    $sheet->getStyle($colLetraNota . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                } else {
                    $sheet->setCellValue($colLetraNota . $currentRow, '-');
                    $sheet->getStyle($colLetraNota . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $col += 2;
            }

            $currentRow++;
        }

        // AUTO SIZE
        foreach (range('A', 'C') as $colID) {
            $sheet->getColumnDimension($colID)->setAutoSize(true);
        }
        for ($i = 4; $i <= $lastColIndex; $i++) {
            $colStr = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colStr)->setWidth(12);
        }

        // DESCARGA
        $writer = new Xlsx($spreadsheet);
        $filename = 'Reporte_Curso_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}