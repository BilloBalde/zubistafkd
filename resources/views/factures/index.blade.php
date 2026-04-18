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
                            <h4>Liste des Factures</h4>
                            <h6>Gerer vos Factures</h6>
                        </div>
                        <div class="page-btn">
                            <a href="{{ route('sales.index') }}" class="btn btn-added"><img src="{{ asset('assets/img/icons/return1.svg') }}" alt="img" class="me-2"></a>
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
                                    <form action="{{ route('factures.index') }}" method="GET"> <!-- Update to GET method -->
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-2 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <input type="text" name="numero_facture" value="{{ request('numero_facture') }}" placeholder="numero facture" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <select name="customer_id" id="customer_id" class="form-control">
                                                        <option value="">Selectionner Client</option>
                                                        @foreach ($customers as $item)
                                                        <option value="{{ $item->id }}" {{ request('customer_id') == $item->id ? 'selected' : '' }}>{{ $item->customerName }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <select name="statut" id="statut" class="form-control">
                                                        <option value="">Selectionner Statut</option>
                                                        <option value="payé" {{ request('statut') == 'payé' ? 'selected' : '' }}>Payé</option>
                                                        <option value="non payé" {{ request('statut') == 'non payé' ? 'selected' : '' }}>Non Payé</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <select name="livraison" id="livraison" class="form-control">
                                                        <option value="">Selectionner Livraison</option>
                                                        <option value="livré" {{ request('livraison') == 'livré' ? 'selected' : '' }}>livré</option>
                                                        <option value="non livré" {{ request('livraison') == 'non livré' ? 'selected' : '' }}>Non livré</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <input type="date" name="created_at" value="{{ request('created_at') }}" placeholder="date creation" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-sm-6 col-12 d-flex align-items-center">
                                                <div class="form-group d-flex">
                                                    <button type="submit" class="btn btn-filters me-2"><img src="assets/img/icons/search-whites.svg" alt="img"></button>
                                                    <a href="{{ route('factures.index') }}" class="btn btn-secondary">Annuler</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table  datanew">
                                    <thead>
                                        <tr>
                                            <th>No. Facture</th>
                                            <th>Information du Client</th>
                                            <th>Information Stock</th>
                                            <th>Quantité</th>
                                            <th>Montant</th>
                                            <th>Avance</th>
                                            <th>Reste</th>
                                            <th>Status</th>
                                            <th>Livraison</th>
                                            <th>Note</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                            <th>Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataTable as $data)
                                        <tr>
                                            <td><a href="{{ route('voirSales', $data->numero_facture) }}">{{ $data->numero_facture }}</a></td>
                                            <td>{{ $data->customerName }}</td>
                                            <td>{{ $data->store->store_name }}</td>
                                            <td>{{ $data->quantity }}</td>
                                            <td>{{ $data->montant_total }}</td>
                                            <td>{{ $data->avance }}</td>
                                            <td>{{ $data->reste }}</td>
                                            <td>
                                                <span class="badge
                                                    @if ($data->statut === 'payé') bg-success
                                                   @elseif ($data->statut === 'partiel') bg-warning 
                                                    @elseif ($data->statut === 'non payé') bg-danger
                                                    @endif
                                                    ">
                                                    {{ ucfirst($data->statut) }}
                                                </span>
                                            </td>
                                            <td>{{ $data->livraison }}</td>
                                            <td>{{ $data->notes }}</td>
                                            <td>{{ $data->created_at }}</td>
                                            <td>
                                                <a href="{{ route('factures.show', $data->numero_facture) }}"><img src="assets/img/icons/eye1.svg" class="me-2" alt="img"></a>
                                                @if ($data->livraison !== "livré")
                                                <a href="javascript:void(0);" class="me-2" data-bs-toggle="modal" data-bs-target="#editfacture" data-id="{{ $data->id }}" data-reference="{{ $data->numero_facture }}"><img src="assets/img/icons/calendars.svg" class="me-2" alt="img"></a>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{{ route('payments.voir', $data->id) }}" class="dropdown-item"><img src="assets/img/icons/dollar-square.svg" class="me-2" alt="img">Voir Paiements</a>
                                                    </li>
                                                    @if ($data->statut !== "payé")
                                                    <li>
                                                        <a href="{{ route('payments.creation', $data->id) }}" class="dropdown-item"><img src="assets/img/icons/plus-circle.svg" class="me-2" alt="img">Ajouter Paiement</a>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @include('layouts.scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var deleteModal = document.getElementById('editfacture');
            deleteModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var deleteId = button.getAttribute('data-id');
                var deleteRef = button.getAttribute('data-reference');
                var form = deleteModal.querySelector('#deleteForm');
                form.action = '/factures/' + deleteId;

                var expenseRefSpan = deleteModal.querySelector('#expense-reference');
                expenseRefSpan.textContent = deleteRef;
            });
        });
    </script>
    @include('factures.edit')
    </body>
</html>
