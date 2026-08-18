<?php

namespace App\Http\Controllers;

use App\Models\QualityProfile;

class QualityNormController extends Controller
{
    public function index()
    {
        $profile = QualityProfile::current();

        $lastAuditChip = $profile->last_audit_date
            ? 'Última auditoría: ' . $profile->last_audit_date->format('d-m-Y')
            : 'Sin auditoría registrada';

        $sections = [
            [
                'title' => '4.1 Requisitos Generales',
                'items' => [
                    ['label' => 'Visión, Misión, Alcance, Última Auditoría, Documentación Legal', 'status' => 'done', 'route' => 'quality.profile.edit', 'chip' => $lastAuditChip],
                    ['label' => 'Mapa de Procesos', 'status' => 'done', 'route' => 'quality.profile.edit'],
                    ['label' => 'Organigrama', 'status' => 'done', 'route' => 'quality.profile.edit'],
                ],
            ],
            [
                'title' => '4.2 Requisitos de Documentación',
                'items' => [
                    ['label' => 'Manual de Calidad', 'status' => 'done', 'route' => 'quality.manual-calidad'],
                    ['label' => 'Control de Documentos (Procedimientos)', 'status' => 'done', 'route' => 'quality.procedures.index'],
                    ['label' => 'Listado de Documentos Externos', 'status' => 'done', 'route' => 'quality.external-documents.index'],
                    ['label' => 'Control de Registros', 'status' => 'done', 'route' => 'quality.records.index'],
                ],
            ],
            [
                'title' => '5.0 Responsabilidad de la Dirección',
                'items' => [
                    ['label' => 'Compromiso de la Dirección', 'status' => 'pending'],
                    ['label' => 'Política de Calidad', 'status' => 'pending'],
                    ['label' => 'Objetivos de Calidad', 'status' => 'pending'],
                    ['label' => 'Panel de Indicadores', 'status' => 'pending'],
                    ['label' => 'Planificación Estratégica', 'status' => 'pending'],
                    ['label' => 'Representante de la Dirección', 'status' => 'pending'],
                    ['label' => 'Comunicación Interna', 'status' => 'pending'],
                    ['label' => 'Revisión por la Dirección', 'status' => 'pending'],
                ],
            ],
            [
                'title' => '6.0 Gestión de Recursos',
                'items' => [
                    ['label' => 'Documentación de Relatores', 'status' => 'done', 'route' => 'instructors.index'],
                    ['label' => 'Perfil de Cargo', 'status' => 'pending'],
                    ['label' => 'Evaluación de Desempeño', 'status' => 'pending'],
                    ['label' => 'Plan de Formación', 'status' => 'pending'],
                    ['label' => 'Infraestructura / Checklist de Sala', 'status' => 'pending'],
                    ['label' => 'Requisitos Financieros', 'status' => 'pending'],
                ],
            ],
            [
                'title' => '7.0 Realización del Servicio de Capacitación',
                'items' => [
                    ['label' => 'Requerimiento del Cliente', 'status' => 'pending'],
                    ['label' => 'Formulario SENCE', 'status' => 'pending'],
                    ['label' => 'Proveedores', 'status' => 'done', 'route' => 'quality.providers.index'],
                    ['label' => 'Evaluación de Proveedores', 'status' => 'pending'],
                    ['label' => 'Libro de Clases', 'status' => 'done', 'route' => 'executions.index', 'note' => 'Dentro de cada Ejecución'],
                    ['label' => 'Reglamento del Curso / Material Didáctico', 'status' => 'pending'],
                    ['label' => 'Diploma del Curso', 'status' => 'done', 'route' => 'diplomas.index'],
                    ['label' => 'Certificado de Asistencia', 'status' => 'pending'],
                    ['label' => 'Registro de Bienes del Cliente', 'status' => 'pending'],
                    ['label' => 'Control de Equipos de Seguimiento y Medición', 'status' => 'pending'],
                ],
            ],
            [
                'title' => '8.0 Medición, Análisis y Mejora',
                'items' => [
                    ['label' => 'Programa de Auditoría', 'status' => 'pending'],
                    ['label' => 'Encuesta de Satisfacción del Alumno y Cliente', 'status' => 'partial', 'route' => 'executions.index', 'note' => 'Dentro de cada Ejecución'],
                    ['label' => 'Indicadores de Seguimiento y Medición', 'status' => 'pending'],
                    ['label' => 'No Conformidades', 'status' => 'done', 'route' => 'quality.non-conformities.index'],
                    ['label' => 'Acciones Correctivas / Preventivas', 'status' => 'done', 'route' => 'quality.non-conformities.index', 'note' => 'Dentro de cada No Conformidad'],
                    ['label' => 'Análisis de Datos', 'status' => 'pending'],
                    ['label' => 'Revisión por la Dirección', 'status' => 'pending'],
                ],
            ],
        ];

        return view('quality.norm', compact('sections'));
    }
}
