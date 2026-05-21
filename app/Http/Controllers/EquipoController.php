<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index()
    {
        $equipos = Equipo::with(['bitacoras','user','cliente','comentarios.user'])->orderByDesc('created_at')->get();

        $bitacoras = [];
        foreach ($equipos as $e) {
            $key = 'eq-' . $e->id;
            $bitacoras[$key] = $e->bitacoras->map(function ($b) {
                // Asegurar que la fecha sea un objeto Carbon antes de formatear
                $fecha = null;
                if (!empty($b->fecha)) {
                    try {
                        $fecha = \Illuminate\Support\Carbon::parse($b->fecha)->format('d M, Y');
                    } catch (\Exception $ex) {
                        $fecha = (string) $b->fecha;
                    }
                }

                return [
                    'fecha' => $fecha,
                    'ot' => $b->ot,
                    'tarea' => $b->tarea,
                    'detalle' => $b->detalle,
                ];
            })->toArray();
        }

        $data = [
            'equipos' => $equipos,
            'bitacorasJson' => json_encode($bitacoras),
        ];

        // preparar comentarios como JSON por equipo (clave: eq-{id})
        $comentariosMap = [];
        foreach ($equipos as $e) {
            $key = 'eq-' . $e->id;
            $comentariosMap[$key] = $e->comentarios->map(function ($c) {
                return [
                    'fecha' => $c->created_at ? $c->created_at->format('d M, Y H:i') : null,
                    'autor' => $c->user ? $c->user->name : 'Anónimo',
                    'texto' => $c->comentario,
                ];
            })->toArray();
        }
        $data['comentariosJson'] = json_encode($comentariosMap);

        // Si el usuario es admin, pasar lista de usuarios para reasignaciones y gestión
        if (auth()->check() && auth()->user()->isAdmin()) {
            $users = User::select('id','name','email')->orderBy('name')->get();
            $data['usersJson'] = json_encode($users);
            $data['usersList'] = $users;
        }

        // Equipos asignados al usuario actual (Mis Órdenes)
        if (auth()->check()) {
            $mis = Equipo::with(['comentarios.user','cliente'])->where('user_id', auth()->id())->orderByDesc('created_at')->get();
            $data['misEquipos'] = $mis;
        }

        // Detectar equipos sin reclamar por más de X días (por ejemplo 30 días)
        $staleDays = 30;
        $staleEquipos = Equipo::whereNull('user_id')
            ->where('created_at', '<=', now()->subDays($staleDays))
            ->orderBy('created_at')
            ->get();
        $data['staleEquipos'] = $staleEquipos;
        $data['staleDays'] = $staleDays;

        // Equipos con estado que indica reparación/terminado (variantes)
        $arreglados = Equipo::with(['user','cliente'])
            ->where(function ($q) {
                $q->whereRaw("LOWER(estado) LIKE ?", ['%arreg%'])
                  ->orWhereRaw("LOWER(estado) LIKE ?", ['%repar%'])
                  ->orWhereRaw("LOWER(estado) LIKE ?", ['%term%'])
                  ->orWhereRaw("LOWER(estado) LIKE ?", ['%complet%']);
            })->orderByDesc('updated_at')->get();
        $data['arreglados'] = $arreglados;

        return view('dashboard', $data);
    }

    public function reassign(Request $request, Equipo $equipo)
    {
        $user = $request->user();
        if (!$user || !$user->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $previousUserId = $equipo->user_id;

        $equipo->user_id = $data['user_id'];
        $equipo->responsable = \App\Models\User::find($data['user_id'])->name;
        $equipo->save();

        return response()->json([
            'message' => 'Asignación actualizada',
            'equipo' => $equipo->fresh('user'),
            'previous_user_id' => $previousUserId,
        ]);
    }

    public function updateEstado(Request $request, Equipo $equipo)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $data = $request->validate([
            'estado' => 'required|string|max:100',
            'comentario' => 'nullable|string',
        ]);

        $old = $equipo->estado;
        $equipo->estado = $data['estado'];
        $equipo->save();

        // Registrar cambio en bitácora (incluir comentario si existe)
        $detalle = isset($data['comentario']) && trim($data['comentario']) !== '' ? trim($data['comentario']) : "Cambiado por {$user->name} (id: {$user->id})";
        $bit = Bitacora::create([
            'equipo_id' => $equipo->id,
            'ot' => '#'.str_pad($equipo->id, 3, '0', STR_PAD_LEFT),
            'tarea' => "Cambio de estado: {$old} -> {$equipo->estado}",
            'detalle' => $detalle,
        ]);

        $equipo->load('user');

        // preparar bitacora resumida para respuesta
        $bitacoraResp = [
            'fecha' => $bit->created_at ? $bit->created_at->format('d M, Y') : null,
            'ot' => $bit->ot,
            'tarea' => $bit->tarea,
            'detalle' => $bit->detalle,
        ];

        return response()->json(['message' => 'Estado actualizado', 'equipo' => $equipo, 'bitacora' => $bitacoraResp]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'nullable|string|max:100',
            'marca' => 'nullable|string|max:255',
            'serie' => 'nullable|string|max:255',
            'falla' => 'nullable|string',
            'telefono' => 'nullable|string|max:50',
        ]);

        $equipo = Equipo::create(array_merge($data, ['estado' => 'Asignado', 'user_id' => auth()->id(), 'responsable' => auth()->user()->name ?? null, 'cliente_id' => auth()->id()]));

        $bitacora = Bitacora::create([
            'equipo_id' => $equipo->id,
            'ot' => '#'.str_pad($equipo->id, 3, '0', STR_PAD_LEFT),
            'tarea' => 'Apertura de Mantenimiento ' . ($equipo->tipo ?? ''),
            'detalle' => $equipo->falla,
        ]);

        $equipo->load('bitacoras','user','cliente');

        return response()->json(['equipo' => $equipo, 'bitacoras' => $equipo->bitacoras], 201);
    }

    // Guardar comentario enviado desde UI
    public function storeComentario(Request $request, Equipo $equipo)
    {
        $user = $request->user();
        $data = $request->validate([
            'comentario' => 'required|string|max:2000',
        ]);

        $coment = \App\Models\Comentario::create([
            'equipo_id' => $equipo->id,
            'user_id' => $user ? $user->id : null,
            'comentario' => $data['comentario'],
        ]);

        $coment->load('user');

        return response()->json([
            'message' => 'Comentario guardado',
            'comentario' => [
                'id' => $coment->id,
                'texto' => $coment->comentario,
                'autor' => $coment->user ? $coment->user->name : ($user ? $user->name : 'Anónimo'),
                'created_at' => $coment->created_at->toDateTimeString(),
            ]
        ], 201);
    }
}
