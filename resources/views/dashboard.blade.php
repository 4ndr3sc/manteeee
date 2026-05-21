<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaintFlow | Panel Técnico</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .slider-wrapper { display: flex; transition: transform 0.5s ease-in-out; }
        .slide-item { flex: 0 0 100%; }
        /* Control dinámico de pantallas */
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        /* Light theme overrides (predominantly white with blue accents) */
        body { background: #ffffff; color: #0f172a; }
        .bg-gray-800, .bg-gray-900, .bg-gray-950, .bg-gray-850 { background: #ffffff !important; color: inherit !important; }
        .text-white { color: #0f172a !important; }
        .text-gray-400 { color: #6b7280 !important; }
        .border-gray-700, .border-gray-800, .border-gray-900 { border-color: #e5e7eb !important; }
        /* Give any element that originally used gray backgrounds a subtle blue tint to separate from pure white */
        [class*="bg-gray-"] {
            background: #f0f9ff !important;
            border-color: #dbeafe !important;
            color: #0f172a !important;
        }
        /* Force inner text/icons inside gray backgrounds to be dark for readability (override light-blue text classes) */
        [class*="bg-gray-"] .text-blue-400,
        [class*="bg-gray-"] .text-blue-500,
        [class*="bg-gray-"] .text-blue-600,
        [class*="bg-gray-"] .text-white,
        [class*="bg-gray-"] i,
        [class*="bg-gray-"] span,
        [class*="bg-gray-"] p,
        [class*="bg-gray-"] h2,
        [class*="bg-gray-"] h3 {
            color: #0f172a !important;
            opacity: 1 !important;
        }
        /* Ensure rounded chips on gray backgrounds use dark text */
        [class*="bg-gray-"] .rounded-full, [class*="bg-gray-"] .px-2\.5.py-0\.5 {
            color: #0f172a !important;
            background: #e6f0ff !important;
            border-color: #cfe6ff !important;
            font-weight: 700;
        }
        /* Make semi-transparent gray backgrounds a pale blue tint for legibility */
        .bg-gray-950\/50, .bg-gray-900\/50, .bg-gray-900\/40 { background: rgba(59,130,246,0.06) !important; }
        /* If an element used a small chip/badge, keep it readable with darker blue text by default */
        .px-2\.5.py-0\.5 { color: #0b3b71 !important; font-weight: 600; }
        /* Specific badge color adjustments to avoid low-contrast combos */
        .px-2\.5.py-0\.5[class*="bg-red-"] {
            background: #fee2e2 !important; /* pale red */
            border-color: #fecaca !important;
            color: #0f172a !important; /* dark text for readability */
            font-weight: 700;
        }
        .px-2\.5.py-0\.5[class*="bg-green-"] {
            background: #ecfdf5 !important; /* pale green */
            border-color: #bbf7d0 !important;
            color: #064e3b !important; /* dark green text */
            font-weight: 700;
        }
        .px-2\.5.py-0\.5[class*="bg-yellow-"] {
            background: #fffbeb !important; /* pale yellow */
            border-color: #fef3c7 !important;
            color: #78350f !important; /* dark amber text */
            font-weight: 700;
        }
        .px-2\.5.py-0\.5[class*="bg-blue-"] {
            background: #eff6ff !important; /* pale blue */
            border-color: #dbeafe !important;
            color: #0b3b71 !important; /* deep blue text */
            font-weight: 700;
        }
        /* Ensure card selects have a minimum width for layout */
        select.cardEstadoSelect { min-width: 140px; }
        aside .tab-btn { color: #0f172a; }
        /* Ensure interactive controls (buttons) remain blue and visible */
        .bg-blue-600, .bg-blue-700, .bg-blue-500 { color: #ffffff !important; }
        button, .rounded-xl, .rounded-2xl { box-shadow: 0 1px 2px rgba(15,23,42,0.04); }
        /* Keep explicit red/green badges visible (don't override) */
    </style>
</head>
<body class="bg-white text-gray-900 min-h-screen font-sans antialiased flex">

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between min-h-screen sticky top-0 z-40">
        <div>
            <div class="p-6 border-b border-gray-800 flex items-center gap-3">
                <i class="fas fa-screwdriver-wrench text-blue-500 text-2xl"></i>
                <span class="text-lg font-bold tracking-wider text-white">Maint<span class="text-blue-500">Flow</span></span>
            </div>

            <!-- Modal para dejar comentario en OT -->
            <div id="modalComentario" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden">
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="w-full max-w-lg bg-gray-800 border border-gray-700 rounded-xl shadow-2xl overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700 bg-gray-850">
                            <h3 class="text-white font-bold">Dejar Comentario</h3>
                            <button onclick="closeComentarioModal()" class="text-gray-400 hover:text-white"><i class="fas fa-xmark"></i></button>
                        </div>
                        <div class="p-4">
                            <div class="mb-3 text-sm text-gray-300">Equipo: <span id="comentEquipoNombre" class="font-bold text-white">---</span></div>
                            <textarea id="comentarioTexto" rows="5" class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-sm text-white" placeholder="Escribe tu comentario aquí..."></textarea>
                            <div class="mt-3 flex items-center justify-end gap-2">
                                <button onclick="closeComentarioModal()" class="px-4 py-2 bg-gray-700 text-gray-200 rounded-xl">Cancelar</button>
                                <button onclick="submitComentario()" class="px-4 py-2 bg-indigo-600 text-white rounded-xl">Enviar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->check() && auth()->user()->hasRole('client'))
            <nav class="p-4 space-y-1">
                <button onclick="switchTab('cliente', this)" class="tab-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl bg-blue-600 text-white transition">
                    <i class="fas fa-user-tag w-5 text-center"></i> Panel Cliente
                </button>
                <button onclick="switchTab('perfil', this)" class="tab-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-gray-400 hover:bg-gray-900 hover:text-white transition">
                    <i class="fas fa-user-gear w-5 text-center"></i> Mi Perfil
                </button>
            </nav>
            @else
            <nav class="p-4 space-y-1">
                <button onclick="switchTab('inicio', this)" class="tab-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl bg-blue-600 text-white transition">
                    <i class="fas fa-chart-pie w-5 text-center"></i> Inicio / Central
                </button>
                <button onclick="switchTab('equipos', this)" class="tab-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-gray-400 hover:bg-gray-900 hover:text-white transition">
                    <i class="fas fa-industry w-5 text-center"></i> Equipos en Taller
                </button>
                @if(!(auth()->check() && auth()->user()->isAdmin()))
                <button onclick="switchTab('ordenes', this)" class="tab-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-gray-400 hover:bg-gray-900 hover:text-white transition">
                    <i class="fas fa-list-check w-5 text-center"></i> Mis Órdenes (OT)
                </button>
                @endif
                <button onclick="switchTab('historial', this)" class="tab-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-gray-400 hover:bg-gray-900 hover:text-white transition">
                    <i class="fas fa-clock-rotate-left w-5 text-center"></i> Historial / Trazabilidad
                </button>
                <button onclick="switchTab('perfil', this)" class="tab-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-gray-400 hover:bg-gray-900 hover:text-white transition">
                    <i class="fas fa-user-gear w-5 text-center"></i> Mi Perfil Técnico
                </button>
                @if(auth()->user()->isAdmin())
                <button onclick="switchTab('tecnicos', this)" class="tab-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-gray-400 hover:bg-gray-900 hover:text-white transition">
                    <i class="fas fa-users w-5 text-center"></i> Técnicos
                </button>
                <button onclick="switchTab('arreglados', this)" class="tab-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-gray-400 hover:bg-gray-900 hover:text-white transition">
                    <i class="fas fa-check-to-slot w-5 text-center"></i> Arreglados
                </button>
                @endif
            </nav>
            @endif
        </div>

        <div class="p-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-3 px-4 py-3 text-sm font-medium rounded-xl bg-blue-700 hover:bg-blue-800 text-white transition shadow-md">
                    <i class="fas fa-right-from-bracket w-5 text-center"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-8 lg:p-12 max-w-7xl mx-auto w-full overflow-x-hidden">
        
        <div id="inicio" class="tab-content active space-y-8">
            <header class="flex justify-between items-center border-b border-gray-800 pb-5">
                <div>
                    <span class="text-blue-500 text-xs font-bold tracking-widest uppercase">Estación de Trabajo</span>
                    <h1 class="text-3xl font-extrabold text-white">Panel de Control de Mantenimiento</h1>
                </div>
                <span class="bg-gray-800 px-4 py-2 rounded-lg border border-gray-700 text-xs font-mono text-green-400 flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Cuadrilla Activa
                </span>
            </header>

            @if(auth()->user()->isAdmin() && !empty($staleEquipos) && count($staleEquipos) > 0)
            <div class="mt-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800">
                <div class="flex justify-between items-center">
                    <div><strong>{{ count($staleEquipos) }}</strong> equipos sin reclamar por más de {{ $staleDays }} días.</div>
                    <div>
                        <button onclick="openEquiposFromAlert()" class="px-3 py-1 bg-red-600 text-white rounded">Ver equipos</button>
                    </div>
                </div>
                <div class="mt-2 text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($staleEquipos->take(5) as $se)
                            <li>{{ $se->nombre }} — creado {{ $se->created_at->diffForHumans() }}</li>
                        @endforeach
                    </ul>
                    @if(count($staleEquipos) > 5)
                        <div class="mt-2 text-xs text-gray-600">... y {{ count($staleEquipos) - 5 }} más</div>
                    @endif
                </div>
            </div>
            @endif

            <div class="relative bg-gray-800 rounded-2xl border border-gray-700 shadow-2xl overflow-hidden">
                <div class="overflow-hidden">
                    <div id="sliderWrapper" class="slider-wrapper">
                        <div class="slide-item p-8 sm:p-12">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                                <div>
                                    <span class="bg-blue-900/50 text-blue-400 text-xs px-3 py-1 rounded-full font-semibold border border-blue-800">Eficiencia Mecánica</span>
                                    <h2 class="text-3xl font-bold text-white mt-3 mb-4">Tiempos de Respuesta Taller</h2>
                                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Indicadores globales de reparación. El tiempo medio de diagnóstico ha disminuido gracias al reporte digital de fallas en sitio.</p>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-gray-950 p-4 rounded-xl border border-gray-700"><div class="text-2xl font-bold text-blue-500">94.2%</div><div class="text-xs text-gray-500 mt-1">Éxito en Reparación</div></div>
                                        <div class="bg-gray-950 p-4 rounded-xl border border-gray-700"><div class="text-2xl font-bold text-green-500">45 Min</div><div class="text-xs text-gray-500 mt-1">Diagnóstico Promedio</div></div>
                                    </div>
                                </div>
                                <div class="bg-gray-950 p-6 rounded-xl border border-gray-700 flex flex-col justify-between h-48">
                                    <div class="flex justify-between items-end h-32 gap-2 border-b border-gray-800 pb-2">
                                        <div class="bg-blue-600 w-full rounded-t" style="height: 40%"></div>
                                        <div class="bg-blue-600 w-full rounded-t" style="height: 75%"></div>
                                        <div class="bg-emerald-500 w-full rounded-t" style="height: 90%"></div>
                                        <div class="bg-blue-600 w-full rounded-t" style="height: 65%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="equipos" class="tab-content space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-800 pb-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-white">Inventario de Maquinaria en Taller</h1>
                    <p class="text-gray-400 text-sm mt-1">Dispositivos bajo supervisión técnica asignados a reparación o calibración.</p>
                </div>
                <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition shadow-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i> Registrar Reporte de Máquina
                </button>
            </div>

            <div id="listaEquipos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($equipos as $equipo)
                 <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 shadow-md flex flex-col justify-between"
                     data-id="eq-{{ $equipo->id }}" data-tipo="{{ $equipo->tipo }}" data-nombre="{{ $equipo->nombre }}"
                     data-marca="{{ $equipo->marca }}" data-serie="{{ $equipo->serie }}" data-estado="{{ $equipo->estado }}"
                     data-falla="{{ $equipo->falla }}" data-responsable="{{ $equipo->user->name ?? $equipo->responsable ?? 'N/A' }}" data-user-id="{{ $equipo->user->id ?? '' }}" data-cliente="{{ optional($equipo->cliente)->name ?? '' }}">
                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <span class="px-2.5 py-0.5 text-xs font-semibold {{ $equipo->tipo === 'Correctivo' ? 'bg-red-950 text-red-400 border-red-900' : 'bg-green-950 text-green-400 border-green-900' }} border rounded-full">{{ $equipo->tipo }}</span>
                            <span class="text-xs font-mono text-gray-500">OT: #{{ str_pad($equipo->id, 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-white">{{ $equipo->nombre }}</h3>
                        <div class="mt-2 flex items-center gap-3">
                            <div class="text-xs px-2.5 py-0.5 rounded-full bg-gray-900 text-gray-300 border border-gray-700">Estado: <strong id="status-{{ $equipo->id }}">{{ $equipo->estado }}</strong></div>
                            <div>
                                <select onchange="cambiarEstadoCard({{ $equipo->id }}, this.value, this)" class="cardEstadoSelect text-xs bg-gray-900 text-white border border-gray-700 rounded px-2 py-1">
                                    <option value="En espera" {{ $equipo->estado === 'En espera' ? 'selected' : '' }}>En espera</option>
                                    <option value="En proceso" {{ $equipo->estado === 'En proceso' ? 'selected' : '' }}>En proceso</option>
                                    <option value="Arreglado" {{ $equipo->estado === 'Arreglado' ? 'selected' : '' }}>Arreglado</option>
                                    <option value="Terminado" {{ $equipo->estado === 'Terminado' ? 'selected' : '' }}>Terminado</option>
                                    <option value="En espera de repuestos" {{ $equipo->estado === 'En espera de repuestos' ? 'selected' : '' }}>En espera de repuestos</option>
                                </select>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">S/N: {{ $equipo->serie ?? 'N/A' }} | Marca: {{ $equipo->marca ?? 'N/A' }}</p>
                        <div class="mt-4 bg-gray-900/50 p-3 rounded-lg border border-gray-700/50 flex items-center gap-2 text-xs text-gray-400">
                            <i class="fas fa-user text-blue-500"></i>
                            <span>Responsable: <strong>{{ $equipo->user->name ?? $equipo->responsable ?? 'Ing. Carlos Mendoza' }}</strong></span>
                        </div>
                        <div class="mt-2 bg-gray-900/40 p-2 rounded-lg border border-gray-700/50 text-xs text-gray-400 flex items-center gap-2">
                            <i class="fas fa-user-circle text-indigo-400"></i>
                            <span>Cliente: <strong>{{ optional($equipo->cliente)->name ?? 'N/A' }}</strong></span>
                        </div>
                    </div>
                    <div class="mt-5 pt-3 border-t border-gray-700 text-xs text-gray-500 flex justify-between items-center">
                        <span><i class="far fa-calendar mr-1"></i> Asignado: {{ $equipo->created_at->diffForHumans() }}</span>
                        <button onclick="verDetalles(this.closest('[data-id]'))" class="text-blue-400 hover:text-blue-300 font-medium flex items-center gap-1.5 transition">
                            Ver detalles <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            @if(auth()->user()->isAdmin())
            <div class="pt-4">
                <h3 class="text-sm text-gray-600 mb-2">Reasignar equipo (selecciona un equipo y usa Ver detalles)</h3>
            </div>
            @endif

            <div id="modalEquipo" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                <div class="bg-gray-800 border border-gray-700 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-700 bg-gray-850 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2"><i class="fas fa-file-waveform text-blue-500"></i> Reporte Técnico de Entrada</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-white text-lg"><i class="fas fa-xmark"></i></button>
                    </div>
                    <form id="formNuevoEquipo" onsubmit="agregarEquipo(event)" class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Nombre del Equipo *</label>
                                <input type="text" id="inputNombre" required placeholder="Ej. Compresor, Torno..." class="w-full bg-gray-950 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:border-blue-500 transition">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Tipo Mantenimiento *</label>
                                <select id="inputTipo" class="w-full bg-gray-950 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:border-blue-500 transition">
                                    <option value="Correctivo">Correctivo</option>
                                    <option value="Preventivo">Preventivo</option>
                                    <option value="Calibración">Calibración</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Marca / Modelo *</label>
                                <input type="text" id="inputMarca" required placeholder="Ej. Caterpillar V2" class="w-full bg-gray-950 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:border-blue-500 transition">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Número de Serie (S/N)</label>
                                <input type="text" id="inputSerie" placeholder="Ej. SN-4921" class="w-full bg-gray-950 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:border-blue-500 transition">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Teléfono del dueño / contacto</label>
                                <input type="text" id="inputTelefono" placeholder="Ej. +52 55 1234 5678" class="w-full bg-gray-950 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:border-blue-500 transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Diagnóstico de Falla / Síntomas *</label>
                            <textarea id="inputFalla" required rows="3" placeholder="Describe detalladamente los hallazgos iniciales o las anomalías mecánicas..." class="w-full bg-gray-950 border border-gray-700 rounded-xl px-4 py-2.5 text-sm text-white focus:border-blue-500 transition resize-none"></textarea>
                        </div>
                        <div class="pt-4 border-t border-gray-700 flex justify-end gap-3">
                            <button type="button" onclick="closeModal()" class="px-4 py-2.5 text-sm font-semibold text-gray-400 bg-gray-700/50 rounded-xl">Cancelar</button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl shadow-lg">Abrir Orden</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="modalDetail" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden overflow-hidden">
                <!-- Botón de cierre flotante siempre accesible -->
                <button id="modalCloseFloating" onclick="closeDetailModal()" class="flex items-center justify-center w-10 h-10 rounded-full bg-red-600 text-white fixed top-5 right-5 z-60 shadow-lg">
                    <i class="fas fa-xmark"></i>
                </button>
                <div id="modalPanel" class="absolute right-0 top-0 bottom-0 bg-gray-800 border-l border-gray-700 w-full max-w-md transform translate-x-full transition-transform duration-300 shadow-2xl overflow-y-auto">
                    <div class="px-5 py-4 border-b border-gray-700 bg-gray-850 flex justify-between items-center sticky top-0">
                        <div class="flex items-center gap-2"><i class="fas fa-microchip text-blue-500 text-lg"></i><h3 class="text-base font-bold text-white">Ficha Técnica</h3></div>
                        <div class="flex items-center gap-2">
                            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-white text-lg px-2 py-1"><i class="fas fa-xmark"></i></button>
                        </div>
                    </div>
                    <div class="p-5 space-y-5">
                        <div class="flex justify-between items-center bg-gray-950 p-4 rounded-xl border border-gray-700">
                            <div><span class="text-xs text-gray-500 font-mono block">ACTIVO</span><h2 id="detNombre" class="text-xl font-bold text-white">---</h2></div>
                            <span id="detTipo" class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-950 text-blue-400 border border-blue-800">---</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="bg-gray-900/40 p-3 rounded-lg border border-gray-800"><span class="text-xs text-gray-500 block mb-0.5">Asignado A:</span><span id="detResponsable" class="font-semibold text-gray-200">---</span></div>
                            <div class="bg-gray-900/40 p-3 rounded-lg border border-gray-800"><span class="text-xs text-gray-500 block mb-0.5">Código OT</span><span id="detOT" class="font-mono font-semibold text-blue-400">---</span></div>
                            <div class="bg-gray-900/40 p-3 rounded-lg border border-gray-800"><span class="text-xs text-gray-500 block mb-0.5">Cliente / Dueño</span><span id="detCliente" class="font-semibold text-gray-200">---</span></div>
                            <div class="bg-gray-900/40 p-3 rounded-lg border border-gray-800"><span class="text-xs text-gray-500 block mb-0.5">Teléfono Contacto</span><span id="detTelefono" class="font-semibold text-gray-200">---</span></div>
                            <div class="bg-gray-900/40 p-3 rounded-lg border border-gray-800"><span class="text-xs text-gray-500 block mb-0.5">Marca / Modelo</span><span id="detMarca" class="text-gray-200">---</span></div>
                            <div class="bg-gray-900/40 p-3 rounded-lg border border-gray-800"><span class="text-xs text-gray-500 block mb-0.5">Número de Serie</span><span id="detSerie" class="font-mono text-gray-200">---</span></div>
                        </div>
                        <div class="bg-gray-950 p-4 rounded-xl border border-gray-700">
                            <span class="text-xs text-blue-400 font-bold uppercase tracking-wider block mb-2"><i class="fas fa-notes-medical mr-1"></i> Estado / Hallazgos Mecánicos</span>
                            <div class="flex gap-2 items-start text-sm text-gray-300 leading-relaxed italic">
                                <i class="fas fa-quote-left text-gray-600 text-xs mt-1"></i>
                                <p id="detFalla">---</p>
                            </div>
                        </div>
                        <!-- Estado se actualiza desde la tarjeta principal; la ficha es de solo lectura para estado -->
                        <div class="mt-4 bg-gray-900 p-4 rounded-xl border border-gray-700">
                            <p class="text-sm text-gray-400">El estado se actualiza desde la tarjeta en la lista. Usa "Ver detalles" para ver información completa.</p>
                        </div>
                        @if(auth()->user()->isAdmin())
                        <div class="bg-gray-900 p-4 rounded-xl border border-gray-700">
                            <label class="block text-xs text-gray-400 mb-2">Reasignar Técnico</label>
                            <div class="flex gap-2">
                                <select id="selectReasignar" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2 text-sm text-white"></select>
                                <button id="btnReasignar" onclick="reasignarEquipoDesdeModal()" class="px-4 py-2 bg-blue-600 text-white rounded-xl">Reasignar</button>
                            </div>
                        </div>
                        @endif
                    </div>
                    </div>
                    <div class="px-5 py-4 bg-gray-850 border-t border-gray-700 flex justify-end sticky bottom-0">
                        <button onclick="closeDetailModal()" class="px-4 py-2 text-sm font-semibold text-gray-400 bg-gray-700/50 rounded-xl">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        @if(!(auth()->check() && auth()->user()->isAdmin()))
        <div id="ordenes" class="tab-content space-y-6">
            <div>
                <h1 class="text-3xl font-extrabold text-white">Órdenes de Trabajo (OT) Asignadas</h1>
                <p class="text-gray-400 text-sm mt-1">Hoja de ruta diaria y tareas críticas asignadas a tu banco de trabajo mecánico.</p>
            </div>
            <div id="misOrdenesList" class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                @foreach($misEquipos ?? collect([]) as $me)
                <div id="mis-eq-{{ $me->id }}" data-id="eq-{{ $me->id }}" data-nombre="{{ $me->nombre }}" data-tipo="{{ $me->tipo }}" data-marca="{{ $me->marca }}" data-serie="{{ $me->serie }}" data-estado="{{ $me->estado }}" data-falla="{{ $me->falla }}" data-telefono="{{ $me->telefono }}" data-responsable="{{ $me->user->name ?? $me->responsable ?? 'N/A' }}" data-user-id="{{ $me->user->id ?? '' }}" class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-xl flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex justify-between items-center border-b border-gray-700 pb-3">
                            <div class="flex flex-col">
                                <span class="text-xs font-mono font-bold text-blue-400 bg-gray-950 px-3 py-1 rounded-lg border border-gray-800">OT-{{ str_pad($me->id,3,'0',STR_PAD_LEFT) }}</span>
                                <span class="text-xs text-gray-400 mt-1">Cliente: <strong class="text-white">{{ optional($me->cliente)->name ?? 'N/A' }}</strong></span>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider bg-yellow-950/80 text-yellow-400 border border-yellow-900 rounded-full flex items-center gap-1.5">Estado: <strong id="status-{{ $me->id }}">{{ $me->estado }}</strong></span>
                                <span class="text-xs text-gray-400 mt-1">Responsable: <strong class="text-white">{{ $me->user->name ?? $me->responsable ?? 'N/A' }}</strong></span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-xl font-bold text-white">{{ $me->nombre }}</h3>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">S/N: {{ $me->serie ?? 'N/A' }} | Marca: {{ $me->marca ?? 'N/A' }}</p>
                        </div>
                    </div>

                        <div class="pt-4 border-t border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs font-mono text-gray-400 flex items-center gap-1.5 bg-gray-900/60 px-3 py-1.5 rounded-lg border border-gray-700/50">
                            <i class="far fa-clock text-blue-500"></i> Asignado: <strong class="text-blue-400 font-bold">{{ $me->created_at->diffForHumans() }}</strong>
                        </div>
                        <button onclick="openComentario(this.closest('[data-id]'))" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition shadow-lg flex items-center justify-center gap-2">Dejar comentario</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div id="historial" class="tab-content space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-white">Historial General de Reparaciones</h1>
                    <p class="text-gray-400 text-sm mt-1">Bitácora completa de equipos que han pasado por mantenimiento técnico.</p>
                </div>
                
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-500">
                        <i class="fas fa-magnifying-glass text-sm"></i>
                    </span>
                    <input type="text" id="inputBuscarHistorial" onkeyup="filtrarHistorial()" placeholder="Buscar por equipo o número de serie..." 
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-gray-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                </div>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-700 bg-gray-850 text-gray-400 text-xs font-semibold uppercase tracking-wider">
                            <th class="p-4">Equipo / Componente</th>
                            <th class="p-4">N° de Serie</th>
                            <th class="p-4 text-center">Intervenciones</th>
                            <th class="p-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaHistorialBody" class="divide-y divide-gray-700 text-sm text-gray-300">
                        @foreach($equipos as $equipo)
                        <tr id="hist-eq-{{ $equipo->id }}" data-hist-nombre="{{ $equipo->nombre }}" data-hist-serie="{{ $equipo->serie }}">
                            <td class="p-4 font-bold text-white">{{ $equipo->nombre }}</td>
                            <td class="p-4 font-mono text-gray-400">{{ $equipo->serie ?? 'N/A' }}</td>
                            <td class="p-4 text-center"><span class="bg-blue-950 text-blue-400 border border-blue-800 text-xs px-2.5 py-0.5 rounded-full font-bold">{{ $equipo->bitacoras->count() }} Veces</span></td>
                            <td class="p-4 text-right"><button onclick="verBitacora('eq-{{ $equipo->id }}')" class="bg-gray-700 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg transition font-medium">Ver Bitácora</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="sinResultados" class="hidden p-8 text-center text-gray-500 border-t border-gray-700 text-sm">
                    <i class="fas fa-folder-open text-2xl mb-2 block text-gray-600"></i> No se encontraron equipos que coincidan con la búsqueda.
                </div>
            </div>

            <div id="modalBitacora" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                <div class="bg-gray-800 border border-gray-700 rounded-2xl w-full max-w-xl shadow-2xl max-h-[80vh] overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-700 bg-gray-850 flex justify-between items-center sticky top-0 z-20">
                        <h3 id="bitacoraEquipoNombre" class="text-base font-bold text-white">---</h3>
                        <button onclick="closeBitacoraModal()" class="text-gray-400 hover:text-white z-30"><i class="fas fa-xmark"></i></button>
                    </div>
                    <div class="p-6 overflow-y-auto max-h-[65vh]">
                        <div id="timelineContenedor" class="relative border-l border-gray-700 ml-3 space-y-4"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="arreglados" class="tab-content space-y-6">
            <div>
                <h1 class="text-3xl font-extrabold text-white">Equipos Arreglados</h1>
                <p class="text-gray-400 text-sm mt-1">Listado de equipos marcados como arreglados/terminados. Visible solo para administradores.</p>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-700 bg-gray-850 text-gray-400 text-xs font-semibold uppercase tracking-wider">
                            <th class="p-4">Equipo</th>
                            <th class="p-4">S/N</th>
                            <th class="p-4">Marca</th>
                            <th class="p-4">Responsable Técnico</th>
                            <th class="p-4">Teléfono Contacto</th>
                            <th class="p-4 text-right">Última actualización</th>
                        </tr>
                    </thead>
                    <tbody id="arregladosBody" class="divide-y divide-gray-700 text-sm text-gray-300">
                        @foreach($arreglados ?? collect([]) as $a)
                        <tr>
                            <td class="p-4 font-bold text-white">{{ $a->nombre }}</td>
                            <td class="p-4 font-mono text-gray-400">{{ $a->serie ?? 'N/A' }}</td>
                            <td class="p-4">{{ $a->marca ?? 'N/A' }}</td>
                            <td class="p-4">{{ $a->user->name ?? $a->responsable ?? 'N/A' }}</td>
                            <td class="p-4">{{ $a->telefono ?? 'N/A' }}</td>
                            <td class="p-4 text-right">{{ $a->updated_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="perfil" class="tab-content space-y-6">
            <div>
                <h1 class="text-3xl font-extrabold text-white">Perfil Profesional del Trabajador</h1>
                <p class="text-gray-400 text-sm mt-1">Información corporativa, especialidades mecánicas y métricas del técnico activo.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 flex flex-col items-center text-center shadow-md text-gray-900">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center text-3xl font-bold text-gray-900 shadow-sm mb-4 border-2 border-gray-200">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U',0,2)) }}
                    </div>
                    <h2 class="text-xl font-extrabold text-gray-900">{{ auth()->user()->name ?? 'Usuario' }}</h2>
                    <span class="text-xs text-gray-700 font-mono font-semibold uppercase bg-gray-100 px-3 py-1 rounded-full border border-gray-200 mt-1">{{ auth()->user()->role ?? 'Técnico' }}</span>

                    <div class="w-full border-t border-gray-200 my-4 pt-4 text-left space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-600">Correo:</span><span class="font-mono text-gray-800">{{ auth()->user()->email }}</span></div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-6 lg:col-span-2 space-y-6 shadow-md text-gray-900">
                    <h3 class="text-lg font-bold text-gray-900 border-b border-gray-200 pb-2"><i class="fas fa-id-card text-blue-600 mr-2"></i> Datos del Puesto e Información Personal</h3>
                    
                    <div class="grid grid-cols-1 gap-4 text-sm">
                        <div><label class="block text-gray-700 text-xs uppercase font-bold mb-1">Correo Corporativo</label><input type="text" disabled value="{{ auth()->user()->email }}" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2 text-gray-800"></div>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->check() && auth()->user()->hasRole('client'))
        <div id="cliente" class="tab-content space-y-6">
            <div>
                <h1 class="text-3xl font-extrabold text-white">Panel Cliente</h1>
                <p class="text-gray-400 text-sm mt-1">Accede a tus solicitudes, seguimiento en tiempo real, activos y métricas.</p>
            </div>

            <div class="flex gap-2">
                <button onclick="switchClienteTab('solicitudes', this)" class="cliente-tab-btn px-3 py-2 rounded-lg bg-blue-600 text-white">Solicitudes / PQR</button>
                <button onclick="switchClienteTab('monitoreo', this)" class="cliente-tab-btn px-3 py-2 rounded-lg text-gray-400">Monitoreo</button>
                <button onclick="switchClienteTab('activos', this)" class="cliente-tab-btn px-3 py-2 rounded-lg text-gray-400">Activos / Preventivo</button>
                <button onclick="switchClienteTab('historialCliente', this)" class="cliente-tab-btn px-3 py-2 rounded-lg text-gray-400">Historial / Métricas</button>
            </div>

            <div id="cliente-solicitudes" class="mt-4">
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-3">Crear nueva solicitud</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input id="clientNombre" placeholder="Nombre del equipo" class="bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-sm text-white" />
                        <input id="clientTipo" placeholder="Tipo (Preventivo/Correctivo)" class="bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-sm text-white" />
                        <input id="clientMarca" placeholder="Marca" class="bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-sm text-white" />
                        <input id="clientSerie" placeholder="Número de serie" class="bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-sm text-white" />
                        <textarea id="clientFalla" placeholder="Descripción de la falla" class="md:col-span-2 bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-sm text-white"></textarea>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button onclick="crearSolicitudCliente()" class="px-4 py-2 bg-blue-600 text-white rounded-xl">Enviar Solicitud</button>
                    </div>
                </div>

                <div class="mt-4">
                    <h3 class="text-lg font-bold text-white mb-3">Mis solicitudes</h3>
                    <div id="clienteMisOrdenes" class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                        @foreach($misEquipos ?? collect([]) as $me)
                        <div id="mis-eq-{{ $me->id }}" data-id="eq-{{ $me->id }}" data-nombre="{{ $me->nombre }}" data-tipo="{{ $me->tipo }}" data-marca="{{ $me->marca }}" data-serie="{{ $me->serie }}" data-estado="{{ $me->estado }}" data-falla="{{ $me->falla }}" data-telefono="{{ $me->telefono }}" data-responsable="{{ $me->user->name ?? $me->responsable ?? 'N/A' }}" data-user-id="{{ $me->user->id ?? '' }}" class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-xl flex flex-col justify-between space-y-4">
                            <div>
                                <div class="flex justify-between items-center border-b border-gray-700 pb-3">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-mono font-bold text-blue-400 bg-gray-950 px-3 py-1 rounded-lg border border-gray-800">OT-{{ str_pad($me->id,3,'0',STR_PAD_LEFT) }}</span>
                                        <span class="text-xs text-gray-400 mt-1">Cliente: <strong class="text-white">{{ optional($me->cliente)->name ?? 'N/A' }}</strong></span>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider bg-yellow-950/80 text-yellow-400 border border-yellow-900 rounded-full flex items-center gap-1.5">Estado: <strong id="status-{{ $me->id }}">{{ $me->estado }}</strong></span>
                                        <span class="text-xs text-gray-400 mt-1">Responsable: <strong class="text-white">{{ $me->user->name ?? $me->responsable ?? 'N/A' }}</strong></span>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h3 class="text-xl font-bold text-white">{{ $me->nombre }}</h3>
                                    <p class="text-xs text-gray-400 font-mono mt-0.5">S/N: {{ $me->serie ?? 'N/A' }} | Marca: {{ $me->marca ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-xs font-mono text-gray-400 flex items-center gap-1.5 bg-gray-900/60 px-3 py-1.5 rounded-lg border border-gray-700/50">
                                    <i class="far fa-clock text-blue-500"></i> Asignado: <strong class="text-blue-400 font-bold">{{ $me->created_at->diffForHumans() }}</strong>
                                </div>
                                <button onclick="verDetalles(this.closest('[data-id]'))" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition shadow-lg flex items-center justify-center gap-2">Ver Ficha</button>
                            </div>
                            @if($me->comentarios && $me->comentarios->count())
                            <div class="mt-3 card-comments space-y-2 text-sm text-gray-300">
                                @foreach($me->comentarios as $c)
                                <div class="bg-gray-900 p-2 rounded-lg border border-gray-800 text-xs">
                                    <div class="font-semibold text-white">{{ $c->user->name ?? 'Anónimo' }}</div>
                                    <div class="text-gray-400">{{ $c->comentario }}</div>
                                    <div class="text-gray-500 text-[10px] mt-1">{{ $c->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="cliente-monitoreo" class="mt-4 hidden">
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-3">Monitoreo y Trazabilidad</h3>
                    <p class="text-gray-400">Visualiza el flujo de trabajo en tiempo real. (Vista preliminar)</p>
                    <!-- Aquí se puede integrar websockets / polling para estado en tiempo real -->
                </div>
            </div>

            <div id="cliente-activos" class="mt-4 hidden">
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-3">Control de Activos y Preventivo</h3>
                    <p class="text-gray-400">Lista de activos, calendarios de mantenimiento preventivo y alertas programadas.</p>
                    <!-- placeholder: tabla de activos -->
                </div>
            </div>

            <div id="cliente-historial" class="mt-4 hidden">
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-3">Historial, Métricas y Cierre de Servicio</h3>
                    <p class="text-gray-400">Historial de intervenciones, métricas por OT y opción para cerrar o valorar el servicio.</p>
                    <!-- placeholder: métricas y opciones de cierre -->
                </div>
            </div>

        </div>
        @endif

        <div id="tecnicos" class="tab-content space-y-6">
            <div>
                <h1 class="text-3xl font-extrabold text-white">Técnicos / Cuentas</h1>
                <p class="text-gray-400 text-sm mt-1">Listado de todas las cuentas registradas y su rol actual. Sólo accesible por administradores.</p>
            </div>

            @php $users = \App\Models\User::orderBy('name')->get(); @endphp

            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-700 bg-gray-850 text-gray-400 text-xs font-semibold uppercase tracking-wider">
                            <th class="p-4">Nombre</th>
                            <th class="p-4">Correo</th>
                            <th class="p-4">Rol</th>
                            <th class="p-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 text-sm text-gray-300">
                        @foreach($users as $u)
                        <tr id="user-{{ $u->id }}">
                            <td class="p-4 font-bold text-white">{{ $u->name }}</td>
                            <td class="p-4 font-mono text-gray-400">{{ $u->email }}</td>
                            <td class="p-4" data-role>{{ $u->role ?? 'user' }}</td>
                            <td class="p-4 text-right">
                                @if(auth()->user()->id !== $u->id)
                                    <div class="flex items-center justify-end gap-2">
                                        <select id="role-select-dashboard-{{ $u->id }}" class="text-xs rounded px-2 py-1 bg-gray-900 border border-gray-700 text-white">
                                            <option value="user" {{ ($u->role ?? 'user') === 'user' ? 'selected' : '' }}>user</option>
                                            <option value="admin" {{ ($u->role ?? 'user') === 'admin' ? 'selected' : '' }}>admin</option>
                                            <option value="client" {{ ($u->role ?? 'user') === 'client' ? 'selected' : '' }}>client</option>
                                        </select>
                                        <button onclick="setRoleDashboard({{ $u->id }})" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg transition font-medium">Actualizar</button>
                                    </div>
                                @else
                                    <span class="text-gray-500 text-xs italic">(Cuenta propia)</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script>
        const baseBitacoras = {!! $bitacorasJson ?? '{}' !!};
        const baseComentarios = {!! $comentariosJson ?? '{}' !!};
        const usersList = {!! $usersJson ?? 'null' !!};
        const currentUserId = {{ auth()->check() ? auth()->id() : 'null' }};

        function switchTab(tabId, buttonElement) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            document.querySelectorAll('.tab-btn').forEach(btn => btn.className = "tab-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-gray-400 hover:bg-gray-900 hover:text-white transition");
            buttonElement.className = "tab-btn w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl bg-blue-600 text-white transition";
            // si abrimos el panel cliente, activar su pestaña por defecto
            if (tabId === 'cliente') {
                const firstClienteBtn = document.querySelector('.cliente-tab-btn');
                if (firstClienteBtn) switchClienteTab('solicitudes', firstClienteBtn);
            }
        }

        // Navegación interna del Panel Cliente
        function switchClienteTab(tabId, btn) {
            // ocultar todas las secciones cliente
            ['cliente-solicitudes','cliente-monitoreo','cliente-activos','cliente-historial'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            });
            // mostrar la solicitada
            const target = document.getElementById('cliente-' + (tabId === 'historialCliente' ? 'historial' : tabId));
            if (target) target.classList.remove('hidden');

            // ajustar estilos de botones
            document.querySelectorAll('.cliente-tab-btn').forEach(b => { b.classList.remove('bg-blue-600'); b.classList.add('text-gray-400'); });
            if (btn) { btn.classList.add('bg-blue-600'); btn.classList.remove('text-gray-400'); }
            // ejecutar render según pestaña
            if (tabId === 'monitoreo') renderMonitoreo();
            if (tabId === 'activos') renderActivos();
            if (tabId === 'historialCliente') renderHistorial();
        }

        // Renderizado y lógica mínima para las secciones cliente
        let _monitoreoInterval = null;
        function renderMonitoreo() {
            const container = document.getElementById('cliente-monitoreo');
            if (!container) return;
            const listId = 'cliente-monitoreo-list';
            let list = document.getElementById(listId);
            if (!list) {
                list = document.createElement('div');
                list.id = listId;
                list.className = 'space-y-3 mt-4';
                container.appendChild(list);
            }
            // limpiar
            list.innerHTML = '';
            // construir lista a partir de las tarjetas de 'Mis solicitudes'
            const cards = document.querySelectorAll('#clienteMisOrdenes [data-id]');
            if (!cards || cards.length === 0) {
                list.innerHTML = '<div class="text-gray-400">No hay solicitudes para monitorear.</div>';
                return;
            }
            cards.forEach(c => {
                const id = c.getAttribute('data-id');
                const nombre = c.getAttribute('data-nombre');
                const estadoEl = c.querySelector('[id^="status-"]');
                const estado = estadoEl ? estadoEl.innerText : (c.getAttribute('data-estado') || 'N/A');
                const row = document.createElement('div');
                row.className = 'bg-gray-900 border border-gray-800 p-3 rounded-lg flex items-center justify-between';
                row.innerHTML = `<div><div class="font-bold text-white">${nombre}</div><div class="text-xs text-gray-400">${id}</div></div><div class="text-sm"><span class="px-3 py-1 rounded-full bg-yellow-950 text-yellow-400 border border-yellow-900">${estado}</span></div>`;
                list.appendChild(row);
            });

            // limpiar intervalo previo
            if (_monitoreoInterval) clearInterval(_monitoreoInterval);
            // simular polling para refrescar estados cada 7s (lee estado desde DOM)
            _monitoreoInterval = setInterval(() => {
                document.querySelectorAll('#cliente-monitoreo-list [id^="status-"]');
            }, 7000);
        }

        function renderActivos() {
            const container = document.getElementById('cliente-activos');
            if (!container) return;
            let tbl = document.getElementById('cliente-activos-table');
            if (!tbl) {
                tbl = document.createElement('div');
                tbl.id = 'cliente-activos-table';
                tbl.className = 'mt-4 overflow-x-auto';
                tbl.innerHTML = `
                    <table class="w-full text-left border-collapse text-sm text-gray-300">
                        <thead><tr class="text-xs text-gray-400 uppercase"><th class="p-3">Activo</th><th class="p-3">S/N</th><th class="p-3">Estado</th><th class="p-3">Próximo Preventivo</th><th class="p-3 text-right">Acciones</th></tr></thead>
                        <tbody id="cliente-activos-body"></tbody>
                    </table>
                `;
                container.appendChild(tbl);
            }
            const body = document.getElementById('cliente-activos-body');
            body.innerHTML = '';
            // poblar desde las tarjetas
            document.querySelectorAll('#clienteMisOrdenes [data-id]').forEach(c => {
                const nombre = c.getAttribute('data-nombre');
                const serie = c.getAttribute('data-serie') || 'N/A';
                const estadoEl = c.querySelector('[id^="status-"]');
                const estado = estadoEl ? estadoEl.innerText : (c.getAttribute('data-estado') || 'N/A');
                const rawId = c.getAttribute('data-id') || '';
                const idMatch = rawId.match(/eq-(\d+)/);
                const idNum = idMatch ? idMatch[1] : rawId;
                const clave = `preventivo_${idNum}`;
                const prox = localStorage.getItem(clave) || 'No programado';
                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-800';
                tr.innerHTML = `
                    <td class="p-3 font-bold text-white">${nombre}</td>
                    <td class="p-3 font-mono text-gray-400">${serie}</td>
                    <td class="p-3"><span class="text-xs px-2 py-1 rounded-full bg-gray-900 border border-gray-700">${estado}</span></td>
                    <td class="p-3"><input type="date" data-id="${idNum}" value="${prox === 'No programado' ? '' : prox}" class="bg-gray-900 border border-gray-700 rounded px-2 py-1 text-sm text-white" /></td>
                    <td class="p-3 text-right"><button data-id="${idNum}" class="px-3 py-1 bg-blue-600 text-white rounded" onclick="guardarPreventivo(this)">Guardar</button></td>
                `;
                body.appendChild(tr);
            });
        }

        function guardarPreventivo(btn) {
            const id = btn.getAttribute('data-id');
            const input = document.querySelector(`#cliente-activos-body input[data-id='${id}']`);
            if (!input) return alert('No existe el input.');
            const val = input.value;
            const clave = `preventivo_${id}`;
            if (!val) {
                localStorage.removeItem(clave);
                alert('Preventivo eliminado.');
            } else {
                localStorage.setItem(clave, val);
                alert('Preventivo guardado: ' + val);
            }
            // refrescar
            renderActivos();
        }

        function renderHistorial() {
            const container = document.getElementById('cliente-historial');
            if (!container) return;
            let metrics = document.getElementById('cliente-hist-metrics');
            if (!metrics) {
                metrics = document.createElement('div');
                metrics.id = 'cliente-hist-metrics';
                metrics.className = 'grid grid-cols-3 gap-4 mt-4';
                container.appendChild(metrics);
            }
            // calcular métricas desde tarjetas
            const cards = document.querySelectorAll('#clienteMisOrdenes [data-id]');
            const total = cards.length;
            let ejecutadas = 0; let arreglados = 0; let tiempoBanco = 0;
            cards.forEach(c => {
                const estadoEl = c.querySelector('[id^="status-"]');
                const estado = estadoEl ? estadoEl.innerText : (c.getAttribute('data-estado') || 'En espera');
                if (estado === 'Arreglado' || estado === 'Terminado') arreglados++;
                ejecutadas += (estado === 'Terminado') ? 1 : 0;
            });
            const tasa = total === 0 ? 0 : Math.round((arreglados / total) * 100);
            metrics.innerHTML = `
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 text-center"><div class="text-xl font-black text-blue-600">${ejecutadas}</div><div class="text-[10px] text-gray-600 mt-0.5">OTs Ejecutadas</div></div>
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 text-center"><div class="text-xl font-black text-green-600">${tasa}%</div><div class="text-[10px] text-gray-600 mt-0.5">Tasa de Cierre</div></div>
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-200 text-center"><div class="text-xl font-black text-purple-600">${Math.round(tiempoBanco)}</div><div class="text-[10px] text-gray-600 mt-0.5">Tiempo en Banco</div></div>
            `;

            // tabla de historial simple
            let tabla = document.getElementById('cliente-hist-tabla');
            if (!tabla) {
                tabla = document.createElement('div');
                tabla.id = 'cliente-hist-tabla';
                tabla.className = 'mt-4 overflow-x-auto';
                tabla.innerHTML = `
                    <table class="w-full text-left border-collapse text-sm text-gray-300">
                        <thead><tr class="text-xs text-gray-400 uppercase"><th class="p-3">Nombre</th><th class="p-3">S/N</th><th class="p-3">Estado</th><th class="p-3">Acción</th></tr></thead>
                        <tbody id="cliente-hist-body"></tbody>
                    </table>
                `;
                container.appendChild(tabla);
            }
            const body = document.getElementById('cliente-hist-body');
            body.innerHTML = '';
            document.querySelectorAll('#clienteMisOrdenes [data-id]').forEach(c => {
                const nombre = c.getAttribute('data-nombre');
                const serie = c.getAttribute('data-serie') || 'N/A';
                const estadoEl = c.querySelector('[id^="status-"]');
                const estado = estadoEl ? estadoEl.innerText : (c.getAttribute('data-estado') || 'N/A');
                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-800';
                tr.innerHTML = `
                    <td class="p-3 font-bold text-white">${nombre}</td>
                    <td class="p-3 font-mono text-gray-400">${serie}</td>
                    <td class="p-3"><span class="text-xs px-2 py-1 rounded-full bg-gray-900 border border-gray-700">${estado}</span></td>
                    <td class="p-3 text-right"><button class="px-3 py-1 bg-gray-700 text-white rounded" onclick="verBitacora('${c.getAttribute('data-id')}')">Ver Bitácora</button></td>
                `;
                body.appendChild(tr);
            });
        }

        function openEquiposFromAlert() {
            const btn = Array.from(document.querySelectorAll('.tab-btn')).find(b => b.textContent.includes('Equipos en Taller'));
            if (btn) switchTab('equipos', btn);
            else {
                const first = document.querySelector('.tab-btn');
                if (first) switchTab('equipos', first);
            }
        }

        const modal = document.getElementById('modalEquipo');
        const form = document.getElementById('formNuevoEquipo');
        const listaEquipos = document.getElementById('listaEquipos');
        const tablaHistorialBody = document.getElementById('tablaHistorialBody');
        let contadorOT = 3; 

        function openModal() { modal.classList.remove('hidden'); }
        function closeModal() { modal.classList.add('hidden'); form.reset(); }

        function agregarEquipo(event) {
            event.preventDefault();
            const nombre = document.getElementById('inputNombre').value;
            const tipo = document.getElementById('inputTipo').value;
            const marca = document.getElementById('inputMarca').value;
            const serie = document.getElementById('inputSerie').value || null;
            const falla = document.getElementById('inputFalla').value || null;

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/equipos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ nombre, tipo, marca, serie, falla })
            }).then(res => {
                if (!res.ok) throw new Error('Error al guardar');
                return res.json();
            }).then(data => {
                const eq = data.equipo;
                const nuevoId = `eq-${eq.id}`;
                const badgeColor = eq.tipo === 'Correctivo' ? 'bg-red-950 text-red-400 border-red-900' : 'bg-green-950 text-green-400 border-green-900';

                const nuevaTarjeta = document.createElement('div');
                nuevaTarjeta.className = "bg-gray-800 border border-gray-700 rounded-xl p-5 shadow-md flex flex-col justify-between";
                nuevaTarjeta.setAttribute('data-id', nuevoId);
                nuevaTarjeta.setAttribute('data-tipo', eq.tipo);
                nuevaTarjeta.setAttribute('data-nombre', eq.nombre);
                nuevaTarjeta.setAttribute('data-marca', eq.marca);
                nuevaTarjeta.setAttribute('data-serie', eq.serie || 'N/A');
                nuevaTarjeta.setAttribute('data-falla', eq.falla || '');
                nuevaTarjeta.setAttribute('data-telefono', eq.telefono || '');
                nuevaTarjeta.setAttribute('data-responsable', eq.responsable || 'N/A');
                nuevaTarjeta.setAttribute('data-user-id', eq.user && eq.user.id ? eq.user.id : '');
                nuevaTarjeta.setAttribute('data-cliente', (eq.cliente && eq.cliente.name) ? eq.cliente.name : (eq.user && eq.user.name ? eq.user.name : 'N/A'));

                nuevaTarjeta.innerHTML = `
                    <div>
                        <div class="flex justify-between items-start mb-3">
                            <span class="px-2.5 py-0.5 text-xs font-semibold ${badgeColor} border rounded-full">${eq.tipo}</span>
                            <span class="text-xs font-mono text-gray-500">OT: #${String(eq.id).padStart(3,'0')}</span>
                        </div>
                        <h3 class="text-lg font-bold text-white">${eq.nombre}</h3>
                        <div class="mt-2 flex items-center gap-3">
                            <div class="text-xs px-2.5 py-0.5 rounded-full bg-gray-900 text-gray-300 border border-gray-700">Estado: <strong id="status-${eq.id}">${eq.estado || 'En espera'}</strong></div>
                            <div>
                                <select onchange="cambiarEstadoCard(${eq.id}, this.value, this)" class="cardEstadoSelect text-xs bg-gray-900 text-white border border-gray-700 rounded px-2 py-1">
                                    <option value="En espera">En espera</option>
                                    <option value="En proceso">En proceso</option>
                                    <option value="Arreglado">Arreglado</option>
                                    <option value="Terminado">Terminado</option>
                                    <option value="En espera de repuestos">En espera de repuestos</option>
                                </select>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">S/N: ${eq.serie || 'N/A'} | Marca: ${eq.marca || 'N/A'}</p>
                        <div class="mt-4 bg-gray-900/50 p-3 rounded-lg border border-gray-700/50 flex items-center gap-2 text-xs text-gray-400">
                            <i class="fas fa-user text-blue-500"></i> <span>Responsable: <strong>${eq.responsable || 'Ing. Carlos Mendoza'}</strong></span>
                        </div>
                        <div class="mt-2 bg-gray-900/40 p-2 rounded-lg border border-gray-700/50 text-xs text-gray-400 flex items-center gap-2">
                            <i class="fas fa-user-circle text-indigo-400"></i> <span>Cliente: <strong>${(eq.cliente && eq.cliente.name) ? eq.cliente.name : (eq.user && eq.user.name ? eq.user.name : 'N/A')}</strong></span>
                        </div>
                        <div class="mt-2 bg-gray-900/40 p-2 rounded-lg border border-gray-700/50 text-xs text-gray-400 flex items-center gap-2">
                            <i class="fas fa-phone text-blue-500"></i> <span>Contacto: <strong>${eq.telefono || 'N/A'}</strong></span>
                        </div>
                    </div>
                    <div class="mt-5 pt-3 border-t border-gray-700 text-xs text-gray-500 flex justify-between items-center">
                        <span><i class="far fa-calendar mr-1"></i> Asignado: Reciente</span>
                        <button onclick="verDetalles(this.closest('[data-id]'))" class="text-blue-400 hover:text-blue-300 font-medium flex items-center gap-1.5 transition">
                            Ver detalles <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                `;
                listaEquipos.prepend(nuevaTarjeta);

                baseBitacoras[nuevoId] = data.bitacoras.map(b => ({ fecha: b.fecha || new Date().toLocaleDateString(), ot: b.ot, tarea: b.tarea, detalle: b.detalle }));

                const nuevaFila = document.createElement('tr');
                nuevaFila.id = `hist-${nuevoId}`;
                nuevaFila.setAttribute('data-hist-nombre', eq.nombre);
                nuevaFila.setAttribute('data-hist-serie', eq.serie || 'N/A');
                nuevaFila.className = "divide-y divide-gray-700 text-sm text-gray-300";
                nuevaFila.innerHTML = `
                    <td class="p-4 font-bold text-white">${eq.nombre}</td>
                    <td class="p-4 font-mono text-gray-400">${eq.serie || 'N/A'}</td>
                    <td class="p-4 text-center"><span class="bg-blue-950 text-blue-400 border border-blue-800 text-xs px-2.5 py-0.5 rounded-full font-bold">1 Vez</span></td>
                    <td class="p-4 text-right"><button onclick="verBitacora('${nuevoId}')" class="bg-gray-700 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg transition font-medium">Ver Bitácora</button></td>
                `;
                tablaHistorialBody.prepend(nuevaFila);

                closeModal();
            }).catch(err => {
                console.error(err);
                alert('No se pudo guardar el equipo.');
            });
        }

        // Crear solicitud desde el Panel Cliente
        function crearSolicitudCliente() {
            const nombre = document.getElementById('clientNombre').value;
            const tipo = document.getElementById('clientTipo').value || 'Correctivo';
            const marca = document.getElementById('clientMarca').value || null;
            const serie = document.getElementById('clientSerie').value || null;
            const falla = document.getElementById('clientFalla').value || null;

            if (!nombre || nombre.trim() === '') return alert('Ingresa el nombre del equipo.');

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/equipos', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                body: JSON.stringify({ nombre, tipo, marca, serie, falla })
            }).then(r => {
                if (!r.ok) throw new Error('Error al crear solicitud');
                return r.json();
            }).then(data => {
                const eq = data.equipo;
                const nuevoId = `eq-${eq.id}`;

                const cont = document.getElementById('clienteMisOrdenes');
                if (cont) {
                    const card = document.createElement('div');
                    card.className = "bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-xl flex flex-col justify-between space-y-4";
                    card.id = `mis-eq-${eq.id}`;
                    card.setAttribute('data-id', nuevoId);
                    card.setAttribute('data-nombre', eq.nombre);
                    card.setAttribute('data-cliente', (eq.cliente && eq.cliente.name) ? eq.cliente.name : (eq.user && eq.user.name ? eq.user.name : 'N/A'));
                    card.innerHTML = `
                        <div>
                            <div class="flex justify-between items-center border-b border-gray-700 pb-3">
                                <span class="text-xs font-mono font-bold text-blue-400 bg-gray-950 px-3 py-1 rounded-lg border border-gray-800">OT-#${String(eq.id).padStart(3,'0')}</span>
                                <span class="px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider bg-yellow-950/80 text-yellow-400 border border-yellow-900 rounded-full flex items-center gap-1.5">Estado: <strong id=\"status-${eq.id}\">${eq.estado || 'En espera'}</strong></span>
                            </div>
                                <div class="mt-4">
                                <h3 class="text-xl font-bold text-white">${eq.nombre}</h3>
                                <p class="text-xs text-gray-400 font-mono mt-0.5">S/N: ${eq.serie || 'N/A'} | Marca: ${eq.marca || 'N/A'}</p>
                                <p class="text-xs text-gray-400 mt-2">Cliente: <strong class="text-white">${(eq.cliente && eq.cliente.name) ? eq.cliente.name : (eq.user && eq.user.name ? eq.user.name : 'N/A')}</strong></p>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-xs font-mono text-gray-400 flex items-center gap-1.5 bg-gray-900/60 px-3 py-1.5 rounded-lg border border-gray-700/50">
                                <i class="far fa-clock text-blue-500"></i> Asignado: <strong class="text-blue-400 font-bold">Ahora</strong>
                            </div>
                            <button onclick="verDetalles(this.closest('[data-id]'))" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition shadow-lg flex items-center justify-center gap-2">Ver Ficha</button>
                        </div>
                    `;
                    cont.prepend(card);
                }

                // actualizar historial local
                try {
                    const tabla = document.getElementById('tablaHistorialBody');
                    if (tabla) {
                        const tr = document.createElement('tr');
                        tr.id = `hist-${nuevoId}`;
                        tr.setAttribute('data-hist-nombre', eq.nombre);
                        tr.setAttribute('data-hist-serie', eq.serie || 'N/A');
                        tr.innerHTML = `
                            <td class="p-4 font-bold text-white">${eq.nombre}</td>
                            <td class="p-4 font-mono text-gray-400">${eq.serie || 'N/A'}</td>
                            <td class="p-4 text-center"><span class="bg-blue-950 text-blue-400 border border-blue-800 text-xs px-2.5 py-0.5 rounded-full font-bold">1 Vez</span></td>
                            <td class="p-4 text-right"><button onclick="verBitacora('${nuevoId}')" class="bg-gray-700 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg transition font-medium">Ver Bitácora</button></td>
                        `;
                        tabla.prepend(tr);
                    }
                } catch(e) { console.error(e); }

                // limpiar formulario
                document.getElementById('clientNombre').value = '';
                document.getElementById('clientTipo').value = '';
                document.getElementById('clientMarca').value = '';
                document.getElementById('clientSerie').value = '';
                document.getElementById('clientFalla').value = '';

                alert('Solicitud enviada correctamente.');
            }).catch(err => {
                console.error(err);
                alert('No se pudo crear la solicitud.');
            });
        }

        const modalDetail = document.getElementById('modalDetail');
        const modalPanel = document.getElementById('modalPanel');
        function verDetalles(el) {
            document.getElementById('detNombre').innerText = el.getAttribute('data-nombre');
            document.getElementById('detTipo').innerText = el.getAttribute('data-tipo');
            document.getElementById('detMarca').innerText = el.getAttribute('data-marca');
            document.getElementById('detSerie').innerText = el.getAttribute('data-serie');
            document.getElementById('detFalla').innerText = el.getAttribute('data-falla');
            document.getElementById('detTelefono').innerText = el.getAttribute('data-telefono') || '---';
            const otEl = el.querySelector('.font-mono');
            document.getElementById('detOT').innerText = otEl ? otEl.innerText.replace('OT: ', '') : '---';
            document.getElementById('detResponsable').innerText = el.getAttribute('data-responsable') || (el.getAttribute('data-responsable') === '' ? '---' : '---');
            document.getElementById('detCliente').innerText = el.getAttribute('data-cliente') || '---';
            // store equipo id on modal for later actions
            const rawId = el.getAttribute('data-id') || '';
            const idMatch = rawId.match(/eq-(\d+)/);
            if (idMatch) modalDetail.dataset.equipoId = idMatch[1];
            else modalDetail.dataset.equipoId = '';

            // if admin, populate user select and set current selection
            if (usersList && document.getElementById('selectReasignar')) {
                const sel = document.getElementById('selectReasignar');
                sel.innerHTML = '';
                usersList.forEach(u => {
                    const opt = document.createElement('option');
                    opt.value = u.id; opt.text = u.name + ' <' + u.email + '>';
                    sel.appendChild(opt);
                });
                const currentUserId = el.getAttribute('data-user-id') || '';
                if (currentUserId) sel.value = currentUserId;
            }
            // el estado se muestra en la ficha pero sólo se actualiza desde las tarjetas
            // show overlay then slide panel in
            modalDetail.classList.remove('hidden');
            // allow next tick for transition
            setTimeout(() => {
                modalPanel.classList.remove('translate-x-full');
                modalPanel.classList.add('translate-x-0');
            }, 25);
        }
        function closeDetailModal() {
            // slide panel out then hide overlay after transition
            if (modalPanel) {
                modalPanel.classList.remove('translate-x-0');
                modalPanel.classList.add('translate-x-full');
            }
            setTimeout(() => { modalDetail.classList.add('hidden'); }, 320);
        }

        // close when clicking on overlay outside the panel
        if (modalDetail) {
            modalDetail.addEventListener('click', function(e) {
                if (e.target === modalDetail) closeDetailModal();
            });
        }
        // close on ESC
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') { if (!modalDetail.classList.contains('hidden')) closeDetailModal(); } });

        // swipe down to close (touch devices)
        (function(){
            let touchStartY = 0;
            let touchCurrentY = 0;
            const threshold = 100; // px to consider swipe
            if (modalPanel) {
                modalPanel.addEventListener('touchstart', function(ev){ if (ev.touches && ev.touches[0]) touchStartY = ev.touches[0].clientY; }, {passive:true});
                modalPanel.addEventListener('touchmove', function(ev){ if (ev.touches && ev.touches[0]) touchCurrentY = ev.touches[0].clientY; }, {passive:true});
                modalPanel.addEventListener('touchend', function(ev){
                    const delta = touchCurrentY - touchStartY;
                    if (delta > threshold) {
                        closeDetailModal();
                    }
                    touchStartY = 0; touchCurrentY = 0;
                }, {passive:true});
            }
        })();

        // Modal comentario elements and handlers
        const modalComentario = document.getElementById('modalComentario');
        let comentarioEquipoId = null;
        function openComentario(el) {
            if (!el) return alert('Elemento no encontrado.');
            const nombre = el.getAttribute('data-nombre') || '---';
            document.getElementById('comentEquipoNombre').innerText = nombre;
            comentarioEquipoId = el.getAttribute('data-id') || '';
            if (modalComentario) modalComentario.classList.remove('hidden');
        }
        function closeComentarioModal() {
            if (modalComentario) modalComentario.classList.add('hidden');
            const ta = document.getElementById('comentarioTexto'); if (ta) ta.value = '';
            comentarioEquipoId = null;
        }
        function submitComentario() {
            const ta = document.getElementById('comentarioTexto'); if (!ta) return alert('Textarea no encontrado');
            const texto = ta.value.trim(); if (!texto) return alert('Escribe un comentario antes de enviar.');
            const rawId = comentarioEquipoId || '';
            const m = rawId.match(/eq-(\d+)/);
            const eqId = m ? m[1] : rawId;
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token = tokenMeta ? tokenMeta.getAttribute('content') : '';
            // intentar enviar al servidor; si falla, guardar en localStorage
            fetch(`/equipos/${eqId}/comentarios`, {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                body: JSON.stringify({ comentario: texto })
            }).then(r => {
                if (!r.ok) throw new Error('Error en servidor');
                return r.json();
            }).then(data => {
                const autor = (data.comentario && data.comentario.autor) ? data.comentario.autor : (data.usuario_nombre || 'Tú');
                const textoServer = (data.comentario && data.comentario.texto) ? data.comentario.texto : texto;
                appendCommentToCard(rawId, textoServer, autor);
                alert('Comentario enviado.');
                closeComentarioModal();
            }).catch(err => {
                const key = `comentarios_${eqId}`;
                const prev = JSON.parse(localStorage.getItem(key) || '[]');
                prev.push({ texto, fecha: new Date().toISOString(), autor: 'Tú (offline)' });
                localStorage.setItem(key, JSON.stringify(prev));
                appendCommentToCard(rawId, texto, 'Tú (offline)');
                alert('No fue posible enviar al servidor. Comentario guardado localmente.');
                closeComentarioModal();
            });
        }
        function appendCommentToCard(cardId, texto, autor) {
            const sel = document.querySelector(`[data-id='${cardId}']`);
            if (!sel) return;
            let commentsContainer = sel.querySelector('.card-comments');
            if (!commentsContainer) {
                commentsContainer = document.createElement('div');
                commentsContainer.className = 'mt-3 card-comments space-y-2 text-sm text-gray-300';
                sel.appendChild(commentsContainer);
            }
            const div = document.createElement('div');
            div.className = 'bg-gray-900 p-2 rounded-lg border border-gray-800 text-xs';
            div.innerHTML = `<div class="font-semibold text-white">${autor}</div><div class="text-gray-400">${texto}</div><div class="text-gray-500 text-[10px] mt-1">${new Date().toLocaleString()}</div>`;
            commentsContainer.prepend(div);
            // también actualizar baseComentarios para que modal/historial lo muestre sin recargar
            try {
                if (typeof baseComentarios !== 'undefined') {
                    if (!baseComentarios[cardId]) baseComentarios[cardId] = [];
                    baseComentarios[cardId].unshift({ fecha: new Date().toLocaleString(), autor: autor, texto: texto });
                }
            } catch(e) { console.error(e); }
        }

        const modalBitacora = document.getElementById('modalBitacora');
        const timeline = document.getElementById('timelineContenedor');
        function verBitacora(id) {
            const f = document.getElementById(`hist-${id}`);
            document.getElementById('bitacoraEquipoNombre').innerText = f.getAttribute('data-hist-nombre');
            timeline.innerHTML = "";
            // bitácoras históricas
            (baseBitacoras[id] || []).forEach(item => {
                const p = document.createElement('div');
                p.className = "relative pl-6 border-l border-gray-700 ml-2";
                p.innerHTML = `<span class="absolute -left-[5px] top-1.5 bg-blue-500 w-2 h-2 rounded-full"></span><div class="bg-gray-950 p-3 rounded-lg text-xs"><span class="text-blue-400 font-mono"><i class="far fa-clock text-[10px] mr-1"></i>${item.fecha} (OT: ${item.ot})</span><h5 class="text-white font-bold mt-1">${item.tarea}</h5><p class="text-gray-400 italic mt-0.5">${item.detalle}</p></div>`;
                timeline.appendChild(p);
            });
            // comentarios relacionados
            (baseComentarios[id] || []).forEach(item => {
                const p = document.createElement('div');
                p.className = "relative pl-6 border-l border-gray-700 ml-2";
                p.innerHTML = `<span class="absolute -left-[5px] top-1.5 bg-indigo-500 w-2 h-2 rounded-full"></span><div class="bg-gray-900 p-3 rounded-lg text-xs"><span class="text-indigo-300 font-mono"><i class="far fa-comment text-[10px] mr-1"></i>${item.fecha}</span><h5 class="text-white font-bold mt-1">Comentario de ${item.autor}</h5><p class="text-gray-400 italic mt-0.5">${item.texto}</p></div>`;
                timeline.appendChild(p);
            });
            modalBitacora.classList.remove('hidden');
        }
        function closeBitacoraModal() { modalBitacora.classList.add('hidden'); }

        function filtrarHistorial() {
            const textoBusqueda = document.getElementById('inputBuscarHistorial').value.toLowerCase().trim();
            const filas = document.querySelectorAll('#tablaHistorialBody tr');
            const divSinResultados = document.getElementById('sinResultados');
            let hayCoincidencias = false;

            filas.forEach(fila => {
                const nombreEquipo = fila.getAttribute('data-hist-nombre').toLowerCase();
                const numeroSerie = fila.getAttribute('data-hist-serie').toLowerCase();

                if (nombreEquipo.includes(textoBusqueda) || numeroSerie.includes(textoBusqueda)) {
                    fila.style.display = ""; 
                    hayCoincidencias = true;
                } else {
                    fila.style.display = "none"; 
                }
            });

            if (hayCoincidencias) { divSinResultados.classList.add('hidden'); } 
            else { divSinResultados.classList.remove('hidden'); }
        }

        function cambiarEstadoOrden(btn) {
            if (btn.classList.contains('bg-green-600')) {
                btn.className = "w-full sm:w-auto bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition shadow-lg flex items-center justify-center gap-2";
                btn.innerHTML = `<i class="fas fa-spinner animate-spin text-[10px]"></i> Pausar / Registrar Avance`;
            } else {
                btn.className = "w-full sm:w-auto bg-gray-700 text-gray-400 font-semibold text-xs px-4 py-2.5 rounded-xl cursor-not-allowed flex items-center justify-center gap-2";
                btn.innerHTML = `<i class="fas fa-circle-check text-[10px]"></i> Enviado a Control de Calidad`;
                btn.disabled = true;
            }
        }

        function setRoleDashboard(userId) {
            const sel = document.getElementById(`role-select-dashboard-${userId}`);
            if (!sel) return alert('Selector no encontrado');
            const role = sel.value;
            if (!confirm('Confirmar cambio de rol?')) return;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/admin/usuarios/${userId}/role`, {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                body: JSON.stringify({ role })
            }).then(async r => {
                if (!r.ok) {
                    let text = '';
                    try { text = await r.text(); } catch(_) { text = '' }
                    console.error('Error response', r.status, text);
                    let msg = 'No se pudo actualizar el rol';
                    try { const j = JSON.parse(text); if (j && j.message) msg = j.message; } catch(_) {}
                    throw new Error(`${r.status} - ${msg}`);
                }
                return r.json();
            }).then(data => {
                const row = document.getElementById(`user-${userId}`);
                if (row) {
                    const roleCell = row.querySelector('[data-role]');
                    roleCell.innerText = data.user.role || role;
                }
                alert('Rol actualizado');
            }).catch(e => { console.error(e); alert(e.message || 'No se pudo actualizar el rol'); });
        }

        function cambiarEstadoCard(equipoId, nuevoEstado, el) {
            if (!confirm('Confirmar cambio de estado?')) return;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const comentario = document.getElementById('modalComentario') ? document.getElementById('modalComentario').value : '';
            fetch(`/equipos/${equipoId}/estado`, {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                body: JSON.stringify({ estado: nuevoEstado, comentario })
            }).then(r => {
                if (!r.ok) throw new Error('Error al actualizar');
                return r.json();
            }).then(data => {
                // actualizar tarjeta principal
                // actualizar todas las tarjetas que correspondan a este equipo
                try {
                    const cards = document.querySelectorAll(`[data-id='eq-${equipoId}']`);
                    cards.forEach(card => {
                        card.setAttribute('data-estado', data.equipo.estado);
                        // actualizar select interno si lo tiene
                        const sel = card.querySelector('select.cardEstadoSelect');
                            if (sel) {
                            sel.value = data.equipo.estado;
                            // mantener el select visible con la nueva opción seleccionada
                            sel.style.display = '';
                            const statusElLocal = card.querySelector(`#status-${equipoId}`);
                            if (statusElLocal) {
                                statusElLocal.innerText = data.equipo.estado;
                                const statusContainer = statusElLocal.parentNode;
                                // mantener el select siempre visible; el usuario abre el desplegable manualmente
                                statusContainer.style.cursor = 'default';
                                // reemplazar otros nodos que contengan texto 'Estado:' para evitar duplicados
                                const possible = card.querySelectorAll('span,div,p');
                                possible.forEach(n => {
                                    if (n === statusElLocal || n === statusContainer) return;
                                    // no tocar elementos que contienen selects u otros hijos (evita destruir el <select>)
                                    if (n.querySelector && n.querySelector('select')) return;
                                    if (n.innerText && /Estado\s*:/i.test(n.innerText)) {
                                        try {
                                            n.innerHTML = n.innerHTML.replace(/Estado\s*:\s*([^<]*)/i, `Estado: <strong id="status-${equipoId}">${data.equipo.estado}</strong>`);
                                        } catch(err) {
                                            n.innerText = `Estado: ${data.equipo.estado}`;
                                        }
                                    }
                                });
                            }
                        }
                        // actualizar badge/estado por id si existe
                        const statusEl = document.getElementById(`status-${equipoId}`);
                        if (statusEl) statusEl.innerText = data.equipo.estado;
                        // actualizar posibles textos que contienen 'Estado:' en otros nodos
                        const nodes = card.querySelectorAll('span,div');
                        nodes.forEach(n => {
                            // solo actualizar nodos sin hijos para no eliminar elementos como <select>
                            if (n.children && n.children.length > 0) return;
                            if (n.innerText && /estado\s*:/i.test(n.innerText)) {
                                n.innerText = n.innerText.replace(/Estado\s*:\s*.*/i, `Estado: ${data.equipo.estado}`);
                            }
                        });
                    });
                } catch(e) { console.error(e); }

                // actualizar panel si está abierto y es el mismo equipo (solo mostrar estado)
                if (modalDetail && modalDetail.dataset && String(modalDetail.dataset.equipoId) == String(equipoId)) {
                    // el panel mostrará el nuevo estado a través de las tarjetas vinculadas; no hay control editable aquí
                }

                // actualizar Mis Órdenes (si existe la card en Mis Órdenes)
                const myCard = document.getElementById(`mis-eq-${equipoId}`);
                if (myCard) {
                    const badge = myCard.querySelector('span[class*="Estado:"]');
                    // fallback: buscar el span que contiene 'Estado:'
                    const spans = myCard.querySelectorAll('span');
                    spans.forEach(s => {
                        if (s.innerText && s.innerText.trim().toLowerCase().includes('estado')) {
                            // actualizar el texto después de ':'
                            s.innerHTML = s.innerHTML.replace(/Estado:\s*[^<]*/i, `Estado: ${data.equipo.estado}`);
                        }
                    });
                }

                // actualizar Historial: incrementar contador de intervenciones si hay nueva bitácora
                try {
                    const histRow = document.getElementById(`hist-eq-${equipoId}`);
                    if (histRow) {
                        const span = histRow.querySelector('td:nth-child(3) span');
                        if (span) {
                            // extraer numero actual
                            const txt = span.innerText || '';
                            const m = txt.match(/(\d+)/);
                            let n = m ? parseInt(m[1]) : 0;
                            // si el servidor devolvió una bitacora, incrementar
                            if (data.bitacora) n = n + 1;
                            span.innerText = `${n} Veces`;
                        }
                    }
                } catch(e) { console.error(e); }

                // sincronizar Arreglados: añadir o eliminar fila (más robusto)
                try {
                    function estadoEsArreglado(estado) {
                        if (!estado) return false;
                        const s = String(estado).toLowerCase();
                        return s.includes('arreg') || s.includes('repar') || s.includes('term') || s.includes('complet');
                    }

                    const tbody = document.getElementById('arregladosBody');
                    if (tbody) {
                        const existingRow = document.getElementById(`arreglados-row-${equipoId}`);
                        const shouldBeInArreglados = estadoEsArreglado(data.equipo.estado);

                        if (shouldBeInArreglados) {
                            // si ya existe, actualizar columnas
                            if (existingRow) {
                                try {
                                    const cols = existingRow.querySelectorAll('td');
                                    if (cols[0]) cols[0].innerText = data.equipo.nombre || cols[0].innerText;
                                    if (cols[1]) cols[1].innerText = data.equipo.serie ?? 'N/A';
                                    if (cols[2]) cols[2].innerText = data.equipo.marca ?? 'N/A';
                                    if (cols[3]) cols[3].innerText = data.equipo.user ? data.equipo.user.name : (data.equipo.responsable ?? 'N/A');
                                    if (cols[4]) cols[4].innerText = data.equipo.telefono ?? 'N/A';
                                    if (cols[5]) cols[5].innerText = 'Ahora';
                                } catch(_) { /* ignore individual update errors */ }
                            } else {
                                const tr = document.createElement('tr');
                                tr.id = `arreglados-row-${equipoId}`;
                                tr.className = '';
                                tr.innerHTML = `
                                    <td class="p-4 font-bold text-white">${data.equipo.nombre ?? '---'}</td>
                                    <td class="p-4 font-mono text-gray-400">${data.equipo.serie ?? 'N/A'}</td>
                                    <td class="p-4">${data.equipo.marca ?? 'N/A'}</td>
                                    <td class="p-4">${data.equipo.user ? data.equipo.user.name : (data.equipo.responsable ?? 'N/A')}</td>
                                    <td class="p-4">${data.equipo.telefono ?? 'N/A'}</td>
                                    <td class="p-4 text-right">Ahora</td>
                                `;
                                tbody.prepend(tr);
                            }
                        } else {
                            if (existingRow) existingRow.remove();
                        }
                    }
                } catch(e) { console.error(e); }

                // actualizar cache local de bitacoras
                try {
                    const key = `eq-${equipoId}`;
                    baseBitacoras[key] = baseBitacoras[key] || [];
                    if (data.bitacora) {
                        baseBitacoras[key].unshift({ fecha: data.bitacora.fecha, ot: data.bitacora.ot, tarea: data.bitacora.tarea, detalle: data.bitacora.detalle });
                    }
                } catch(e) { console.error(e); }

                alert('Estado actualizado');
            }).catch(e => { console.error(e); alert('No se pudo actualizar el estado'); });
        }

        // Actualizaciones desde la modal han sido deshabilitadas: el estado solo se cambia desde las tarjetas.

        function reasignarEquipoDesdeModal() {
            const equipoId = modalDetail.dataset.equipoId;
            if (!equipoId) return alert('Equipo no identificado');
            const sel = document.getElementById('selectReasignar');
            if (!sel) return alert('No hay lista de técnicos disponible');
            const userId = sel.value;
            if (!userId) return alert('Selecciona un técnico');
            if (!confirm('Confirmar reasignación del equipo?')) return;

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/equipos/${equipoId}/reassign`, {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN': token, 'Accept': 'application/json'},
                body: JSON.stringify({ user_id: userId })
            }).then(r => {
                if (!r.ok) throw new Error('No autorizado o error');
                return r.json();
            }).then(data => {
                // actualizar tarjeta correspondiente
                const card = document.querySelector(`[data-id='eq-${equipoId}']`);
                if (card) {
                    const newName = data.equipo.user ? data.equipo.user.name : sel.options[sel.selectedIndex].text;
                    card.setAttribute('data-responsable', newName);
                    card.setAttribute('data-user-id', data.equipo.user ? data.equipo.user.id : userId);
                    // actualizar texto visible
                    const respSpan = card.querySelector('.mt-4 .fa-user') ? card.querySelector('.mt-4') : null;
                    // actualizar manualmente el elemento donde aparece el responsable en la tarjeta
                    const respBox = card.querySelector('.mt-4');
                    if (respBox) respBox.querySelector('strong').innerText = newName;
                }
                // actualizar modal
                document.getElementById('detResponsable').innerText = data.equipo.user ? data.equipo.user.name : sel.options[sel.selectedIndex].text;
                // actualizar Mis Órdenes: si el equipo fue asignado a mi, añadir; si fue removido de mi, quitar
                try {
                    const prevId = data.previous_user_id;
                    const newId = data.equipo.user ? data.equipo.user.id : null;
                    if (currentUserId && newId === currentUserId) {
                        // añadir si no existe
                        if (!document.getElementById(`mis-eq-${equipoId}`)) {
                            // clonar la tarjeta principal si existe
                            if (card) {
                                const clone = card.cloneNode(true);
                                clone.id = `mis-eq-${equipoId}`;
                                // remove possible duplicate event handlers by ensuring buttons use global functions
                                document.getElementById('misOrdenesList').prepend(clone);
                            }
                        }
                    }
                    if (currentUserId && prevId === currentUserId) {
                        const removed = document.getElementById(`mis-eq-${equipoId}`);
                        if (removed) removed.remove();
                    }
                } catch(e) { console.error(e); }
                alert('Reasignación realizada');
            }).catch(e => { console.error(e); alert('No se pudo reasignar'); });
        }
    </script>
</body>
</html>