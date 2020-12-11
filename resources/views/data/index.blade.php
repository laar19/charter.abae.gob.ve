@extends('layout')

@section('content')

    @include('menu')
        
        <div class="container">
            <center><h2>Descargar datos</h2></center>

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
                                    {{ $datas->links() }}
                                </th>
                            </tr>
                        </table>
                    </form>
                </div>

            <div class="panel panel-primary">
                <div class="panel-heading">         
                    <h3 class="panel-title">Lista de archivos cargados</h3>
                    <div id="loader" class="text-center"></div>
                </div>
                    
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre del paquete</th>
                                <th>XML original</th>
                                <th>XML charter</th>
                                <th>Fecha de carga</th>
                                <th>Descargar todo</th>
                            </tr>
                        </thead>                

                        <tbody>
                            
                            @foreach($datas as $i)

                                <tr>
                                    <td>
                                        {{ $i->folder_name }}
                                    </td>

                                    <td>
                                        <a href="{{ route('download_xml', ['id'=>$i->id, 'file'=>'xml_original']) }}" class="fa fa-download">
                                            Descargar
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ route('download_xml', ['id'=>$i->id, 'file'=>'xml_charter']) }}" class="fa fa-download">
                                            Descargar
                                        </a>
                                    </td>

                                    <td>
                                        {{ $i->created_at }}
                                    </td>

                                    <td>
                                        <a href="{{ route('download_all', $i->id) }}" class="fa fa-download">
                                            Descargar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>
            
            <div class="form-group">
                {{ $datas->links() }}
            </div>

@endsection