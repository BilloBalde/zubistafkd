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
                            <h4>Gestion Achat</h4>
                            <h6>Ajout Achat</h6>
                        </div>
                    </div>
                    @include('layouts.flash')
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('produits.store') }}" method="post" id="Register" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="libelle">Libelle</label>
                                            <input type="text" id="libelle" name="libelle" class="form-control" value="{{ old('libelle') }}"  placeholder="entrer le nombre du produit">
                                            @error('libelle')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="sku">Identifiant du produit</label>
                                            <input type="text" id="sku" name="sku" class="form-control" value="{{ old('sku') }}"  placeholder="entrer identifiant du produit">
                                            @error('sku')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                          <div class="col-lg-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="price">price unitaire</label>
                                            <input type="text" id="price" name="price" class="form-control" placeholder="entrer le prix unitaire du produit" value="{{ old('price') }}">
                                            @error('price')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                     <div class="col-lg-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="pcs">Nombre piece/ Carton</label>
                                            <input type="text" id="pcs" name="pcs" class="form-control" placeholder="entrer le nombre de piece par carton" value="{{ old('pcs') }}">
                                            @error('pcs')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                      <div class="col-lg-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="price_carton">Prix par Carton</label>
                                                <input type="text" id="price_carton" name="price_carton" class="form-control" 
                                                    value="{{ old('price_carton', $product->price_carton ?? '') }}" 
                                                    placeholder="Entrer le prix par carton" readonly>
                                                @error('price_carton')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    <div class="col-lg-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="categories">Categories:</label>
                                            <select name="categories[]" multiple required class="form-control">
                                                @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->slug }} ({{ $category->category_type }})</option>
                                                @endforeach
                                            </select>
                                            @error('categories')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                  
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" class="form-control">{{ old('description') }}</textarea>
                                            @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-sm-12 col-12">
                                        <div class="form-group">
                                            <label for="image"> Product Image</label>
                                            <div class="image-upload">
                                                <input type="file" name="image" value="{{ old('image') }}">
                                                <div class="image-uploads">
                                                    <img src="{{ asset('assets/img/icons/upload.svg') }}" alt="img">
                                                    <h4>Drag and drop a file to upload</h4>
                                                </div>
                                            </div>
                                            @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <button type="submit" class="btn btn-submit me-2">Valider</button>
                                        <a href="{{ route('produits.index') }}" class="btn btn-cancel">Annuler</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      <!--price carton calcul automatique puis close de modal annul et close dans le modal-->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    /**
                     * --- Mise à jour automatique du prix par carton ---
                     * price * pcs => price_carton
                     */
                    const priceInput = document.getElementById('price');
                    const pcsInput = document.getElementById('pcs');
                    const cartonPriceInput = document.getElementById('price_carton');

                    function updateCartonPrice() {
                        const price = parseFloat(priceInput?.value) || 0;
                        const pcs = parseInt(pcsInput?.value) || 0;
                        const cartonPrice = price * pcs;

                        if (!isNaN(cartonPrice)) {
                            cartonPriceInput.value = cartonPrice.toFixed(2);
                        }
                    }

                    priceInput?.addEventListener('input', updateCartonPrice);
                    pcsInput?.addEventListener('input', updateCartonPrice);

                    updateCartonPrice(); // Calcul initial
                });
            </script>
        <script>
            document.getElementById('categoryTaille_id').addEventListener('change', function() {
                var url = this.value;
                if (url && isValidURL(url)) {
                    window.location.href = url;
                }
            });
            document.getElementById('categorieEmballage_id').addEventListener('change', function() {
                var url = this.value;
                if (url && isValidURL(url)) {
                    window.location.href = url;
                }
            });
            document.getElementById('categoryEnsemble_id').addEventListener('change', function() {
                var url = this.value;
                if (url && isValidURL(url)) {
                    window.location.href = url;
                }
            });
            function isValidURL(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }
        </script>
        @include('layouts.scripts')
    </body>
</html>
