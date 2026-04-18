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
                            <h4>Gestion d'Achat</h4>
                            <h6>Modifier l'Achat</h6>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                           <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" id="Register">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id" value="{{ $purchase->id }}">

                                <div class="row">
                                    {{-- Produit --}}
                                    <div class="col-lg-4 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="product_id">Produit</label>
                                            <select id="product_id" name="product_id" class="form-control select2" required>
                                                <option value="">-- Sélectionner un produit --</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" 
                                                        {{ old('product_id', $purchase->product_id) == $product->id ? 'selected' : '' }}>
                                                        {{ $product->libelle }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="error-danger"><strong id="product_id-error"></strong></span>
                                        </div>
                                    </div>

                                    {{-- Store --}}
                                  <div class="col-lg-4 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="store_id">Magasin</label>
                                            <select id="store_id" name="store_id" class="form-control select2" required>
                                                <option value="">-- Sélectionner un magasin --</option>
                                                @foreach ($stores as $store)
                                                    <option value="{{ $store->id }}" 
                                                        {{ old('store_id', $purchase->store_id) == $store->id ? 'selected' : '' }}>
                                                        {{ $store->store_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span class="error-danger"><strong id="store_id-error"></strong></span>
                                        </div>
                                    </div>
                                    {{-- Prix d'achat --}}
                                    <div class="col-lg-4 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="price">Prix d'achat</label>
                                            <input type="text" id="price" name="price" 
                                                value="{{ old('price', $purchase->price) }}"
                                                class="form-control">
                                             
                                            <span class="error-danger"><strong id="price-error"></strong></span>
                                        </div>
                                    </div>

                                    {{-- Prix carton --}}
                                    <div class="col-lg-4 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="price_ctn">Prix Carton</label>
                                            <input type="text" id="price_ctn" name="price_ctn" 
                                                value="{{ old('price_ctn', $purchase->price_ctn)}}" 
                                                class="form-control">
                                            <span class="error-danger"><strong id="price_ctn-error"></strong></span>
                                        </div>
                                    </div>
                                    {{-- Quantité --}}
                                    <div class="col-lg-4 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="quantity">Quantité</label>
                                            <input type="number" id="quantity" name="quantity" 
                                                value="{{ old('quantity', $purchase->quantity) }}" 
                                                class="form-control" required min="1">
                                            <span class="error-danger"><strong id="quantity-error"></strong></span>
                                        </div>
                                    </div>

                                    {{-- Boutons --}}
                                    <div class="col-lg-12 mt-3">
                                        <button type="submit" class="btn btn-submit me-2">Modifier</button>
                                        <a href="{{ route('purchases.index') }}" class="btn btn-cancel">Annuler</a>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.scripts')
    </body>
</html>
