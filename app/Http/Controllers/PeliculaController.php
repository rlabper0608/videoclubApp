<?php

namespace App\Http\Controllers;


// Importamos el modelo y clases necesarias
use App\Models\Pelicula; 
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PeliculaCreateRequest; 
// use App\Http\Requests\Rule;


class PeliculaController extends Controller {
    
    function index():View{
        // Obtenemos todas las películas para visualizarlas
        $peliculas = Pelicula::all(); 
        return view('pelicula.index', ['peliculas' => $peliculas]);
    }

    
    function create():View{
        // Devolvemos la vista create con el formulario
        return view('pelicula.create');
    }

    
    function store(PeliculaCreateRequest $request):RedirectResponse{

        // Validando clave únnica compuesta

        // $rules = [
        //     'titulo' => [
        //          Rule::unique('pelicula')
        //              ->where(function ($query)use ($request) {
        //                  return $query->where('director', $request->titulo);
        //              }),
        //     ],
        // ];

        // $validator = Validator::make($request->all(), $rules. []);
        // if($validator->fails()) {
        //     $messages = [
        //          'titulo' => 'clave unica violada',
        //          'director' => 'clave unica violada'
        //      ];
        //      return back()->withInputs()->withErrors($messages);
        // }
        
        // Creamos un nuevo objeto Pelicula con los datos del request
        $pelicula = new Pelicula($request->all());
        $result = false;
        $txtmessage = "";

        // Intentamos guardar la película
        try {
            $result = $pelicula->save(); 
            $txtmessage = "La película se ha añadido correctamente.";

            // Si me llega la portada, la subo y la guardo
            if($request->hasFile('portada')) {
                $ruta = $this->uploadPortada($request, $pelicula);
                $pelicula->portada = $ruta;
                $pelicula->save();
            }
            
        } catch(UniqueConstraintViolationException $e){
            // Error por clave duplicada
            $txtmessage = "Clave duplicada: Ya existe una película con esa información.";
        } catch(QueryException $e){
            $txtmessage = "Error en la base de datos: Valor nulo o incorrecto.";
        }catch (\Exception $e){
            // Cualquier error no capturado
            $txtmessage = "Error Fatal al guardar la película";
        }

        $message = [
            "mensajeTexto" => $txtmessage,
        ];

        // Redirigimos con el mensaje
        if($result){
            return redirect()->route('main')->with($message);
        }else{
            return back()->withInput()->withErrors($message);
        }
    }

    private function uploadPortada(Request $request, Pelicula $pelicula):string {

        $portada = $request->file('portada');
        // Usamos el ID de la película como nombre de archivo para evitar duplicados
        $name = $pelicula->id . "." . $portada->getClientOriginalExtension();

        // Guardamos en la carpeta 'portadas'
        $ruta = $portada->storeAs('portadas', $name, 'public');

        return $ruta;
    }


    
    function show(Pelicula $pelicula):View{
        return view('pelicula.show', ['pelicula' => $pelicula]);
    }

    function edit(Pelicula $pelicula):View{
        return view('pelicula.edit', ['pelicula' => $pelicula]);
    }

    function update(Request $request, Pelicula $pelicula): RedirectResponse{

        // Validando clave únnica compuesta

        // $rules = [
        //     'titulo' => [
        //          Rule::unique('pelicula')
        //              ->where(function ($query)use ($request) {
        //                  return $query->where('director', $request->titulo);
        //              }),
        //     ],
        // ];

        // $validator = Validator::make($request->all(), $rules. []);
        // if($validator->fails()) {
        //     $messages = [
        //          'titulo' => 'clave unica violada',
        //          'director' => 'clave unica violada'
        //      ];
        //      return back()->withInputs()->withErrors($messages);
        // }

        // Lógica para eliminar portada existente (vía checkbox)
        if($request->deleteImage == 'true' && $pelicula->portada) {
            // Borrado del archivo de portada
            Storage::delete($pelicula->portada);
            
            // La ponemos como nula en la base de datos
            $pelicula->portada = null;
        }

        $result = false;
        $pelicula->fill($request->all());
        $txtmessage = "";

        // Intentamos actualizar y manejar archivos
        try {
            // Subir nueva portada si se proporciona
            if($request->hasFile('portada')) {
                // 1. CORREGIDO: Eliminar la portada antigua SI EXISTE y NO SE PIDIÓ BORRAR POR CHECKBOX
                // Nota: La portada se elimina aquí SOLO si se está subiendo un reemplazo.
                if ($pelicula->portada) {
                    Storage::delete($pelicula->portada);
                }
                
                // 2. Subir la nueva portada y actualizar el registro
                $ruta = $this->uploadPortada($request, $pelicula);
                $pelicula->portada = $ruta;
            }

            $result = $pelicula->save();
            $txtmessage = "La película se ha actualizado correctamente.";
        } catch(UniqueConstraintViolationException $e) {
            $txtmessage = "Clave duplicada: Ya existe una película con esa información.";
        } catch(QueryException $e) {
            $txtmessage = "Error en la base de datos: Valor nulo o incorrecto.";
        }catch (\Exception $e) {
            $txtmessage = "Error fatal al actualizar la película.";
        }

        $message = [
            "mensajeTexto" => $txtmessage,
        ];

        if($result){
            return redirect()->route('main')->with($message);
        }else{
            return back()->withInput()->withErrors($message);
        }
    }

    function destroy(Pelicula $pelicula): RedirectResponse {
        try {
            // 1. Eliminar la portada del almacenamiento
            if ($pelicula->portada) {
                Storage::delete($pelicula->portada);
            }

            // 2. Eliminar el registro de la película (la DB gestiona las copias si se usa CASCADE)
            $result = $pelicula->delete();
            $textmessage = 'La película se ha eliminado.';

        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar excepción de la base de datos (generalmente por clave foránea si no usa CASCADE)
            $result = false;
            $textmessage = 'Error: Esta película no puede eliminarse porque tiene copias asociadas.';
        } catch (\Exception $e) {
            $result = false;
            $textmessage = 'Error fatal al eliminar la película.';
        }

        $message = [
            'mensajeTexto' => $textmessage,
        ];
        
        if($result){
                return redirect()->route('main')->with($message);
            } else {
                return back()->withInput()->withErrors($message);
            }
    }
}