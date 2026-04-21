<!DOCTYPE html>
<html lang="fr">
    @include('layouts.head')
    <body>
        <div id="global-loader">
            <div class="whirly-loader"></div>
        </div>

        <div class="main-wrapper">
            @include('layouts.header')
            @include('layouts.sidebar')
            <div class="page-wrapper">
                <div class="content">
                    <div class="page-header">
                        <div class="page-title">
                            <h4>Ventes confirmées</h4>
                            <h6>Toutes les commandes approuvées et leurs ventes</h6>
                        </div>
                        <div class="page-btn">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                <i class="me-1 fas fa-clock"></i> Commandes en attente
                            </a>
                        </div>
                    </div>

                    @include('layouts.flash')

                    {{-- Filtres --}}
                    <div class="card mb-3">
                        <div class="card-body pb-0">
                            <form action="{{ route('admin.orders.confirmed') }}" method="GET">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="form-group">
                                            <input type="text" name="order_id" value="{{ request('order_id') }}"
                                                placeholder="N° commande" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="form-group">
                                            <input type="text" name="client" value="{{ request('client') }}"
                                                placeholder="Nom client" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="form-group">
                                            <select name="payment" class="form-control">
                                                <option value="">Tous les statuts paiement</option>
                                                <option value="non payé"  {{ request('payment') == 'non payé'  ? 'selected' : '' }}>Non payé</option>
                                                <option value="partiel"   {{ request('payment') == 'partiel'   ? 'selected' : '' }}>Partiel</option>
                                                <option value="payé"      {{ request('payment') == 'payé'      ? 'selected' : '' }}>Payé</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-12">
                                        <div class="form-group d-flex gap-2">
                                            <button type="submit" class="btn btn-filters">
                                                <img src="{{ asset('assets/img/icons/search-whites.svg') }}" alt="img">
                                            </button>
                                            <a href="{{ route('admin.orders.confirmed') }}" class="btn btn-secondary">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Tableau --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table datanew">
                                    <thead>
                                        <tr>
                                            <th>#Cmd</th>
                                            <th>Client</th>
                                            <th>N° Facture</th>
                                            <th>Articles</th>
                                            <th>Total (GNF)</th>
                                            <th>Intérêt (GNF)</th>
                                            <th>Paiement</th>
                                            <th>Payé (GNF)</th>
                                            <th>Reste (GNF)</th>
                                            <th>Livraison</th>
                                            <th>Date confirmation</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $order)
                                        <tr>
                                            <td><strong>#{{ $order->id }}</strong></td>
                                            <td>
                                                {{ $order->user->name ?? 'N/A' }}
                                                <span class="d-block text-muted" style="font-size:11px;">
                                                    {{ $order->user->phone ?? '' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($order->facture)
                                                    <span class="badge bg-light text-dark font-monospace">
                                                        {{ $order->facture->numero_facture }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @foreach($order->items as $item)
                                                    <span class="d-block" style="font-size:12px;">
                                                        {{ $item->product->libelle ?? '#'.$item->product_id }}
                                                        <em class="text-muted">× {{ $item->quantity }}</em>
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>{{ number_format($order->total_amount, 0, ',', ' ') }}</td>
                                            <td class="text-info fw-semibold">
                                                {{ number_format($order->sales->sum('interet'), 0, ',', ' ') }}
                                            </td>
                                            <td>
                                                @if($order->facture)
                                                    @php
                                                        $ps = match($order->facture->statut) {
                                                            'payé'    => ['label' => 'Payé',     'class' => 'bg-success'],
                                                            'partiel' => ['label' => 'Partiel',  'class' => 'bg-warning'],
                                                            default   => ['label' => 'Non payé', 'class' => 'bg-danger'],
                                                        };
                                                    @endphp
                                                    <span class="badges {{ $ps['class'] }}">{{ $ps['label'] }}</span>
                                                @else
                                                    <span class="badges bg-secondary">—</span>
                                                @endif
                                            </td>
                                            <td class="text-success fw-semibold">
                                                {{ $order->facture ? number_format($order->facture->avance, 0, ',', ' ') : '—' }}
                                            </td>
                                            <td class="{{ ($order->facture && $order->facture->reste > 0) ? 'text-danger' : 'text-muted' }} fw-semibold">
                                                {{ $order->facture ? number_format($order->facture->reste, 0, ',', ' ') : '—' }}
                                            </td>
                                            <td>
                                                @if($order->facture)
                                                    @if($order->facture->livraison === 'livré')
                                                        <span class="badges bg-success">Livrée</span>
                                                    @else
                                                        <span class="badges bg-warning">En attente</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex gap-1 flex-wrap">
                                                    @if($order->facture)
                                                        {{-- Voir facture --}}
                                                        <a href="{{ route('factures.show', $order->facture->id) }}"
                                                           class="btn btn-info btn-sm" title="Voir facture">
                                                            <i class="fas fa-file-invoice"></i>
                                                        </a>

                                                        {{-- Enregistrer paiement --}}
                                                        @if($order->facture->statut !== 'payé')
                                                        <a href="{{ route('payments.creation', $order->facture->id) }}"
                                                           class="btn btn-success btn-sm" title="Enregistrer paiement">
                                                            <i class="fas fa-money-bill-wave"></i>
                                                        </a>
                                                        @endif

                                                        {{-- Marquer comme livré --}}
                                                        @if($order->facture->livraison !== 'livré')
                                                        <form action="{{ route('factures.update', $order->facture->id) }}"
                                                              method="POST" style="display:inline"
                                                              onsubmit="return confirm('Confirmer la livraison de cette commande ?')">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-warning btn-sm" title="Marquer comme livré">
                                                                <i class="fas fa-truck"></i>
                                                            </button>
                                                        </form>
                                                        @else
                                                        <span class="btn btn-sm btn-outline-success disabled" title="Déjà livrée">
                                                            <i class="fas fa-check"></i>
                                                        </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted py-4">
                                                Aucune vente confirmée pour le moment.
                                            </td>
                                        </tr>
                                        @endforelse
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
