<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin - OrgTrack')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Ionicons (para iconos ion-*) -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- User Account Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                    <span id="admin-username">Administrador</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Perfil
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item logout-link">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                    </a>
                </div>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <span class="brand-text font-weight-light"><b>Org</b>Track <small>Admin</small></span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block" id="admin-sidebar-username">Administrador</a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-header">GESTIÓN DE ENVÍOS</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.envios.index') }}" class="nav-link {{ request()->routeIs('admin.envios.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shipping-fast"></i>
                            <p>Envíos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.envios.create') }}" class="nav-link {{ request()->routeIs('admin.envios.create') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-plus"></i>
                            <p>Nuevo Envío</p>
                        </a>
                    </li>
                    <li class="nav-header">GESTIÓN DE USUARIOS</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.transportistas.index') }}" class="nav-link {{ request()->routeIs('admin.transportistas.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-friends"></i>
                            <p>Transportistas</p>
                        </a>
                    </li>
                    <li class="nav-header">GESTIÓN DE RECURSOS</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.vehiculos.index') }}" class="nav-link {{ request()->routeIs('admin.vehiculos.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>Vehículos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.condiciones.index') }}" class="nav-link {{ request()->routeIs('admin.condiciones.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-check"></i>
                            <p>Condiciones</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.incidentes.index') }}" class="nav-link {{ request()->routeIs('admin.incidentes.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-exclamation-triangle"></i>
                            <p>Incidentes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.direcciones.index') }}" class="nav-link {{ request()->routeIs('admin.direcciones.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-map-marker-alt"></i>
                            <p>Direcciones</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.documentos.index') }}" class="nav-link {{ request()->routeIs('admin.documentos.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Documentos</p>
                        </a>
                    </li>
                    <li class="nav-header">PARAMETRIZACIÓN</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.unidades_medida.index') }}" class="nav-link {{ request()->routeIs('admin.unidades_medida.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-balance-scale"></i>
                            <p>Unidades de Medida</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.catalogo_carga.index') }}" class="nav-link {{ request()->routeIs('admin.catalogo_carga.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Catálogo de Carga</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">@yield('page-title', 'Dashboard Admin')</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            @yield('breadcrumb')
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @yield('content')
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <strong>Copyright &copy; 2024 <a href="#">OrgTrack</a>.</strong>
        Todos los derechos reservados.
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
function performLogout(event) {
    if (event) event.preventDefault();
    try {
        localStorage.removeItem('authToken');
        localStorage.removeItem('usuario');
    } catch (error) {
        console.warn('No se pudo limpiar el localStorage', error);image.png
    }
    window.location.replace('/login');
}

document.querySelectorAll('.logout-link').forEach(function(link) {
    link.addEventListener('click', performLogout);
});

// Guard de acceso: requiere token y rol admin
(function(){
    try {
        var token = localStorage.getItem('authToken');
        var usuarioStr = localStorage.getItem('usuario');
        var usuario = usuarioStr ? JSON.parse(usuarioStr) : null;
        
        if (!token) {
            if (window.location.pathname !== '/login' && window.location.pathname !== '/register') {
                window.location.replace('/login');
            }
            return;
        }
        
        // Verificar que sea admin
        if (usuario && (usuario.rol || '').toLowerCase() !== 'admin') {
            if (window.location.pathname.startsWith('/admin')) {
                window.location.replace('/dashboard');
            }
            return;
        }
        
        // Actualizar nombre de usuario en la UI
        if (usuario) {
            var nombreCompleto = ((usuario.nombre || '') + ' ' + (usuario.apellido || '')).trim();
            if (nombreCompleto) {
                var headerUser = document.getElementById('admin-username');
                var sidebarUser = document.getElementById('admin-sidebar-username');
                if (headerUser) headerUser.textContent = nombreCompleto;
                if (sidebarUser) sidebarUser.textContent = nombreCompleto;
            }
        }
    } catch (e) {
        console.error('Error en guard admin:', e);
        if (window.location.pathname !== '/login' && window.location.pathname !== '/register') {
            window.location.replace('/login');
        }
    }
})();
</script>
@yield('scripts')
</body>
</html>

