@extends('layouts.backend')
@section('title','Estudiantes')
@section('parent-page','Escritorio')
@section('route-parent',route('home'))
@section('current-page','Estudiantes')

@section('js')
<script type="text/javascript" src="{{ asset('js/data-table/datatables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/components/student.js') }}"></script>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('css/data-table/datatables.min.css') }}">
@endsection

@section('content')
<div class="card">
    <div class="row">
        <div class="card-title col-6 mt-4 ml-4">
            <h3>Listado</h3>
        </div>
        <div class="col-5 mt-4 ml-4 text-right">
            <a href="{{ route('students.create') }}" class="btn btn-primary "><i class="i-Add"></i> Crear</a>             
        </div>
    </div>

    <div class="card-body"> 
        @if (session()->has('type') && session()->has('content'))
            <div class="alert alert-card alert-{{ session()->get('type') }}">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                {{ session()->get('content') }}
            </div>
        @endif
    	<table class="table table-hover table-responsive-lg" id="list-students">
    		<thead>
    			<tr>
                    <th>Clase</th>
                    <th>Nombre</th>
                    <th>Representante</th>
    				<th>Género</th>
    				<th>Edad</th>
                    <th>Estado</th>
    				<th>Acción</th>
    			</tr>
    		</thead>
    		<tbody>
    			@foreach ($students as $student)
        			<tr>
                        <td>
                            <div class="@if($student->currentEnrollment() && $student->currentEnrollment()->class_type == 2)  text-success @else text-warning @endif" >{{ $student->currentEnrollment() && $student->currentEnrollment()->class_type == 2 ? 'Pagada' : 'Demostrativa' }}</div>
                        </td>
                        <td><a href="{{ route('students.edit',['id' => $student->id]) }}" class="text-primary">{{$student->person->name .' '. $student->person->last_name}}</td>
                        <td>{{ $student->representant ? $student->representant->name .' '.  $student->representant->last_name : '-'}}</td>
        				<td>
                            {{ $student->person->genre =='m' ? 'Masculino' : 'Femenino' }}
                        </td>
                        <td>{{ $student->person->age }} Año(s)</td>
                        <td><div class="{{ $student->state == 1 ? 'text-success' : 'text-warning' }} ">{{ $student->state == 1 ? 'Activo' : 'Inactivo' }} </div></td>
        				<td>
        					<a class="btn btn-warning btn-flat " href="{{ route('students.edit',['id' => $student->id]) }}"><i class="i-Pen-2"></i> Editar</a>
        					<button class="btn btn-danger btn-flat  delete-btn" data-toggle="modal" data-target="#delete-modal"  data-object="{{$student}}" data-fieldname="{{$student->person->name}} {{$student->person->last_name}}" data-message="Está seguro de eliminar el Estudiante" data-route="{{ route('students.destroy',$student->id) }}"><i class="i-File-Trash"></i> Eliminar</button>
        				</td>
        			</tr>
    		  @endforeach
        		
    		</tbody>
    	</table>
    </div>
</div>
@endsection
