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
                    ['number' => '4.1', 'label' => 'Visión, Misión, Alcance, Última Auditoría, Documentación Legal', 'status' => 'done', 'route' => 'quality.profile.edit', 'chip' => $lastAuditChip],
                    ['number' => '4.1', 'label' => 'Mapa de Procesos', 'status' => 'done', 'route' => 'quality.profile.edit'],
                    ['number' => '4.1', 'label' => 'Organigrama', 'status' => 'done', 'route' => 'quality.profile.edit'],
                ],
            ],
            [
                'title' => '4.2 Requisitos de Documentación',
                'items' => [
                    ['number' => '4.2.2', 'label' => 'Manual de Calidad', 'status' => 'done', 'route' => 'quality.manual-calidad'],
                    ['number' => '4.2.3', 'label' => 'Control de Documentos — Procedimientos', 'status' => 'done', 'route' => 'quality.procedures.index'],
                    ['number' => '4.2.3', 'indent' => 1, 'label' => 'Listado de Documentos Externos', 'status' => 'done', 'route' => 'quality.external-documents.index'],
                    ['number' => '4.2.4', 'label' => 'Control de Registros', 'status' => 'done', 'route' => 'quality.records.index'],
                ],
            ],
            [
                'title' => '5.0 Responsabilidad de la Dirección',
                'items' => [
                    ['number' => '5.1', 'type' => 'header', 'label' => 'Compromiso de la dirección'],
                    ['number' => '5.2', 'label' => 'Enfoque al usuario — Requerimiento del Cliente', 'status' => 'done', 'route' => 'quality.requirements.index'],
                    ['number' => '5.4', 'label' => 'Planificación — Política de Calidad', 'status' => 'done', 'route' => 'quality.policy.edit'],
                    ['number' => '5.4', 'indent' => 1, 'label' => 'Objetivos de Calidad', 'status' => 'done', 'route' => 'quality.policy.edit', 'note' => 'Anidados dentro de cada Compromiso, en la misma pantalla'],
                    ['number' => '5.4', 'indent' => 2, 'label' => 'Panel de Indicadores', 'status' => 'pending'],
                    ['number' => '5.4', 'indent' => 2, 'label' => 'Planificación Estratégica', 'status' => 'done', 'route' => 'quality.strategic-plan'],
                    ['number' => '5.5', 'type' => 'header', 'label' => 'Responsabilidad, Autoridad y Comunicación'],
                    ['number' => '5.5.1', 'label' => 'Organigrama', 'status' => 'done', 'route' => 'quality.profile.edit', 'routeParams' => ['tab' => 'org-chart'], 'note' => 'Mismo Organigrama de Requisitos Generales (4.1)'],
                    ['number' => '5.5.2', 'label' => 'Representante de la Dirección', 'status' => 'done', 'route' => 'quality.representatives.index'],
                    ['number' => '5.5.3', 'label' => 'Comunicación interna', 'status' => 'done', 'route' => 'quality.internal-communications.index'],
                    ['number' => '5.6', 'label' => 'Revisión por la Dirección', 'status' => 'pending'],
                ],
            ],
            [
                'title' => '6.0 Gestión de Recursos',
                'items' => [
                    ['number' => '6.1', 'label' => 'Provisión de Recursos — Presupuestos de Cursos', 'status' => 'done', 'route' => 'budgets.index'],
                    ['number' => '6.2', 'label' => 'Recursos Humanos — Documentación Colaboradores del OTEC', 'status' => 'done', 'route' => 'settings.users.index', 'note' => 'Se unificó con Usuarios del sistema'],
                    ['number' => '6.2', 'indent' => 1, 'label' => 'Documentación de Relatores', 'status' => 'done', 'route' => 'instructors.index'],
                    ['number' => '6.2', 'indent' => 1, 'label' => 'Perfil de Cargo', 'status' => 'done', 'route' => 'quality.job-profiles.index'],
                    ['number' => '6.2', 'indent' => 1, 'label' => 'Evaluación de Desempeño de los Colaboradores', 'status' => 'done', 'route' => 'quality.surveys.index'],
                    ['number' => '6.2', 'indent' => 1, 'label' => 'Plan de Formación', 'status' => 'done', 'route' => 'quality.internal-trainings.index'],
                    ['number' => '6.3', 'label' => 'Provisión de Recursos — Infraestructura: Check-List de Sala de Clases', 'status' => 'done', 'route' => 'quality.checklist.index'],
                    ['number' => '6.4', 'type' => 'header', 'label' => 'Ambiente de trabajo'],
                    ['number' => '6.5', 'label' => 'Requisitos Financieros', 'status' => 'done', 'route' => 'quality.financial-documents'],
                ],
            ],
            [
                'title' => '7.0 Realización del Servicio de Capacitación',
                'items' => [
                    ['number' => '7.1', 'label' => 'Planificación de la Realización del Servicio — Requerimiento del Cliente', 'status' => 'done', 'route' => 'quality.requirements.index', 'note' => 'Mismo registro de Requerimientos del Cliente (5.2)'],
                    ['number' => '7.2', 'label' => 'Procesos Relacionados con el Cliente — Levantamiento del Requerimiento', 'status' => 'pending'],
                    ['number' => '7.3', 'label' => 'Diseño y Desarrollo — Formulario SENCE', 'status' => 'done', 'route' => 'courses.index'],
                    ['number' => '7.4', 'label' => 'Compra — Listado de Proveedores', 'status' => 'done', 'route' => 'quality.providers.index'],
                    ['number' => '7.4', 'indent' => 1, 'label' => 'Evaluación de Proveedores', 'status' => 'pending'],
                    ['number' => '7.4', 'indent' => 1, 'label' => 'Formato de Solicitud de Cotizaciones', 'status' => 'done', 'route' => 'budgets.index', 'note' => 'Se realiza desde el módulo de Presupuestos'],
                    ['number' => '7.5', 'label' => 'Producción y Prestación del Servicio — Libro de Clases', 'status' => 'done', 'route' => 'executions.index', 'note' => 'Dentro de cada Ejecución'],
                    ['number' => '7.5', 'indent' => 1, 'label' => 'Reglamento del Curso y Material Didáctico', 'status' => 'pending'],
                    ['number' => '7.5', 'indent' => 1, 'label' => 'Diploma del Curso', 'status' => 'done', 'route' => 'diplomas.index'],
                    ['number' => '7.5', 'indent' => 1, 'label' => 'Certificado de Asistencia', 'status' => 'pending'],
                    ['number' => '7.5', 'indent' => 1, 'label' => 'Registro de Bienes de Propiedad del Cliente', 'status' => 'pending'],
                    ['number' => '7.6', 'label' => 'Control de Equipos de Seguimiento y Medición', 'status' => 'pending'],
                ],
            ],
            [
                'title' => '8.0 Medición, Análisis y Mejora',
                'items' => [
                    ['label' => 'Programa de Auditoría', 'status' => 'pending'],
                    ['label' => 'Encuesta de Satisfacción del Alumno y Cliente', 'status' => 'partial', 'route' => 'quality.surveys.index', 'note' => 'Alumno + Gerencia listo. Falta Encuesta a Clientes (empresas)'],
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
