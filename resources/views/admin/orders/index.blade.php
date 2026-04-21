<!DOCTYPE html>
<html lang="en">
    @include('layouts.head')
    <body>
        <div id="global-loader">
            <div class="whirly-loader"> </div>
        </div>

        <div class="main-wrapper">
            @include('layouts.header')
            @include('layouts.sidebar')
            <div class="page-wrapper">
                <div class="content">
                    <div class="page-header">
                        <div class="page-title">
                            <h4>Commandes à valider</h4>
                            <h6>Gérer les commandes en attente</h6>
                        </div>
                    </div>
                    @include('layouts.flash')
                    <div class="card">
                        <div class="card-body">
                            <div class="table-top">
                                <div class="search-set">
                                    <div class="search-path">
                                        <a class="btn btn-filter" id="filter_search">
                                            <img src="{{ asset('assets/img/icons/filter.svg') }}" alt="img">
                                            <span><img src="{{ asset('assets/img/icons/closes.svg') }}" alt="img"></span>
                                        </a>
                                    </div>
                                    <div class="search-input">
                                        <a class="btn btn-searchset"><img src="{{ asset('assets/img/icons/search-white.svg') }}" alt="img"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card" id="filter_inputs">
                                <div class="card-body pb-0">
                                    <form action="{{ route('admin.orders.index') }}" method="GET">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <input type="text" name="order_id" value="{{ request('order_id') }}" placeholder="N° commande" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <input type="text" name="customer_name" value="{{ request('customer_name') }}" placeholder="Nom client" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-sm-6 col-12 d-flex align-items-center">
                                                <div class="form-group d-flex">
                                                    <button type="submit" class="btn btn-filters me-2"><img src="{{ asset('assets/img/icons/search-whites.svg') }}" alt="img"></button>
                                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Reset</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table datanew">
                                    <thead>
                                        <tr>
                                            <th>#ID</th>
                                            <th>Client</th>
                                            <th>Total (GNF)</th>
                                            <th>Paiement</th>
                                            <th>Statut</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->user->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($order->total_amount, 0, ',', ' ') }} GNF</td>
                                            <td>{{ strtoupper($order->payment_method) }}</td>
                                            <td>
                                                @if($order->status == 'pending')
                                                    <span class="badges bg-warning">En attente</span>
                                                @elseif($order->status == 'approved')
                                                    <span class="badges bg-success">Approuvée</span>
                                                @elseif($order->status == 'rejected')
                                                    <span class="badges bg-danger">Rejetée</span>
                                                @else
                                                    <span class="badges bg-secondary">{{ $order->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <form action="{{ route('admin.orders.approve', $order) }}" method="POST" class="me-2">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm" {{ $order->status != 'pending' ? 'disabled' : '' }}>
                                                            Approuver
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.orders.reject', $order) }}" method="POST" class="me-2">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm" {{ $order->status != 'pending' ? 'disabled' : '' }}>
                                                            Rejeter
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-info btn-sm">Voir</a>
                                                </div>
                                             </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.scripts')
        @include('layouts.delete')
    </body>
</html>