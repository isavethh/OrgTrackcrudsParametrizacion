<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class QRController extends Controller
{
    public function index()
    {
        $clientes = Usuario::whereNotNull('nombre')
            ->get();
            
        return view('qr.index', compact('clientes'));
    }

    public function enviosPorCliente(Request $request)
    {
        $validated = $request->validate([
            'id_cliente' => 'required|exists:usuarios,id'
        ]);

        $envios = Envio::with(['direccion', 'productos.tipoEmpaque', 'productos.unidadMedida', 'historialEstados.estadoEnvio'])
            ->where('id_usuario', $validated['id_cliente'])
            ->orderBy('fecha_creacion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'envios' => $envios
        ]);
    }

    public function generarCodigoQR(Request $request, $id)
    {
        $envio = Envio::findOrFail($id);

        // Generar código único si no existe
        if (!$envio->codigo_qr) {
            $envio->codigo_qr = 'ENV-' . strtoupper(Str::random(10));
            $envio->save();
        }

        return response()->json([
            'success' => true,
            'codigo' => $envio->codigo_qr,
            'message' => 'Código QR generado exitosamente'
        ]);
    }

    public function generarQR($id)
    {
        try {
            // Usar solo el ID para búsqueda rápida
            $envio = Envio::select('id', 'codigo_qr')->find($id);
            
            if (!$envio) {
                return response('Envío no encontrado', 404);
            }

            // Generar código único si no existe
            if (!$envio->codigo_qr) {
                $envio->codigo_qr = 'ENV-' . strtoupper(Str::random(10));
                $envio->save();
            }

            // URL que apuntará al documento del envío
            $url = url('/qr/documento/' . $envio->codigo_qr);

            // Crear QR usando SVG (no requiere GD extension)
            $qrCode = new QrCode($url);
            $writer = new SvgWriter();
            $result = $writer->write($qrCode);

            return response($result->getString())
                ->header('Content-Type', 'image/svg+xml')
                ->header('Cache-Control', 'public, max-age=3600')
                ->header('Access-Control-Allow-Origin', '*');
                
        } catch (\Exception $e) {
            Log::error('Error generando QR: ' . $e->getMessage());
            return response('Error generando QR: ' . $e->getMessage(), 500);
        }
    }

    public function leerQR()
    {
        return view('qr.leer');
    }

    public function documento($codigo)
    {
        $envio = Envio::with([
            'usuario',
            'direccion',
            'productos.tipoEmpaque',
            'productos.unidadMedida',
            'historialEstados.estadoEnvio'
        ])->where('codigo_qr', $codigo)->firstOrFail();

        // Calcular duración del viaje
        $duracionViaje = null;
        if ($envio->fecha_inicio_tracking && $envio->fecha_fin_tracking) {
            $duracionMinutos = $envio->fecha_inicio_tracking->diffInMinutes($envio->fecha_fin_tracking);
            $horas = floor($duracionMinutos / 60);
            $minutos = $duracionMinutos % 60;
            $duracionViaje = $horas > 0 ? "{$horas}h {$minutos}min" : "{$minutos}min";
        }

        $pdf = Pdf::loadView('qr.documento-pdf', compact('envio', 'duracionViaje'));
        
        return $pdf->stream('Envio-' . $envio->codigo_qr . '.pdf');
    }

    public function buscarPorCodigo(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string'
        ]);

        $envio = Envio::where('codigo_qr', $validated['codigo'])->first();

        if (!$envio) {
            return response()->json([
                'success' => false,
                'message' => 'Código QR no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'url' => route('qr.documento', ['codigo' => $envio->codigo_qr])
        ]);
    }
}
