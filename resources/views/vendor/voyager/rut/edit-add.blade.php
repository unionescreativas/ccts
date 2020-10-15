@extends('voyager::master')

@section('content')
<div class="page-content container-fluid">
    @include('voyager::alerts')
    <div class="row">
        <div class="col-md-12">

            <div class="admin-section-title">
                <h3><i class="voyager-upload"></i> Cargar Documento Rut</h3>
            </div>
            <div class="clear"></div>
{{-- <h1>Cargar Documento Rut</h1> --}}
<div id="app">
    <example-component></example-component>
</div>
        </div>
    </div>
</div>
@endsection
