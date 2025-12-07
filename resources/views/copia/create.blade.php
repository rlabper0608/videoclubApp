@extends('bootstrap.template')

@section('title')
Añadir una Copia
@endsection

@section('styles')
<link rel="stylesheet" href="{{ url('assets/css/copia/createStyle.css') }}">
@endsection

@section('content')
<form action="{{ route('copia.store') }}" method="post" enctype="multipart/form-data"> 
    @csrf
    <div class="espacio">
        @error('idpelicula')
        <div class="alert alert-danger">
            {{ $message }}
        </div>
        @enderror
        <label for="idpelicula">Copia de la película:</label>
        <select name="idpelicula" id="idpelicula" required class="form-control">
            <option value="" @if(old('idpelicula') == null) selected @endif disabled>Selecciona una pelicula</option>
            @foreach($peliculas as $indice=>$idpelicula)
                <option value="{{ $indice }}" @if(old('idpelicula') == $indice) selected @endif>{{ $idpelicula }}</option>
            @endforeach
         </select>
    </div>
    <div class="espacio">
        @error('codigo_barras')
        <div class="alert alert-danger">
            {{ $message }}
        </div>
        @enderror
        <label for="codigo_barras">Código de barras:</label>
        <input class="form-control" required id="codigo_barras" name="codigo_barras" minlength="10" maxlength="10" value="{{ old('codigo_barras') }}" type="text">
    </div>
    <div class="espacio">
        @error('estado')
        <div class="alert alert-danger">
            {{ $message }}
        </div>
        @enderror
        <label for="estado">Estado de la copia:</label>
        <input class="form-control" required id="estado" name="estado" minlength="3" maxlength="60" value="{{ old('estado') }}" type="text">
    </div>
    <div class="espacio">
        @error('formato')
        <div class="alert alert-danger">
            {{ $message }}
        </div>
        @enderror
        <label for="formato">Formato de la copia:</label>
        <input class="form-control" required id="formato" name="formato" minlength="3" maxlength="60" value="{{ old('formato') }}" type="text">
    </div>
    
    <div class="espacio">
        <input class="btn btn-primary" value="Añadir Copia" type="submit">
    </div>
</form>
@endsection