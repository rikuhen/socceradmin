@extends('layouts.backend')

@section('title', isset($field) ?  'Editar Cancha '. $field->name : 'Crear Cancha' )
@section('parent-page','Canchas')
@section('route-parent',route('fields.index') )

@section('content')

<!-- Container fluid  -->
<div class="container-fluid">
    <!-- Start Page Content -->
    <div class="row">
        <div class="col-12">
            <div class="card p-30">

            	<div class="row">
            		<div class="card-title col-12 px-0">
            			<h3>@if (isset($field)) {{  'Editar Cancha '. $field->name }} @else Crear Cancha @endif </h3>
            		</div>

            		<div class="card-body col-12">
		            	@if (session()->has('type') && session()->has('content'))
		            		<div class="alert alert-{{ session()->get('type') }} sufee-alert alert with-close alert-dismissible fade show">
		            			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
		            			{{ session()->get('content') }}
		            		</div>
		            	@endif

		            	<div class="form-validation">
			            	<form action="@if(isset($field)) {{ route('fields.update',['id'=>$field->id]) }} @else {{ route('fields.store') }} @endif" method="POST" class="crud-futbol">
			            		{{ csrf_field() }}
			            		@if (isset($field))
			            			<input type="hidden" name="_method" value="PUT">
			            			<input type="hidden" name="key" value="{{ $field->id }}">
			            		@endif
				                <div class="card-body">
				                	<div class="form-body">
					                	<div class="row">

					                		<div class="col-lg-3 col-6">
						                		<div class="form-group @if ($errors->has('name')) is-invalid @endif">
						                			<label for="name">Nombre <span class="text-danger">*</span></label>
						                			<input type="text" name="name" id="name" class="form-control"  autofocus="" value="@if(isset($field)){{ $field->name }}@else {{ old('name') }}@endif">
						                			@if ($errors->has('name'))
						                				<div id="val-username-error" class="invalid-feedback animated fadeInDown">{{ $errors->first('name') }}</div>
						                			@endif
						                		</div>
					                		</div>
					                		
					                		<div class="col-lg-7 col-6">
						                		<div class="form-group @if ($errors->has('address')) is-invalid @endif">
						                			<label for="address">Dirección <span class="text-danger">*</span></label>
						                			<input type="text" name="address" id="address" class="form-control"  autofocus="" value="@if(isset($field)){{ $field->address }}@else {{ old('address') }}@endif">
						                			@if ($errors->has('address'))
						                				<div id="val-username-error" class="invalid-feedback animated fadeInDown">{{ $errors->first('address') }}</div>
						                			@endif
						                		</div>
					                		</div>
					                		
					                		<div class="col-lg-2 col-6">
						                		<div class="form-group @if ($errors->has('type')) is-invalid @endif">
						                			<label for="type">Tipo <span class="text-danger">*</span></label>
						                			<select name="type" id="type" class="form-control custom-select">
						                				<option value="synthetic" @if( (isset($field) && $field->state == 'synthetic') || ( old('type') == 'synthetic' ) ) selected @endif>Sintética</option>
						                				<option value="escuela" @if( (isset($field) && $field->state == 'escuela') || ( old('type') == 'escuela' ) ) selected @endif>Escuela</option>
						                			</select>
						                			@if ($errors->has('type'))
						                				<div id="val-state-error" class="invalid-feedback animated fadeInDown">{{ $errors->first('type') }}</div>
						                			@endif
						                		</div>
					                		</div>
					                	</div>

					                	<div class="row">
											<div class="col-12">
												<label for="">Disponibilidad</label>
											</div>
										</div>
										<div class="row justify-content-center">
											<div class="col-lg-6 col-12">
												<ul class="list-group">
													<li class="list-group-item">
														<div class="form-check">
															<input class="form-check-input" type="checkbox" value="" id="lunes">
															<label class="form-check-label" for="lunes">
																Lunes
															</label>
														</div>
													</li>
												</ul>	
											</div>
										</div>
				                	</div>
				                	<hr>
				                	<div class="form-actions">
				                		<input type="hidden" value="0" name="redirect-index" id="redirect-index">
				                		<button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-save"></i> Guardar</button>
				                		<button class="btn btn-success btn-sm save-close" type="submit"><i class="fa fa-save"></i> Guardar y Cerrar</button>
				                		<a class="btn btn-inverse btn-sm" href="{{ route('fields.index') }}"><i class="fa fa-ban"></i> Cancelar</a>
				                	</div>
				                </div>
			            	</form>
		            		
		            	</div>
            		</div>
            	</div>
            </div>
        </div>
    </div>
    <!-- End PAge Content -->
</div>
@endsection