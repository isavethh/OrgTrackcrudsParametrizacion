@extends('layouts.admin')

@section('title', 'Subir Documento - OrgTrack')
@section('page-title', 'Subir Documento')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.documentos.index') }}">Documentos</a></li>
    <li class="breadcrumb-item active">Subir Documento</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Subir Nuevo Documento</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="archivo">Seleccionar Archivo</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="archivo">
                                <label class="custom-file-label" for="archivo">Elegir archivo</label>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Formatos permitidos: PDF, JPG, PNG, DOC, DOCX (Máximo 10MB)
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label for="nombre">Nombre del Documento</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Nombre descriptivo del documento">
                    </div>
                    
                    <div class="form-group">
                        <label for="categoria">Categoría</label>
                        <select class="form-control" id="categoria">
                            <option value="">Seleccionar categoría</option>
                            <option value="factura">Factura</option>
                            <option value="guia_envio">Guía de Envío</option>
                            <option value="comprobante">Comprobante</option>
                            <option value="contrato">Contrato</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" rows="3" placeholder="Descripción del documento"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_documento">Fecha del Documento</label>
                                <input type="date" class="form-control" id="fecha_documento">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_referencia">Número de Referencia</label>
                                <input type="text" class="form-control" id="numero_referencia" placeholder="Número de referencia o folio">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="documento_publico">
                            <label class="custom-control-label" for="documento_publico">
                                Documento público (visible para otros usuarios)
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Subir Documento
                </button>
                <a href="{{ route('admin.documentos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
        <!-- /.card -->
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Formatos Soportados</h5>
                    <ul class="mb-0">
                        <li>PDF (Recomendado)</li>
                        <li>JPG, PNG (Imágenes)</li>
                        <li>DOC, DOCX (Documentos)</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Límites</h5>
                    <ul class="mb-0">
                        <li>Tamaño máximo: 10MB</li>
                        <li>Archivos por usuario: 100</li>
                    </ul>
                </div>
                
                <div class="alert alert-success">
                    <h5><i class="icon fas fa-check"></i> Consejos</h5>
                    <ul class="mb-0">
                        <li>Usa nombres descriptivos</li>
                        <li>Selecciona la categoría correcta</li>
                        <li>Verifica la calidad del archivo</li>
                    </ul>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

@section('scripts')
<script>
// Actualizar el label del input file
$('.custom-file-input').on('change', function() {
    let fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);
});
</script>
@endsection
