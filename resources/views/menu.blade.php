<div class="col-md-12" id="menu">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                        <li>
                            <center><img src="{{ asset('/assets/img/abae-charter3-otra.png') }}"></center>
                        </li>
                    
                        <li>
                            <a class="navbar-brand" href="{{ route('index') }}">
                                CHARTER
                            </a>
                        </li>
                        
                        <li>
                            <a href="#" data-toggle="collapse" data-target="#toggleDemo2" data-parent="#sidenav01" class="collapsed">
                                <span class="fa fa-upload"></span>Subir Archivos<span class="caret"></span>
                            </a>
                            
                            <div class="collapse" id="toggleDemo2" style="height: 0px;">
                                <ul class="nav nav-list">
                                    <li>
                                        <a href="{{ route('data.create') }}">
                                            Cargar datos
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <li>
                            <a href="#" data-toggle="collapse" data-target="#toggleDemo1" data-parent="#sidenav01" class="collapsed">
                                <span class="fa fa-retweet"></span>Procesos<span class="caret"></span>
                            </a>
                            
                            <div class="collapse" id="toggleDemo1" style="height: 0px;">
                                <ul class="nav nav-list">
                                    <li>
                                        <a href="{{ route('data.index') }}">
                                            Visualizar datos
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <li>
                            
                            @if((Auth::user()->role === 'admin'))
                                <a href="#" data-toggle="collapse" data-target="#toggleDemo3" data-parent="#sidenav01" class="collapsed">
                                    <span class="fa fa-cog"></span>Gestión de usuarios<span class="caret"></span>
                                </a>
                                <div class="collapse" id="toggleDemo3" style="height: 0px;">
                                    <ul class="nav nav-list">
                                        <li>
                                            <a href="{{ route('user.create') }}">
                                                Crear nuevo usuario
                                            </a>
                                        </li>
                                    </ul>
                                    <ul class="nav nav-list">
                                        <li>
                                            <a href="{{ route('user.index') }}">
                                                Editar usuarios
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </li>
                    
                        <li>
                            <a href="#" data-toggle="collapse" data-target="#toggleDemo4" data-parent="#sidenav01" class="collapsed">
                                <span class="fa fa-times-circle"></span>Salir<span class="caret"></span>
                            </a>
                            
                            <div class="collapse" id="toggleDemo4" style="height: 0px;">
                                <ul class="nav nav-list">
                                    <li>
                                        <a href="{{ route('logout') }}">
                                            Cerrar sesión
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
