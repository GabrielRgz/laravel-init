@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Home')

@section('content')
<h4>Home Page</h4>
<h4>Cambios desde local</h4>
@role('admin')
<p>Solo admin</p>
@endrole
@role('writer')
<p>Solo escritor</p>
@endrole
@endsection
