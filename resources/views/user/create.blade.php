@extends('layout')

@section('content')

    @include('menu')
        
        <center>            
            <div class="container">
                <div class="row">
                    <div class="col-md-offset-4 col-md-5">
                        <div class="form-login">
                            <form method="post" action="{{ route('user.store') }}">
                                
                                {!! csrf_field() !!}
                                
                                <table>
                                    <div class="form-group">
                                        <tr>
                                            <td>
                                                <label>Nombre</label>
                                            </td>
                                            <td>
                                                <input type="text" name="name" required/>
                                            </td>
                                        </tr>
                                    </div>
                                    
                                    <div class="form-group">
                                        <tr>
                                            <td>
                                                <label>Correo</label>
                                            </td>
                                            <td>
                                                <input type="email" name="email" required/>
                                            </td>
                                        </tr>
                                    </div>

                                    <div class="form-group">
                                        <tr>
                                            <td>
                                                <label>Contraseña</label>
                                            </td>
                                            <td>
                                                <input type="password" id="password" name="password" required/>
                                            </td>
                                        </tr>
                                    </div>

                                    <div class="form-group">
                                        <tr>
                                            <td>
                                                <label>Confirme la contraseña</label>
                                            </td>
                                            <td>
                                                <input type="password" id="repetir_password" required/>

                                                <span id="resultado_password" class="help-block"></span>
                                            </td>
                                        </tr>
                                    </div>

                                    <div class="form-group">
                                        <tr>
                                            <td>
                                                <label for="role">Rol</label>
                                            </td>
                                            <td>
                                                <select id="role" name="role" required>
                                                    <option value="usuario" selected>Usuario</option>
                                                    <option value="admin">Administrador</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </div>
                                </table><br>
                                <input class="btn btn-info btn-md" id="crear_usuario" type="submit" value="Aceptar"/>
                                
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @elseif(session()->has('error'))
                                    <div class="alert alert-danger">
                                        {{ session()->get('error') }}
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </center>

        <!-- jQuery -->
        <script src="{{ asset('/assets/js/jquery/jquery-3.2.1.min.js') }}"></script>

        <script src="{{ asset('/assets/js/jquery/verify_password.js') }}"></script>
        
@endsection
