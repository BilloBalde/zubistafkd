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
                            <h4>Liste des Ventes</h4>
                            <h6>Gerer vos Ventes</h6>
                        </div>
                        <div class="page-btn">
                            {{-- <a class="btn btn-added" data-bs-toggle="modal" data-bs-target="#addsale"><img src="{{ asset('assets/img/icons/plus.svg') }}" alt="img" class="me-2">Vendre</a> --}}
                            <a class="btn btn-added" href="{{ route('pos') }}">
                                Vendre
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            @include('layouts.flash')
                            <div class="table-top">
                                <div class="search-set">
                                    <div class="search-path">
                                        <a class="btn btn-filter" id="filter_search">
                                            <img src="assets/img/icons/filter.svg" alt="img">
                                            <span><img src="assets/img/icons/closes.svg" alt="img"></span>
                                        </a>
                                    </div>
                                    <div class="search-input">
                                        <a class="btn btn-searchset"><img src="{{ asset('assets/img/icons/search-white.svg') }}" alt="img"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card" id="filter_inputs">
                                <div class="card-body pb-0">
                                    <form action="{{ route('sales.index') }}" method="GET"> <!-- Update to GET method -->
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-3 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <input type="text" name="numeroFacture" value="{{ request('numeroFacture') }}" placeholder="numeroFacture" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <select name="product_id" id="product_id" class="form-control">
                                                        <option value="">Selectionner Produit</option>
                                                        @foreach ($produits as $item)
                                                        <option value="{{ $item->id }}" {{ request('product_id') == $item->id ? 'selected' : '' }}>{{ $item->libelle }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <input type="date" name="created_at" value="{{ request('created_at') }}" placeholder="date creation" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-12 d-flex align-items-center">
                                                <div class="form-group d-flex">
                                                    <button type="submit" class="btn btn-filters me-2"><img src="assets/img/icons/search-whites.svg" alt="img"></button>
                                                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">Remettre à jour</a>
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
                                            <th>Produit</th>
                                            <th>Quantité</th>
                                            <th>Prix</th>
                                            <th>Prix Total</th>
                                            <th>Interet</th>
                                            <th>Created at</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataTable as $data)
                                        <tr>
                                            <td>{{ $data->numeroFacture }}</td>
                                            <td class="productimgname">
                                                <a href="javascript:void(0);" class="product-img">
                                                    <img src="{{ asset('products/' . $data->produitImage) }}" alt="product">
                                                </a>
                                                <a href="javascript:void(0);">{{ $data->produit }}</a>
                                            </td>
                                            <td>{{ $data->quantity }}</td>
                                            <td>{{ $data->prix }}</td>
                                            <td>{{ $data->prixTotal }}</td>
                                            <td>{{ $data->interet }}</td>
                                            <td>{{ $data->created_at }}</td>
                                            <td class="text-end">
                                                @if (App\Models\Facture::where('numero_facture', $data->numeroFacture)->first()->statut == 'pending')
                                                <a class="me-3" href="{{ route('sales.edit', $data->id) }}">
                                                    <img src="assets/img/icons/edit.svg" alt="img">
                                                </a>
                                                @else
                                                No action
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
            </div>
        </div>

        @include('factures.add')

        @include('layouts.scripts')
    </body>
</html>
