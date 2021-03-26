@extends('layouts.app', ['page' => 'Administrar Ventas', 'pageSlug' => 'sales', 'section' => 'transactions'])

@section('content')
    @include('alerts.success')
    @include('alerts.error')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">Resumen de Ventas</h4>
                        </div>
                        @if (!$sale->finalized_at)
                            <div class="col-4 text-right">
                                @if ($sale->products->count() == 0)
                                    <form action="{{ route('sales.destroy', $sale) }}" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            Borrar Venta
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary" onclick="confirm('¿quiere finalizar esta venta?  Sus registros no se pueden modificar a partir de ahora. ') ? window.location.replace('{{ route('sales.finalize', $sale) }}') : ''">
                                        Finalizar Venta
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th>Total Stock</th>
                            <th>Precio Total</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ date('d-m-y', strtotime($sale->created_at)) }}</td>
                                <td>{{ $sale->user->name }}</td>
                                <td><a href="{{ route('clients.show', $sale->client) }}">{{ $sale->client->name }}<br>{{ $sale->client->document_type }}-{{ $sale->client->document_id }}</a></td>
                                <td>{{ $sale->products->count() }}</td>
                                <td>{{ $sale->products->sum('qty') }}</td>
                                <td>{{ format_money($sale->products->sum('total_amount')) }}</td>
                                <td>{!! $sale->finalized_at ? 'Completado en <br>'.date('d-m-y', strtotime($sale->finalized_at)) : (($sale->products->count() > 0) ? 'Para finalizar' : 'En espera') !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">Productos: {{ $sale->products->sum('qty') }}</h4>
                        </div>
                        @if (!$sale->finalized_at)
                            <div class="col-4 text-right">
                                <a href="{{ route('sales.product.add', ['sale' => $sale->id]) }}" class="btn btn-sm btn-primary">Agregar</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>ID</th>
                            <th>Categoría</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio C/U</th>
                            <th>Total</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @foreach ($sale->products as $sold_product)
                                <tr>
                                    <td>{{ $sold_product->product->id }}</td>
                                    <td><a href="{{ route('categories.show', $sold_product->product->category) }}">{{ $sold_product->product->category->name }}</a></td>
                                    <td><a href="{{ route('products.show', $sold_product->product) }}">{{ $sold_product->product->name }}</a></td>
                                    <td>{{ $sold_product->qty }}</td>
                                    <td>{{ format_money($sold_product->price) }}</td>
                                    <td>{{ format_money($sold_product->total_amount) }}</td>
                                    <td class="td-actions text-right">
                                        @if(!$sale->finalized_at)
                                            <a href="{{ route('sales.product.edit', ['sale' => $sale, 'soldproduct' => $sold_product]) }}" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Editar Pedido">
                                                <i class="tim-icons icon-pencil"></i>
                                            </a>
                                            <form action="{{ route('sales.product.destroy', ['sale' => $sale, 'soldproduct' => $sold_product]) }}" method="post" class="d-inline">
                                                @csrf
                                                @method('delete')
                                                <button type="button" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Borrar Pedido" onclick="confirm('Estás seguro que quieres eliminar este producto? Su registro será eliminado de esta venta.') ? this.parentElement.submit() : ''">
                                                    <i class="tim-icons icon-simple-remove"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets') }}/js/sweetalerts2.js"></script>
@endpush