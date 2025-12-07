@extends('bootstrap.template')

@section('title')
Listado de Peliculas Alquiladas 
@endsection

@section('styles')
<link rel="stylesheet" href="{{ url('assets/css/copia/indexStyle.css') }}">
@endsection

@section('content')

<!-- Ventanas Modales principio -->

<div class="modal fade" id="destroyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">¿Seguro que quieres eliminar esta copia?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="destroyModalContent">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button  form="form-delete" type="submit" class="btn btn-danger">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- Ventanas modales fin -->

<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
<h2 style="color: var(--rent-accent); margin-bottom: 0;">Lista de Copias ({{ $copias->count() }})</h2>
@auth
  @if(Auth::user()->hasVerifiedEmail())
    <a href="{{ route('copia.create') }}" class="btn btn-primary"
    style="background-color: var(--rent-accent, #8b5cf6); border-color: var(--rent-accent, #8b5cf6); font-weight: 600;">
    <i class="fas fa-plus-circle"></i> Nueva Copia
    </a>
  @endif
@endauth
</div>

<hr>

<table class="table table-hover">
  <thead>
    <tr>
        <th>#</th>
        <th>Pelicula</th>
        <th>Codigo de barras</th>
        <th>Estado</th>
        <th>Formato</th>
    </tr>
  </thead>
  <tbody>
    @foreach($copias as $copia)
        <tr>
            <td>{{ $copia->id }}</td>
            <td>{{ $copia->pelicula->titulo }}</td>
            <td>{{ $copia->codigo_barras}}</td>
            <td>{{ $copia->estado }}</td>
            <td>{{ $copia->formato }}</td>
            <td>
                <a href=" {{ route('copia.edit', $copia->id) }}" class="btn btn-warning btn-sm text-white">Edit</a>
                <a class="link-destroy btn btn-danger btn-sm text-white" 
                  data-bs-toggle="modal"
                  data-bs-target="#destroyModal"
                  data-href="{{ route('copia.destroy', $copia)}}" 
                  data-alumno="{{ $copia->id }}">Delete</a>
            </td>
        </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
        <th colspan="3">Número de copias registradas:</th>
        <th>{{ count($copias) }}</th>
    </tr>
  </tfoot>
</table>

<form action="" method="post" id="form-delete">
    @csrf
    @method('delete')
</form>
@endsection