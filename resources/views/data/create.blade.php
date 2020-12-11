@extends('layout')

@section('content')

    @include('menu')

        <center>            
            <div class="container">
                <div class="row">
                    <div class="col-md-offset-4 col-md-5">
                        <div class="form-login">
                            <form method="post" action="{{ route('data.store') }}" enctype="multipart/form-data">
                                
                                {!! csrf_field() !!}
                                
                                <table>
                                    <h3>Todos los campos son requeridos</h3>
                                    <br>
                                    <tr>
                                        <td>
                                            <label> Seleccione el archivo .XML </label>
                                            <input type="file" name="xml_file" required="required" accept=".xml"/><br>
                                            {!! $errors->first('xml_file', '<span class="error">:message</span>') !!}
                                            <!--
                                            <input type="file" name="archivo_xml[]" required="required" accept=".xml" multiple/><br><br>
                                            -->
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td>
                                            <label> Seleccione el icon </label>
                                            <input type="file" name="icon" required="required" accept=".jpg, .jpeg"/><br>
                                            {!! $errors->first('icon', '<span class="error">:message</span>') !!}
                                            <!--
                                            <input type="file" name="icons[]" required="required" accept=".jpg, .jpeg" multiple/><br><br>
                                            -->
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td>
                                            <label> Seleccione la imagen preview </label>
                                            <input type="file" name="preview" required="required" accept=".jpg, .jpeg"/><br>
                                            {!! $errors->first('preview', '<span class="error">:message</span>') !!}
                                            <!--
                                            <input type="file" name="imagen_preview[]" required="required" accept=".jpg, .jpeg" multiple/><br><br>
                                            -->
                                        </td>
                                    </tr>
                                </table>
                                <input  class="btn btn-info btn-md" type="submit" value="Subir Archivos"/>
                                
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
@endsection