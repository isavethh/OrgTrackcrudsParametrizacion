<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'OrgTrack')</title>

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
                    <span id="cliente-nav-username">Invitado</span>
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
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-text font-weight-light"><b>Org</b>Track</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="https://adminlte.io/themes/v3/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block" id="cliente-username">Invitado</a>
                </div>
            </div>

            @include('cliente.components.sidebar-cliente')
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
                        <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
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
        console.warn('No se pudo limpiar el localStorage', error);
    }
    window.location.replace('/login');
}

document.querySelectorAll('.logout-link').forEach(function(link) {
    link.addEventListener('click', performLogout);
});

// Guard de acceso: requiere token en localStorage y envía a la vista correcta
(function(){
    try {
        var token = localStorage.getItem('authToken');
        var usuarioRaw = localStorage.getItem('usuario');
        var usuario = usuarioRaw ? JSON.parse(usuarioRaw) : null;
        var rol = usuario && usuario.rol ? usuario.rol.toLowerCase() : 'cliente';

        if (!token) {
            if (window.location.pathname !== '/login' && window.location.pathname !== '/register') {
                window.location.replace('/login');
            }
            return;
        }

        if (rol === 'admin') {
            window.location.replace('/admin/dashboard');
            return;
        }

        if (usuario) {
            var nombreCompleto = ((usuario.nombre || '') + ' ' + (usuario.apellido || '')).trim();
            var navLabel = document.getElementById('cliente-nav-username');
            var sidebarLabel = document.getElementById('cliente-username');
            if (nombreCompleto) {
                if (navLabel) navLabel.textContent = nombreCompleto;
                if (sidebarLabel) sidebarLabel.textContent = nombreCompleto;
            }
        }
    } catch (e) {
        if (window.location.pathname !== '/login' && window.location.pathname !== '/register') {
            window.location.replace('/login');
        }
    }
})();
</script>
@yield('scripts')
</body>
</html>
