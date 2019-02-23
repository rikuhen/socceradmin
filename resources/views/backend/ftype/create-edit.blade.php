@extends('layouts.backend')
@section('title', isset($ftype) ?  'Editar Tipo de Cancha '. $ftype->name : 'Crear Tipo de Cancha' )
@section('parent-page','Tipos de Cancha')
@section('route-parent',route('ftypes.index') )

@section('content')
<!-- Container fluid  -->
<div class="container-fluid">
    <!-- Start Page Content -->
    <div class="row">
        <div class="col-12">
			

			<ul class="nav nav-tabs customtab mb-2">
                <li class="nav-item">
                    <a class="nav-link" id="field-tab" href="{{route('fields.index')}}">Canchas</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link active"  id="range-age-tab"  data-toggle="tab" href="#ageranges" role="tab" aria-controls="ageranges" aria-selected="true"> Tipos de Cancha</a>
                </li>


                <li class="nav-item">
                    <a class="nav-link" id="range-age-tab" href="{{route('ageranges.index')}}">Rango de Edades</a>
                </li>
            </ul>

            <div class="card p-30">
            	<div class="row">
            		<div class="card-title col-12 px-0">
            			<h3>@if (isset($ftype)) {{  'Editar Tipo de '. $ftype->name }} @else Crear Tipo de Cancha @endif </h3>
            		</div>

            		<div class="card-body col-12">
            			@if (session()->has('type') && session()->has('content'))
		            		<div class="alert alert-{{ session()->get('type') }} sufee-alert alert with-close alert-dismissible fade show">
		            			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
		            			{{ session()->get('content') }}
		            		</div>
            			@endif

            			<div class="form-validation">
			            	<form action="@if(isset($ftype)) {{ route('ftypes.update',['id'=>$ftype->id]) }} @else {{ route('ftypes.store') }} @endif" method="POST" class="crud-futbol">
			            		{{ csrf_field() }}
			            		@if (isset($ftype))
			            			<input type="hidden" name="_method" value="PUT">
			            			<input type="hidden" name="key" value="{{ $ftype->id }}">
			            		@endif
			                	<div class="row">
			                		<div class="col-lg-4 col-6">
				                		<div class="form-group {{ $errors->has('name') ? ' is-invalid' : '' }}">
				                			<label for="name">Nombre <span class="text-danger">*</span></label>
				                			<input type="text" name="name" id="name" class="form-control"  autofocus="" value="@if(isset($ftype)){{ $ftype->name }}@else{{ old('name') }}@endif">
				                			@if ($errors->has('name'))
				                				<div class="invalid-feedback animated fadeInDown">{{ $errors->first('name') }}</div>
				                			@endif
				                		</div>
			                		</div>
			                	</div>
            			</div>
            		</div>
            		<div class="card-footer col-12">
    			    	<div class="form-actions">
    			    		<input type="hidden" value="0" name="redirect-index" id="redirect-index">
    			    		<button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-save"></i> Guardar</button>
    			    		<button class="btn btn-secondary btn-sm save-close" type="submit"><i class="fa fa-save"></i> Guardar y Cerrar</button>
    			    		<a class="btn btn-inverse btn-sm" href="{{ route('ftypes.index') }}"><i class="fa fa-ban"></i> Cancelar</a>
    			    	</div>
            			</form>	
            		</div>
            	</div>
            </div>
        </div>
    </div>
</div>
@endsection