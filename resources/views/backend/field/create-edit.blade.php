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
						
						@if(isset($field))
						<ul class="nav nav-tabs customtab">
							<li class="nav-item">
								<a class="nav-link active" id="field-tab" data-toggle="tab" href="#field" role="tab" aria-controls="field" aria-selected="true">Cancha</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="groups-tab" data-toggle="tab" href="#groups" role="tab" aria-controls="groups" aria-selected="true">Grupos</a>
							</li>
						</ul>
						{{-- tab field --}}
						<div class="tab-content" id="mytabcontent">
							<div class="tab-pane fade show active" id="field" role="tabpanel" aria-labelledby="field-tab">
						@endif
							{{-- content field --}}
							<div class="form-validation @if(isset($field)) p-2 @endif">
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
													<div class="form-group @if ($errors->has('available_days')) is-invalid @endif">
														<label for="">Horario <span class="text-danger">*</span></label>
														
														@if ($errors->has('available_days'))
															<div id="val-state-error" class="invalid-feedback animated fadeInDown">{{ $errors->first('available_days') }}</div>
														@endif
													</div>
												</div>
											</div>
											<div class="row justify-content-center">
												<div class="col-lg-6 col-12">
													<ul class="list-group">
														<li class="list-group-item">
															<table class="table" id="shcedule-field">
																<thead>
																	<tr>
																		<td class="text-center"><b>	Día</b></td>
																		<td class="text-center"><b>Horario de Atención</b></td>
																	</tr>
																</thead>
																<tbody>
																	@php $countDay = 0; @endphp
																	@foreach ($daysOfWeek as $kday => $day)
																		<tr id="row-{{$kday}}">
																			<td>
																				<div class="form-check">
																					<input class="form-check-input select-day" type="checkbox" value="" id="{{$kday}}" @if( (isset($field) && is_array($field->available_days) && array_key_exists($kday,$field->available_days) ) || (old('available_days') != null && array_key_exists($kday,old('available_days') ) ) ) checked @endif>
																					<label class="form-check-label text-secondary" for="{{$kday}}">
																						{{$day}}
																					</label>
																				</div>
																			</td>
																			<td>
																				
																				@if ( isset($field) && is_array($field->available_days) && array_key_exists($kday, $field->available_days)  )
																					@php $numSchedule = 0; @endphp
																					
																					@foreach($field->available_days[$kday] as  $shcheduleNum => $scheduleDetail)
																						<div class="row">
																							@foreach ($scheduleDetail as $keyAction => $hours)
																								<div class="col-5 form-group @if ($errors->has('available_days.'.$kday.'.'.$shcheduleNum.'.'.$keyAction)) is-invalid @endif">
																									<input class="form-control form-control-sm  @if($keyAction == 'start') start-hour @else end-hour @endif" type="time" name="available_days[{{$kday}}][{{$shcheduleNum}}][{{$keyAction}}]" id="" value="{{$hours}}">
																									@if ($errors->has('available_days.'.$kday.'.'.$shcheduleNum.'.'.$keyAction))
																										<div id="val-state-error" class="invalid-feedback animated fadeInDown">{{ $errors->first('available_days.'.$kday.'.'.$shcheduleNum.'.'.$keyAction) }}</div>
																									@endif
																								</div>
																							@endforeach
																							
																							@if($numSchedule < 1)
																								<div class="form-group">
																									<button type="button" class="btn btn-link btn-sm add-schedule"><i class="fa fa-plus"></i></button>
																								</div>
																							@else
																								<div class="form-group">
																									<button type="button" class="btn btn-link btn-sm remove-schedule"><i class="fa fa-close"></i></button>
																								</div>
																							@endif
																							@php $numSchedule++; @endphp 
																						</div>
																					@endforeach

																				@elseif(old('available_days') != null && array_key_exists($kday,old('available_days')))
																					@php $numSchedule = 0; @endphp
																					@foreach (old('available_days')[$kday] as $sheduleNum => $scheduleDetail)
																						<div class="row">
																							@foreach ($scheduleDetail as $keyAction => $hours)
																								<div class="col-5 form-group @if ($errors->has('available_days.'.$kday.'.'.$sheduleNum.'.'.$keyAction)) is-invalid @endif">
																								<input class="form-control form-control-sm  @if($keyAction == 'start') start-hour @else end-hour @endif" type="time" name="available_days[{{$kday}}][{{$sheduleNum}}][{{$keyAction}}]" id="" value="{{$hours}}">
																									@if ($errors->has('available_days.'.$kday.'.'.$sheduleNum.'.'.$keyAction))
																										<div id="val-state-error" class="invalid-feedback animated fadeInDown">{{ $errors->first('available_days.'.$kday.'.'.$sheduleNum.'.'.$keyAction) }}</div>
																									@endif
																								</div>
																							@endforeach
																							@if($numSchedule < 1)
																								<div class="form-group">
																									<button type="button" class="btn btn-link btn-sm add-schedule"><i class="fa fa-plus"></i></button>
																								</div>
																							@else
																								<div class="form-group">
																									<button type="button" class="btn btn-link btn-sm remove-schedule"><i class="fa fa-close"></i></button>
																								</div>
																							@endif
																							@php $numSchedule++; @endphp
																						</div>
																					@endforeach
																				@else
																					<div class="row">			
																						<div class="col-5 form-group @if ($errors->has('available_days.'.$kday.'.schedule_0.start')) is-invalid @endif">
																							<input class="form-control form-control-sm start-hour" type="time" name="available_days[{{$kday}}][schedule_0][start]" id="" @if( isset($field) && isset($field->available_days[$kday]) && isset($field->available_days[$kday]['start']) ) value="{{$field->available_days[$kday]['start'][$countDay]}}"  @endif disabled>
																							@if ($errors->has('available_days.'.$kday.'.schedule_0.start'))
																								<div id="val-state-error" class="invalid-feedback animated fadeInDown">{{ $errors->first('available_days.'.$kday.'.schedule_0.start') }}</div>
																							@endif
																						</div>
																						
																						<div class="col-5 form-group @if ($errors->has('available_days.'.$kday.'.schedule_0.end')) is-invalid @endif">
																							<input type="time" class="form-control form-control-sm end-hour" name="available_days[{{$kday}}][schedule_0][end]" id="" disabled>
																							@if ($errors->has('available_days.'.$kday.'.schedule_0.end'))
																								<div id="val-state-error" class="invalid-feedback animated fadeInDown">{{ $errors->first('available_days.'.$kday.'.schedule_0.end') }}</div>
																							@endif
																						</div>
																						<div class="form-group">
																							<button type="button" class="btn btn-link btn-sm add-schedule" disabled><i class="fa fa-plus"></i></button>
																						</div>
																					</div>
																				@endif
																					
																			</td>
																		</tr>
																		@php $countDay++; @endphp
																	@endforeach
																</tbody>
															</table>
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
								@if(!isset($field))
								</form>
								@endif
								
							</div>
						@if(isset($field))
							</div>
							<div class="tab-pane fade" id="groups" role="tabpanel" aria-labelledby="groups-tab">
								
							</div>
						</div>
						</form>
						@endif
						
						
            		</div>
            	</div>
            </div>
        </div>
    </div>
    <!-- End PAge Content -->
</div>
@endsection

@section('js')
<script src="{{asset('js/components/field.js')}}" type="text/javascript"></script>
@endsection()