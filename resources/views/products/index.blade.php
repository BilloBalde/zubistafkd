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
                            <h4>Liste des produits</h4>
                            <h6>Gerer vos produits</h6>
                        </div>
                        <div class="page-btn">
                            <a href="{{ route('produits.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-2">Ajouter Produit</a>
                        </div>
                    </div>
                    @include('layouts.flash')
                    <div class="card">
                        <div class="card-body">
                            <div class="table-top">
                                <div class="search-set">
                                    <div class="search-path">
                                        <a class="btn btn-filter" id="filter_search">
                                            <img src="assets/img/icons/filter.svg" alt="img">
                                            <span><img src="assets/img/icons/closes.svg" alt="img"></span>
                                        </a>
                                    </div>
                                    <div class="search-input">
                                        <a class="btn btn-searchset"><img src="assets/img/icons/search-white.svg" alt="img"></a>
                                    </div>
                                </div>
                                <div class="wordset">
                                    <ul>
                                        <li>
                                            <a href="{{ route('products.export-pdf') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img src="assets/img/icons/pdf.svg" alt="img"></a>
                                        </li>
                                        <li>
                                            <a href="{{ route('products.export-excel') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img src="assets/img/icons/excel.svg" alt="img"></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card" id="filter_inputs">
                                <div class="card-body pb-0">
                                    <form action="{{ route('produits.index') }}" method="GET">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-4 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <input type="text" name="libelle" value="{{ request('libelle') }}" placeholder="libelle" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <select name="category_id" id="category_id" class="form-control">
                                                        <option value="">Selectionner Category</option>
                                                        @foreach ($categories as $item)
                                                        <option value="{{ $item->id }}" {{ request('category_id') == $item->id ? 'selected' : '' }}>{{ $item->slug }} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-sm-6 col-12 d-flex align-items-center">
                                                <div class="form-group d-flex">
                                                    <button type="submit" class="btn btn-filters me-2"><img src="assets/img/icons/search-whites.svg" alt="img"></button>
                                                    <a href="{{ route('produits.index') }}" class="btn btn-secondary">Reset</a>
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
                                            <th>Nom Produit</th>
                                            <th>Identifiant Stock</th>
                                            <th>Prix Unitaire</th>
                                            <th>Nb Pieces/Carton</th>
                                            <th>prix Par Carton</th>
                                            <th>Category</th>
                                            <th>Qtité en Stock</th>
                                            <th>Image</th>
                                            <th>Date d'ajout</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allProducts as $dataItem)
                                            @php
                                                // Initialisation obligatoire pour éviter l'erreur "Undefined variable"
                                                $quantity = 0;

                                                // Récupérer l'ID du store de l'utilisateur (si applicable)
                                                $userStoreId = Auth::user()->store_id;

                                                // Vérifier si l'utilisateur a le rôle 3
                                                if (Auth::user()->role_id === 3) {
                                                    // Pour les utilisateurs avec role_id 3, quantité pour son store
                                                    $quantity = DB::table('store_products')
                                                        ->where('store_id', $userStoreId)
                                                        ->where('product_id', $dataItem->id)
                                                        ->value('quantity') ?? 0;
                                                }

                                                // Vérifier si l'utilisateur a le rôle 2
                                                if (Auth::user()->role_id === 2) {
                                                    // Pour les utilisateurs avec role_id 2, quantité du store principal (store_id 1)
                                                    $quantity = DB::table('store_products')
                                                        ->where('store_id', 1)
                                                        ->where('product_id', $dataItem->id)
                                                        ->value('quantity') ?? 0;
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ $dataItem->libelle }}</td>
                                                <td>{{ $dataItem->sku }}</td>
                                                <td>{{ $dataItem->price }}</td>
                                                <td>{{ $dataItem->pcs }}</td>
                                                <td>{{ $dataItem->price_carton }}</td>
                                                <td>
                                                    @foreach ($dataItem->categories as $category)
                                                        {{ $category->slug . ' (' . $category->category_type . ')' }}
                                                    @endforeach
                                                </td>
                                                <td>{{ $quantity }}</td>
                                                <td>
                                                    <img src="{{ asset('products/' . $dataItem->image) }}" alt="product" style="width: 150px; height: 100px;">
                                                </td>
                                                <td>{{ $dataItem->updated_at }}</td>
                                                <td>
                                                    <a class="me-3" href="{{ route('produits.edit', $dataItem->id) }}">
                                                        <img src="{{ asset('assets/img/icons/edit.svg') }}" alt="img">
                                                    </a>
                                                    <a
                                                        type="button"
                                                        class="me-3 deleteButtionItem"
                                                        data-bs-toggle="modal"
                                                        data-slug="{{ $dataItem->sku }}"
                                                        data-bs-target="#confirmDeleteModal"
                                                        onclick="setDeleteFormAction('{{ route('produits.destroy', $dataItem->id) }}')">
                                                        <img src="assets/img/icons/delete.svg" class="me-2" alt="img">
                                                    </a>
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
        @include('layouts.delete')
        @include('products.delete')
    </body>
</html>