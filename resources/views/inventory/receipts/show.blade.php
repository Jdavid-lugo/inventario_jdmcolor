@extends('layouts.app', ['page' => 'Recepciones', 'pageSlug' => 'receipts', 'section' => 'inventory'])


@section('content')
    @include('alerts.success')
    @include('alerts.error')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h4 class="card-title">Resumen de recepción</h4>
                        </div>
                        @if (!$receipt->finalized_at)
                            <div class="col-4 text-right">
                                @if ($receipt->products->count() === 0)
                                    <form action="{{ route('receipts.destroy', $receipt) }}" method="post" class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            Borrar Recepción
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary" onclick="confirm('ATENCIÓN: Al finalizar esta recepcion no podrá cargar más productos.') ? window.location.replace('{{ route('receipts.finalize', $receipt) }}') : ''">
                                        Finalizar Recepción
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
                            <th>Titulos</th>
                            <th>Usuario</th>
                            <th>Proveedor</th>
                            <th>Productos</th>
                            <th>Stock</th>
                            <th>Stock defetuoso</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $receipt->id }}</td>
                                <td>{{ date('d-m-y', strtotime($receipt->created_at)) }}</td>
                                <td style="max-width:150px;">{{ $receipt->title }}</td>
                                <td>{{ $receipt->user->name }}</td>
                                <td>
                                    @if($receipt->provider_id)
                                        <a href="{{ route('providers.show', $receipt->provider) }}">{{ $receipt->provider->name }}</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $receipt->products->count() }}</td>
                                <td>{{ $receipt->products->sum('stock') }}</td>
                                <td>{{ $receipt->products->sum('stock_defective') }}</td>
                                <td>{!! $receipt->finalized_at ? 'Finalizado' : '<span style="color:red; font-weight:bold;">Para finalizar</span>' !!}</td>
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
                            <h4 class="card-title">productos: {{ $receipt->products->count() }}</h4>
                        </div>
                        @if (!$receipt->finalized_at)
                            <div class="col-4 text-right">
                                <a href="{{ route('receipts.product.add', ['receipt' => $receipt]) }}" class="btn btn-sm btn-primary">Agregar</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>Categoría</th>
                            <th>Producto</th>
                            <th>Stock</th>
                            <th>Stock defetuoso</th>
                            <th>Total Stock</th>
                            <th></th>
                        </thead>
                        <tbody>
                            @foreach ($receipt->products as $received_product)
                                <tr>
                                    <td><a href="{{ route('categories.show', $received_product->product->category) }}">{{ $received_product->product->category->name }}</a></td>
                                    <td><a href="{{ route('products.show', $received_product->product) }}">{{ $received_product->product->name }}</a></td>
                                    <td>{{ $received_product->stock }}</td>
                                    <td>{{ $received_product->stock_defective }}</td>
                                    <td>{{ $received_product->stock + $received_product->stock_defective }}</td>
                                    <td class="td-actions text-right">
                                        @if(!$receipt->finalized_at)
                                            <a href="{{ route('receipts.product.edit', ['receipt' => $receipt, 'receivedproduct' => $received_product]) }}" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Edit Pedido">
                                                <i class="tim-icons icon-pencil"></i>
                                            </a>
                                            <form action="{{ route('receipts.product.destroy', ['receipt' => $receipt, 'receivedproduct' => $received_product]) }}" method="post" class="d-inline">
                                                @csrf
                                                @method('delete')
                                                <button type="button" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="Delete Pedido" onclick="confirm('Estás seguro que quieres eliminar este producto?') ? this.parentElement.submit() : ''">
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