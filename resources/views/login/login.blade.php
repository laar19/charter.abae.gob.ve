@extends('layout')

@section('content')
        
        <div class="container">
            <div class="row">
                <div class="col-md-offset-4 col-md-4">
                    
                    @if(isset(Auth::user()->email))
                        {{ route('checklogin') }}
                    @endif

                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="form-login">
                        <form method="post" action="{{ route('checklogin') }}">
                            
                            {{ csrf_field() }}
                            
                            <center><h4>Ingresar al Sistema</h4></center>
                            <input name="email" type="email" id="email" class="form-control input-sm chat-input" placeholder="Correo electrónico"/>
                                
                            <input name="password" type="password" id="password" class="form-control input-sm chat-input" placeholder="Contraseña" required />
                            
                            <br>
                                
                            <div class="wrapper">
                                <span class="group-btn">     
                                    <center><input name="login" type="submit" value="Ingresar" class="btn btn-success btn-md"></center>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

@endsection