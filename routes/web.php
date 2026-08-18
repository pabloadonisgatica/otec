<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DiplomaTemplateController;
use App\Http\Controllers\DiplomaController;
use App\Http\Controllers\ExecutionController;
use App\Http\Controllers\ExecutionSessionController;
use App\Http\Controllers\ClassBookController;
use App\Http\Controllers\ExecutionSurveyController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\ExecutionPlanningController;
use App\Http\Controllers\NonConformityController;
use App\Http\Controllers\CorrectiveActionController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\QualityNormController;
use App\Http\Controllers\QualityProfileController;
use App\Http\Controllers\QualityDocumentController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\ExternalDocumentController;
use App\Http\Controllers\QualityRecordController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Encuesta de satisfacción (pública, sin login — identificada por token)
|--------------------------------------------------------------------------
*/

Route::get('/encuesta/{token}', [SurveyController::class, 'show'])
    ->name('survey.show');

Route::post('/encuesta/{token}', [SurveyController::class, 'store'])
    ->name('survey.store');

Route::get('/validar/{code}', [DiplomaController::class, 'validateCode'])
    ->name('diplomas.validate');

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Perfil
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Empresas
    |--------------------------------------------------------------------------
    */

    Route::resource('companies', CompanyController::class);

    /*
    |--------------------------------------------------------------------------
    | Relatores
    |--------------------------------------------------------------------------
    */

    Route::resource('instructors', InstructorController::class);
    Route::get('/instructors/{instructor}/json', [InstructorController::class, 'show'])
        ->name('instructors.json');

    /*
    |--------------------------------------------------------------------------
    | Documentos
    |--------------------------------------------------------------------------
    */

    Route::get('/documents/{type}/{id}/{index}', [DocumentController::class, 'show'])
        ->name('documents.show');

    Route::get('/documents/{type}/{id}/{index}/download', [DocumentController::class, 'download'])
        ->name('documents.download');

    Route::delete('/documents/{type}/{id}/{index}', [DocumentController::class, 'delete'])
        ->name('documents.delete');

    /*
    |--------------------------------------------------------------------------
    | Participantes
    |--------------------------------------------------------------------------
    */

    Route::get('participants/{participant}/json', [ParticipantController::class, 'json'])
        ->name('participants.json');

    Route::get('participants/import', [ParticipantController::class, 'importForm'])
        ->name('participants.import.form');

    Route::post('participants/import', [ParticipantController::class, 'import'])
        ->name('participants.import');

    Route::resource('participants', ParticipantController::class);

    /*
    |--------------------------------------------------------------------------
    | Cursos
    |--------------------------------------------------------------------------
    */

    Route::resource('courses', CourseController::class);

    /*
    |--------------------------------------------------------------------------
    | Presupuestos
    |--------------------------------------------------------------------------
    */

    Route::resource('budgets', BudgetController::class);

    Route::post('budgets/{budget}/duplicate', [BudgetController::class, 'duplicate'])
        ->name('budgets.duplicate');

    Route::get('budgets/{budget}/pdf', [BudgetController::class, 'pdf'])
        ->name('budgets.pdf');

    /*
    |--------------------------------------------------------------------------
    | Configuración
    |--------------------------------------------------------------------------
    */

    Route::get('/configuracion', [SettingsController::class, 'index'])
        ->name('settings.index');

    Route::post('/configuracion/logo', [SettingsController::class, 'updateLogo'])
        ->name('settings.logo.update');

    Route::post('/configuracion/otec-name', [SettingsController::class, 'updateOtecName'])
        ->name('settings.otec-name.update');

    Route::prefix('configuracion')->group(function () {

        Route::get('/usuarios', [UserManagementController::class, 'index'])
            ->name('settings.users.index');

        Route::get('/usuarios/crear', [UserManagementController::class, 'create'])
            ->name('settings.users.create');

        Route::post('/usuarios', [UserManagementController::class, 'store'])
            ->name('settings.users.store');

        Route::get('/usuarios/{user}/editar', [UserManagementController::class, 'edit'])
            ->name('settings.users.edit');

        Route::put('/usuarios/{user}', [UserManagementController::class, 'update'])
            ->name('settings.users.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Diplomas
    |--------------------------------------------------------------------------
    */

    Route::post('diploma-templates/preview', [DiplomaTemplateController::class, 'preview'])
        ->name('diploma-templates.preview');

    Route::post('diploma-templates/preview-html', [DiplomaTemplateController::class, 'previewHtml'])
        ->name('diploma-templates.preview-html');

    Route::resource('diploma-templates', DiplomaTemplateController::class);

    Route::get('diplomas', [DiplomaController::class, 'index'])
        ->name('diplomas.index');

    Route::get('diplomas/emit', [DiplomaController::class, 'create'])
        ->name('diplomas.create');

    Route::post('diplomas/emit', [DiplomaController::class, 'store'])
        ->name('diplomas.store');

    Route::get('diplomas/{diploma}', [DiplomaController::class, 'show'])
        ->name('diplomas.show');

    Route::get('diplomas/{diploma}/pdf', [DiplomaController::class, 'pdf'])
        ->name('diplomas.pdf');

    Route::delete('diplomas/{diploma}', [DiplomaController::class, 'destroy'])
        ->name('diplomas.destroy');

    /*
|--------------------------------------------------------------------------
| Ejecuciones
|--------------------------------------------------------------------------
*/

    Route::resource('executions', ExecutionController::class);

    Route::put(
        '/executions/{execution}/close',
        [ExecutionController::class, 'close']
    )->name('executions.close');

    Route::put(
        '/executions/{execution}/reopen',
        [ExecutionController::class, 'reopen']
    )->name('executions.reopen');

    /*
|--------------------------------------------------------------------------
| Planificación
|--------------------------------------------------------------------------
*/

    Route::put(
        '/executions/{execution}/planning',
        [ExecutionPlanningController::class, 'update']
    )->name('executions.planning.update');

    Route::post(
        '/executions/{execution}/planning/generate',
        [ExecutionPlanningController::class, 'generate']
    )->name('executions.planning.generate');

    /*
|--------------------------------------------------------------------------
| Participantes
|--------------------------------------------------------------------------
*/

    Route::post(
        '/executions/{execution}/participants',
        [ExecutionController::class, 'addParticipant']
    )->name('executions.participants.store');

    Route::post(
        '/executions/{execution}/participants/company',
        [ExecutionController::class, 'addCompanyParticipants']
    )->name('executions.participants.company');

    Route::delete(
        '/executions/{execution}/participants/{participant}',
        [ExecutionController::class, 'removeParticipant']
    )->name('executions.participants.destroy');

    Route::delete(
        '/executions/{execution}/participants',
        [ExecutionController::class, 'removeAllParticipants']
    )->name('executions.participants.destroy-all');

    /*
|--------------------------------------------------------------------------
| Relatores
|--------------------------------------------------------------------------
*/

    Route::post(
        '/executions/{execution}/instructors',
        [ExecutionController::class, 'addInstructor']
    )->name('executions.instructors.store');

    Route::delete(
        '/executions/{execution}/instructors/{instructor}',
        [ExecutionController::class, 'removeInstructor']
    )->name('executions.instructors.destroy');

    /*
|--------------------------------------------------------------------------
| Sesiones
|--------------------------------------------------------------------------
*/

    Route::get(
        '/executions/{execution}/sessions/{session}/edit',
        [ExecutionSessionController::class, 'edit']
    )->name('executions.sessions.edit');

    Route::get(
        '/executions/{execution}/sessions/create',
        [ExecutionSessionController::class, 'create']
    )->name('executions.sessions.create');

    Route::post(
        '/executions/{execution}/sessions',
        [ExecutionSessionController::class, 'store']
    )->name('executions.sessions.store');

    Route::put(
        '/executions/{execution}/sessions/{session}',
        [ExecutionSessionController::class, 'update']
    )->name('executions.sessions.update');

    Route::delete(
        '/executions/{execution}/sessions/{session}',
        [ExecutionSessionController::class, 'destroy']
    )->name('executions.sessions.destroy');

    /*
|--------------------------------------------------------------------------
| Libro de clases
|--------------------------------------------------------------------------
*/

    Route::put(
        '/executions/{execution}/classbook/attendance',
        [ClassBookController::class, 'updateAttendance']
    )->name('executions.classbook.attendance.update');

    Route::put(
        '/executions/{execution}/classbook/grades',
        [ClassBookController::class, 'updateGrades']
    )->name('executions.classbook.grades.update');

    Route::get(
        '/executions/{execution}/classbook/pdf',
        [ClassBookController::class, 'pdf']
    )->name('executions.classbook.pdf');

    /*
|--------------------------------------------------------------------------
| Encuesta de satisfacción (admin)
|--------------------------------------------------------------------------
*/

    Route::post(
        '/executions/{execution}/survey/send',
        [ExecutionSurveyController::class, 'send']
    )->name('executions.survey.send');

    /*
|--------------------------------------------------------------------------
| Gestión de Calidad — No Conformidades y Acciones
|--------------------------------------------------------------------------
*/

    Route::prefix('calidad')->name('quality.')->group(function () {

        Route::get('/norma', [QualityNormController::class, 'index'])
            ->name('norm.index');

        Route::get('/requisitos-generales', [QualityProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::put('/requisitos-generales', [QualityProfileController::class, 'update'])
            ->name('profile.update');

        Route::post('/requisitos-generales/documentos', [QualityProfileController::class, 'uploadDocument'])
            ->name('profile.documents.upload');

        Route::get('/requisitos-generales/documentos/{index}/descargar', [QualityProfileController::class, 'downloadDocument'])
            ->name('profile.documents.download');

        Route::delete('/requisitos-generales/documentos/{index}', [QualityProfileController::class, 'deleteDocument'])
            ->name('profile.documents.delete');

        Route::post('/requisitos-generales/mapa-procesos', [QualityProfileController::class, 'uploadProcessMap'])
            ->name('profile.process-map.upload');

        Route::post('/requisitos-generales/organigrama', [QualityProfileController::class, 'uploadOrgChart'])
            ->name('profile.org-chart.upload');

        Route::get('/manual-calidad', [QualityDocumentController::class, 'manualCalidad'])
            ->name('manual-calidad');

        Route::put('/manual-calidad', [QualityDocumentController::class, 'updateManualCalidad'])
            ->name('manual-calidad.update');

        Route::post('/manual-calidad/versiones', [QualityDocumentController::class, 'uploadVersion'])
            ->name('manual-calidad.versions.upload');

        Route::get('/documentos/versiones/{version}/descargar', [QualityDocumentController::class, 'downloadVersion'])
            ->name('documents.versions.download');

        Route::resource('procedimientos', ProcedureController::class)
            ->parameters(['procedimientos' => 'procedure'])
            ->names('procedures');

        Route::post('/procedimientos/{procedure}/versiones', [ProcedureController::class, 'uploadVersion'])
            ->name('procedures.versions.upload');

        Route::resource('documentos-externos', ExternalDocumentController::class)
            ->parameters(['documentos-externos' => 'externalDocument'])
            ->names('external-documents');

        Route::post('/documentos-externos/{externalDocument}/versiones', [ExternalDocumentController::class, 'uploadVersion'])
            ->name('external-documents.versions.upload');

        Route::resource('registros', QualityRecordController::class)
            ->parameters(['registros' => 'record'])
            ->names('records');

        Route::resource('no-conformidades', NonConformityController::class)
            ->parameters(['no-conformidades' => 'non_conformity'])
            ->names('non-conformities');

        Route::post(
            '/no-conformidades/{non_conformity}/acciones',
            [CorrectiveActionController::class, 'store']
        )->name('non-conformities.actions.store');

        Route::put(
            '/no-conformidades/{non_conformity}/acciones/{action}',
            [CorrectiveActionController::class, 'update']
        )->name('non-conformities.actions.update');

        Route::delete(
            '/no-conformidades/{non_conformity}/acciones/{action}',
            [CorrectiveActionController::class, 'destroy']
        )->name('non-conformities.actions.destroy');

        Route::get('/proveedores/{provider}/informe', [ProviderController::class, 'report'])
            ->name('providers.report');

        Route::resource('proveedores', ProviderController::class)
            ->parameters(['proveedores' => 'provider'])
            ->names('providers');

    });
});

require __DIR__ . '/auth.php';
