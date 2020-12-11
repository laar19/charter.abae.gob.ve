@extends('layout')

@section('content')

    @include('menu')
        
        <div class="container">
            <center><h2>Administrar usuarios</h2></center>

            <div class="form-group">
                    <form action="">
                        <table>
                            <tr>
                                <th>
                                    <input type="text" name="q" placeholder="Buscar..." class="form-control">
                                </th>
                                <th>
                                    <input type="submit" class="btn btn-primary" value="Buscar">
                                </th>
                            </tr>
                            <tr>
                                <th>
                                    {{ $users->links() }}
                                </th>
                            </tr>
                        </table>
                    </form>
                </div>

            <div class="panel panel-primary">
                <div class="panel-heading">         
                    <h3 class="panel-title">Lista de usuarios</h3>
                    <div id="loader" class="text-center"></div>
                </div>
                
                <br>
                
                <div class="outer_div">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                            </tr>
                        </thead>                

                        <tbody class="buscar">
                            @foreach($users as $i)

                                <tr>
                                    <td>
                                        {{ $i->name }}
                                    </td>

                                    <td>
                                        {{ $i->email }}
                                    </td>

                                    <td>
                                        {{ $i->role }}
                                    </td>
                                    
                                    <td>
                                        <a href="{{ route('user.edit', $i->id) }}" class="btn btn-info btn-xs">
                                            Editar
                                        </a>

                                        <form style="display:inline" method="post" action="{{ route('user.destroy', $i->id) }}">
                                            {!! method_field('DELETE') !!}
                                            {!! csrf_field() !!}
                                            <button type="submit" class="btn btn-danger btn-xs">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div>
                {{ $users->links() }}
            </div>
        </div>

@endsection