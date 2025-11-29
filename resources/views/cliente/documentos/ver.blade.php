@extends('layouts.cliente')

@section('page-title', 'Documento de Envío')

@section('page-content')
<div class="row">
    <div class="col-12">
        <a href="{{ route('documentos.particiones', ['id' => 9]) }}" class="mb-3 d-inline-block"><i class="fas fa-arrow-left mr-1"></i> Volver atrás</a>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="6" class="text-center">Ortrack<br><small>Documento de Envío</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3"><strong>Nombre de cliente</strong><br> Mauricio Martinez</td>
                                <td colspan="3"><strong>Fecha</strong><br> 13/10/2025</td>
                            </tr>
                            <tr>
                                <td colspan="3"><strong>Punto de recogida</strong><br> Ferbo, Municipio Santa Cruz de la Sierra</td>
                                <td colspan="3"><strong>Punto de entrega</strong><br> Av. Pedro Marbán, Petrolero Sur, Estación Argentina</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-center"><strong>Detalles de Bloque de Envío</strong></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Día</strong><br> 01/10/2025</td>
                                <td colspan="2"><strong>Hora de Recogida</strong><br> 09:30</td>
                                <td colspan="2"><strong>Hora de Entrega</strong><br> 11:30</td>
                            </tr>
                            <tr>
                                <td colspan="3"><strong>Instrucciones en punto de recogida</strong><br> Sin instrucciones</td>
                                <td colspan="3"><strong>Instrucciones en punto de entrega</strong><br> Sin instrucciones</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-center"><strong>Transportista</strong></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Nombre y Apellido</strong><br> Mauri Martinez</td>
                                <td colspan="2"><strong>Teléfono</strong><br> 75386249</td>
                                <td colspan="2"><strong>CI</strong><br> 132252</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-center"><strong>Vehículo</strong></td>
                            </tr>
                            <tr>
                                <td colspan="3"><strong>Tipo</strong><br> Mediano - Refrigerado</td>
                                <td colspan="3"><strong>Placa</strong><br> STU0123</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-center"><strong>Transporte</strong></td>
                            </tr>
                            <tr>
                                <td colspan="3"><strong>Tipo</strong><br> Refrigerado</td>
                                <td colspan="3"><strong>Descripción</strong><br> Ideal para productos que requieren baja temperatura como frutas y lácteos</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-center"><strong>Detalles de cargamento</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Tipo</strong><br> Verduras</td>
                                <td><strong>Variedad</strong><br> Zanahorias</td>
                                <td><strong>Empaquetado</strong><br> Bolsa plástica</td>
                                <td><strong>Cantidad</strong><br> 50</td>
                                <td colspan="2"><strong>Peso Kg</strong><br> 100 kg</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div style="font-size:24px"></div>
                                    <div>Firma del Cliente</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <a href="#" class="btn btn-primary">Descargar PDF</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


