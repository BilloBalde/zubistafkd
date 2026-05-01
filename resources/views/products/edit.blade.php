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
                            <h4>Modification Achat</h4>
                            <h6>Modifier l'Achat</h6>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('produits.update', $product->id) }}" method="POST" id="Register" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="id" value="{{ $product->id }}">

                                <div class="row">
                                    <!-- Product Name -->
                                    <div class="col-lg-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="libelle">Libelle</label>
                                            <input type="text" id="libelle" name="libelle" class="form-control" value="{{ old('libelle', $product->libelle) }}" required>
                                            @error('libelle')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Stock Identifier -->
                                    <div class="col-lg-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="sku">Identifiant produit</label>
                                            <input type="text" id="sku" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" required>
                                            @error('sku')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="price">Prix Unitaire du produit</label>
                                            <input type="text" id="price" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                                            @error('price')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                     <div class="col-lg-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="pcs">Nombre de pieces par carton</label>
                                            <input type="text" id="pcs" name="pcs" class="form-control" value="{{ old('pcs', $product->pcs) }}" required>
                                            @error('pcs')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                     <div class="col-lg-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="price_carton">Prix par carton</label>
                                            <input type="text" id="price_carton" name="price_carton" class="form-control" value="{{ old('price_carton', $product->price_carton) }}" required>
                                            @error('price_carton')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- Categories (Multiple Select) -->
                                    <div class="col-lg-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="categories">Categories:</label>
                                            <select name="categories[]" id="categories" multiple class="form-control" required>
                                                @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ in_array($category->id, $product->categories->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                    {{ $category->slug }} ({{ $category->category_type }})
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('categories')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- Product Description -->
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" class="form-control" required>{{ old('description', $product->description) }}</textarea>
                                            @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Product Image Upload -->
                                    <div class="col-lg-12 col-sm-12 col-12">
                                        <div class="form-group">
                                            <label for="image">Product Image</label>
                                            <div class="image-upload">
                                                @if ($product->image)
                                                    <img src="{{ asset('products/' . $product->image) }}" alt="product image" style="width: 150px; height: auto; border: 1px solid #ccc; margin-bottom: 10px;">
                                                @else
                                                    <p>Aucune Image associated</p>
                                                @endif

                                                <!-- File Input -->
                                                <input type="file" name="image" id="image" class="form-control">
                                                <small class="form-text text-muted">Mettre a jour l'image of product.</small>
                                            </div>
                                            @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- ── Section Promotion ── -->
                                    <div class="col-12 mt-3">
                                        <div class="card" style="border:2px solid #f59e0b;border-radius:12px;">
                                            <div class="card-header d-flex align-items-center gap-3" style="background:#fffbeb;border-bottom:1px solid #fde68a;">
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" id="is_promo" name="is_promo" value="1"
                                                           {{ old('is_promo', $product->is_promo) ? 'checked' : '' }}
                                                           onchange="togglePromoSection(this)">
                                                    <label class="form-check-label fw-bold" for="is_promo" style="color:#d97706;">
                                                        🏷️ Mettre en promotion
                                                    </label>
                                                </div>
                                                <small class="text-muted">Cochez pour activer une réduction sur ce produit</small>
                                            </div>
                                            <div class="card-body" id="promo-section" style="{{ old('is_promo', $product->is_promo) ? '' : 'display:none;' }}">
                                                <div class="row g-3">
                                                    <div class="col-lg-4 col-sm-6 col-12">
                                                        <label class="form-label fw-semibold">Prix promotionnel (GNF)</label>
                                                        <input type="number" id="promo_price" name="promo_price" class="form-control"
                                                               placeholder="Ex: 22400"
                                                               value="{{ old('promo_price', $product->promo_price) }}"
                                                               min="0" oninput="updateDiscount()">
                                                        <div id="discount-preview" class="mt-1" style="font-size:13px;color:#16a34a;font-weight:600;"></div>
                                                    </div>
                                                    <div class="col-lg-4 col-sm-6 col-12">
                                                        <label class="form-label fw-semibold">Date de fin de promo</label>
                                                        <input type="datetime-local" id="promo_ends_at" name="promo_ends_at" class="form-control"
                                                               value="{{ old('promo_ends_at', $product->promo_ends_at ? \Carbon\Carbon::parse($product->promo_ends_at)->format('Y-m-d\TH:i') : '') }}">
                                                        <small class="text-muted">Laisser vide = pas de date limite</small>
                                                    </div>
                                                    <div class="col-lg-4 col-sm-6 col-12 d-flex align-items-end">
                                                        <div class="p-3 rounded" style="background:#f0fdf4;border:1px solid #86efac;width:100%;">
                                                            <div style="font-size:12px;color:#6b7280;">Prix normal</div>
                                                            <div style="font-size:18px;font-weight:700;color:#374151;" id="normal-price-display">{{ number_format($product->price, 0, ',', ' ') }} GNF</div>
                                                            <div style="font-size:12px;color:#6b7280;margin-top:4px;">Réduction calculée automatiquement</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit and Cancel Buttons -->
                                    <div class="col-lg-12 mt-3">
                                        <button type="submit" class="btn btn-submit me-2">Mettre à jour</button>
                                        <a href="{{ route('produits.index') }}" class="btn btn-cancel">Annuler</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
    document.addEventListener('DOMContentLoaded', function () {
    const priceInput = document.getElementById('price');
    const pcsInput = document.getElementById('pcs');
    const cartonPriceInput = document.getElementById('price_carton');

    // Vérifie que tous les éléments existent avant d'attacher les écouteurs
    if (priceInput && pcsInput && cartonPriceInput) {
        function updateCartonPrice() {
            const price = parseFloat(priceInput.value) || 0;
            const pcs = parseInt(pcsInput.value) || 0;
            const cartonPrice = price * pcs;
            cartonPriceInput.value = cartonPrice.toFixed(2);
        }

        updateCartonPrice();

        priceInput.addEventListener('input', updateCartonPrice);
        pcsInput.addEventListener('input', updateCartonPrice);
    } else {
        console.warn('Un ou plusieurs champs price, pcs, price_carton sont manquants dans le DOM.');
    }
});

   </script>
        @include('layouts.scripts')
        <script>
            const normalPrice = {{ $product->price }};

            function togglePromoSection(checkbox) {
                document.getElementById('promo-section').style.display = checkbox.checked ? '' : 'none';
                if (!checkbox.checked) {
                    document.getElementById('promo_price').value = '';
                    document.getElementById('promo_ends_at').value = '';
                    document.getElementById('discount-preview').textContent = '';
                }
            }

            function updateDiscount() {
                const promoPrice = parseFloat(document.getElementById('promo_price').value);
                const preview    = document.getElementById('discount-preview');
                if (promoPrice > 0 && promoPrice < normalPrice) {
                    const pct = Math.round((1 - promoPrice / normalPrice) * 100);
                    preview.textContent = `✓ Réduction de ${pct}% (économie : ${(normalPrice - promoPrice).toLocaleString('fr-FR')} GNF)`;
                    preview.style.color = '#16a34a';
                } else if (promoPrice >= normalPrice) {
                    preview.textContent = '⚠ Le prix promo doit être inférieur au prix normal.';
                    preview.style.color = '#dc2626';
                } else {
                    preview.textContent = '';
                }
            }

            // Calcul initial si déjà en promo
            updateDiscount();
        </script>
    </body>
</html>
