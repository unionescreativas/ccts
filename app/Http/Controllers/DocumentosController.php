<?php

namespace App\Http\Controllers;

use App\Documento;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
// use jeremykenedy\LaravelLogger\App\Http\Traits\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DocumentosController extends Controller {

    protected $configModelo;
    protected $modulo;

    public function __construct() {
        // Variables Globales---------------------------->
        $this->configModelo = new Documento;
        $this->modulo = "Documentos";
        // Variables Globales---------------------------->

    }

    public function index(Request $request) {
        $documento = Auth::user()->documento;
        $modulo_id = $request->input('modulo_id');
        $modulo = $request->input('modulo');
        $variableConsulta = $this->configModelo::where('modulo_id', $documento)->get();
        // ActivityLogger::activity("Consulto datos del modulo {$this->modulo},Parametros: Cantidad de registros: -> Metodo Index");
        return ['data' => $variableConsulta, 'status' => '201'];
    }
    public function show($id) {
        $variableConsulta = $this->configModelo::where('id', $id)->where('estado', '1')->get();
        return ['data' => $variableConsulta, 'status' => '201'];
    }
    public function store(Request $request) {

        // ------------------------>Datos Ruta
        $modulo = "rut";
        // $moduloId = auth()->user()->id;
        $ruta = "/documentos/{$modulo}/";
        $documento = Auth::user()->documento;
        $id = Auth::id();
        DB::table('documentos')->where('modulo_id', '=', $documento)->delete();
        // return ['data' => $id, 'status' => '202'];
        // ------------------------>Datos Ruta
        $data = [];
        $variableConsulta = $this->configModelo;
        $variableConsulta->usuario_creacion = auth()->user();
        foreach ($request->file as $key => $value) {
            // // ------------------------>Datos Archivo
            $extensionArchivo = $request->file[$key]->getClientOriginalExtension();
            $nombreArchivo = $documento . "." . $extensionArchivo;
            $nombre_carga = $request->file[$key]->getClientOriginalName();
            $tamañoArchivo = $request->file[$key]->getSize();
            $aplicacionArchivo = $request->file[$key]->getMimeType();
            $ruta_carga = $request->file[$key]->getRealPath();
            $ruta_descarga = "{$ruta}{$nombreArchivo}";
            // ------------------------>Datos Archivo
            // Mover Archivo--------------->
            $path = $request->file[$key]->storeAs("public" . $ruta, $nombreArchivo);
            // $request->file[$key]->move(public_path($ruta), $nombreArchivo . "." . $extensionArchivo);
            //Mover Archivo--------------->
            $data[$key] = $variableConsulta::create([
                'modulo_id' => $documento,
                'modulo' => $modulo,
                'nombre_archivo' => $nombreArchivo,
                'nombre_carga' => $nombre_carga,
                'url' => $ruta,
                'url_descarga' => $ruta_descarga,
                'extension' => $extensionArchivo,
                'tamano' => $tamañoArchivo,
                'aplicacion' => $aplicacionArchivo,
                'ruta_carga' => $ruta_carga,
                'usuario_creacion' => $id,
                'usuario_actualizacion' => $id,

            ]);
            //Campos a guardar aquí--------------->
        }
        // ActivityLogger::activity("Guardando datos del modulo {$this->modulo}, Datos Guardaros:{$variableConsulta}, -> Metodo Store.");
        return ['data' => $data, 'status' => '202'];
    }
    public function update(Request $request, $id) {
        //
        $datosAnteriores = $this->configModelo::find($id);
        $variableConsulta = $this->configModelo::find($id);

        //Campos Actualizar aquí--------------->
        $variableConsulta->nombre_carga = $request->nombre_carga;
        $variableConsulta->usuario_actualizacion = $request->user()->id;
        //Campos Actualizar aquí--------------->

        $variableConsulta->save();
        // ActivityLogger::activity("Actualizando datos del modulo {$this->modulo},  Datos Anteriores:{$datosAnteriores}  Datos Nuevos:{$variableConsulta}, para el registro id {$id} ->Metodo Update.");
        return ['data' => $variableConsulta, 'status' => '203'];
    }
    public function destroy($id) {
        $variableConsulta = $this->configModelo::find($id);
        $datosElimnados = $variableConsulta;
        $variableConsulta->usuario_actualizacion = $request->user()->id;
        $variableConsulta = $this->configModelo::destroy($id);
        // ActivityLogger::activity("Eliminado Registo Modulo {$this->modulo},Datos eliminados:{$datosElimnados},  para el registro {$id} -> Metodo destroy");
        return ['data' => $variableConsulta, 'status' => '204'];
    }

    public function activar($id) {
        $variableConsulta = $this->configModelo::find($id);
        $variableConsulta->usuario_actualizacion = $request->user()->id;
        $datosActivar = $variableConsulta;

        // ActivityLogger::activity("Activando Registo Modulo {$this->modulo},Datos Activar: {$datosActivar}, para el registro {$id} -> Metodo Activar.");
        $variableConsulta->estado = 1;
        $variableConsulta->save();
        return ['data' => $variableConsulta, 'status' => '205'];
    }

    public function inactivar($id) {
        $variableConsulta = $this->configModelo::find($id);
        $variableConsulta->usuario_actualizacion = $request->user()->id;
        $datosActivar = $variableConsulta;
        // ActivityLogger::activity("Inactivando Registo Modulo {$this->modulo},Datos Inactivar: {$datosActivar}, para el registro {$id} -> Metodo Inactivar.");
        $variableConsulta->estado = 0;
        $variableConsulta->save();
        return ['data' => $variableConsulta, 'status' => '206'];
    }
    public function restore($id) {
        $variableConsulta = $this->configModelo::withTrashed()->find($id);
        $datosRestaurar = $variableConsulta;
        // ActivityLogger::activity("Restaurando Registo Modulo {$this->modulo},Datos a Restaurar: {$datosRestaurar}, para el registro {$id} -> Metodo Restaurar.");
        $variableConsulta->restore();
        return ['data' => $variableConsulta, 'status' => '207'];
    }

    public function cors() {
        if (!$this->corsEnabled) {
            return;
        }
        header('Access-Control-Allow-Origin: *'); // Enable CORS
        header('Access-Control-Allow-Methods: POST, PUT, DELETE, OPTIONS'); // Allow CORS methods
        header('Access-Control-Allow-Headers: accept, content-type, x-test-header'); // Allow CORS methods

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit;
        }
    }

    public function handle() {
        $this->cors();
        $response = $this->handleRequest();
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
