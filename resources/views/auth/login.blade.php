@extends('layouts.auth')

@section('title','Ingreso')

@section('content')

<div class="col-md-8 py-5">
    <div class="p-4">
        <div class="auth-logo text-center mb-4">
            <img src="{{asset('assets/images/logo.png')}}" alt="">
        </div>
        <h1 class="mb-3 text-18">Ingreso</h1>
        <form class="login-form" action="{{ route('login') }}" method="POST">
            {{ csrf_field() }}
            <div class="form-group {{ $errors->has('email') ? ' is-invalid' : '' }}">
                <label for="email" class="text-uppercase">Email</label>
                <input type="text" class="form-control" id="email" placeholder="" name="email" value="{{ old('email') }}" autofocus="">
                @if ($errors->has('email'))
                    <span class="invalid-feedback animated fadeInDown">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group {{ $errors->has('password') ? ' is-invalid' : '' }}">
                <label for="password" class="text-uppercase">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control">
                @if ($errors->has('password'))
                    <span class="invalid-feedback animated fadeInDown">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-check">
                <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <small>Recordarme</small>
                </label>
                <button type="submit" class="btn btn-login float-right">
                    <span class="signingin hidden"><span class="voyager-refresh"></span> Ingresando...</span>
                    <span class="signin">Ingresar</span>
                </button>
          </div>
      </form>
    </div>
</div>
@endsection

@section('js')
<script>
    var btn = document.querySelector('button[type="submit"]');
    var form = document.forms[0];
    var email = document.querySelector('[name="email"]');
    var password = document.querySelector('[name="password"]');
    btn.addEventListener('click', function(ev){
        if (form.checkValidity()) {
            btn.querySelector('.signingin').className = 'signingin';
            btn.querySelector('.signin').className = 'signin hidden';
        } else {
            ev.preventDefault();
        }
    });
</script>
@endsection
