# Proyecto OTEC — ERP/LMS (MVP)

## Contexto general

Plataforma ERP/LMS para una **OTEC chilena**, enfocada en administrar todo el ciclo de vida de una capacitación. Es la base para un futuro sistema comercial, pero el foco actual es un **MVP funcional**.

Prioridad: que funcione correctamente. Arquitectura limpia, modular y fácil de mantener — no extremadamente compleja.

## Stack tecnológico

- Laravel 11
- PHP 8.3+
- Blade
- Tailwind CSS
- Alpine.js
- MySQL (local vía XAMPP, puerto 3307)
- Laravel Breeze
- Componentes Blade reutilizables (`x-ui.*`)

## Entorno local

- Corro con XAMPP en Mac (MySQL en puerto 3307, DB `otec`)
- Para levantar todo junto: `composer run dev` (corre `php artisan serve` + `queue:listen` + `pail` + `npm run dev` en paralelo)
- **Requisito:** MySQL de XAMPP debe estar corriendo antes de levantar el proyecto

## Componentes UI existentes

Ya existen y deben reutilizarse siempre que sea posible:

```
x-ui.card
x-ui.section
x-ui.section-header
x-ui.stat-card
x-ui.empty-state
```

Pendiente de crear: `x-ui.alert` (para reemplazar todos los mensajes flash).

## Filosofía del proyecto

- NO es un CRUD genérico: es un ERP especializado para OTEC.
- Código simple, modular, desacoplado, fácil de mantener.
- Evitar sobreingeniería. No usar patrones innecesarios para un MVP (nada de DTO, Repository, etc. salvo que se justifique explícitamente).

## Módulos existentes

- Empresas
- Cursos
- Participantes
- Relatores
- Presupuestos
- Plantillas de diplomas
- Usuarios
- Configuración
- Ejecuciones (en desarrollo activo, ver detalle abajo)

## Módulo Ejecuciones — arquitectura

Una ejecución contiene actualmente:

```
General
Planificación
Sesiones
Participantes
Relatores
```

Pendiente de construir más adelante:

```
Asistencia
Libro de clases
Evaluaciones
Encuestas
Diplomas
Cierre
```

### Decisión de arquitectura (refactor ya aplicado)

Antes, la planificación estaba mezclada dentro de `Execution`. Se separó completamente:

```
Execution
  ↓
ExecutionPlanning
  ↓
ExecutionCalendarService
  ↓
ExecutionSessions
```

No usar DTO, Repository ni patrones complejos. Es un MVP.

### Base de datos

**`execution_plannings`** (1:1 con execution, unique en `execution_id`):
- `execution_id`
- `mode` (enum: automatic | manual)
- `start_date`
- `start_time`
- `hours_per_day`
- `week_days` (json, array de 1-7, 1=Lunes...7=Domingo)
- `exclude_holidays` (boolean)

**`execution_sessions`** — toda la plataforma dependerá de esta tabla:
- `execution_id`
- `session_date`
- `start_time`
- `end_time`
- `hours`
- `instructor_id`

### Modelos

- `ExecutionPlanning` — relación `Execution::planning()` → `hasOne`
- `ExecutionSession`

### Servicios

**`ExecutionPlanningService`** — responsabilidad única: guardar / actualizar / eliminar la planificación. NO genera sesiones.

**`ExecutionCalendarService`** — lee desde `ExecutionPlanning` (no desde `Execution`). Responsabilidad:
```
preview()
generate()
build()
calculateEndTime()
isWorkingDay()
```
No hace otra cosa.

### Controladores

**`ExecutionPlanningController`**:
- `update()` — guardar planificación
- `generate()` — generar agenda
- Pendiente: `preview()`, `regenerate()`

**`ExecutionController`** debe quedar SOLO con:
```
CRUD
Participantes
Relatores
```
Nunca más lógica de planificación ahí.

### UI — Pestaña Planificación

Ya NO usa `planning_start_time`, `exclude_saturdays`, `exclude_sundays` (campos deprecados que van a desaparecer). Usa exclusivamente `ExecutionPlanning` con selección libre de días de la semana (checkboxes 1-7), no "excluir sábado/domingo".

Secciones de la UI:
```
Resumen (horas curso, horas jornada, inicio, término)
Configuración (fecha inicio, hora inicio, horas jornada)
Días (checkboxes lunes a domingo, cualquier combinación)
Opciones (excluir feriados)
Botones (Guardar planificación / Generar agenda)
```

## Flujo definitivo del módulo Ejecuciones

```
Crear ejecución → Guardar → Entrar a la ejecución → Planificación
→ Guardar planificación → Generar agenda → Sesiones
→ Asistencia → Libro → Evaluaciones → Diplomas
```

## Reglas de negocio

- La agenda siempre se genera desde la planificación.
- Las sesiones pueden editarse manualmente después de generadas.
- Cada modificación debe recalcular automáticamente: horas planificadas, horas restantes, horas excedidas.
- Toda la plataforma depende de `execution_sessions`, sin importar si la sesión fue creada automática o manualmente — el resto del sistema nunca debe saber cómo fue creada.
- La validación de horas **informa**, no bloquea (horas requeridas / planificadas / faltantes / excedidas).

## Casos de uso del módulo Ejecuciones (orden de trabajo)

1. ✅ **Guardar planificación** — VALIDADO. Flujo Form → Controller → Service → `ExecutionPlanning` funciona correctamente.
2. ⏳ **Generar agenda** — EN CURSO. Debe crear sesiones respetando días seleccionados, excluir feriados, ajustar automáticamente la última sesión.
3. Editar sesión.
4. Agregar sesión manual.
5. Eliminar sesión.
6. Validación de horas (informativa, no bloqueante).

**No avanzar a Asistencia ni otros módulos hasta que el flujo de Planificación → Agenda → Sesiones funcione al 100%.**

## Forma de trabajo (muy importante)

- Un único caso de uso a la vez: Implementar → Probar → Corregir → Continuar.
- No respuestas ni cambios enormes de una vez.
- No realizar grandes refactorizaciones sin validación previa.
- No romper funcionalidades existentes.
- Entregar archivos completos cuando se modifiquen, indicando siempre el nombre del archivo.
- Reutilizar componentes UI existentes (`x-ui.*`).
- Priorizar experiencia de usuario enfocada en coordinadores OTEC.
- Evitar sobreingeniería — es un MVP.

## Notas de mantenimiento

- Se eliminó la ruta `executions.planning.edit` (código muerto, sin controller method ni uso en ninguna vista).