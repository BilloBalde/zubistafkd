<!DOCTYPE html>
<html lang="en">
@include('layouts.head')
<body>
<style>
    .product-row { display: flex; justify-content: space-between; align-items: center; margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
    .product-row .quantity-set,
    .product-row .price,
    .product-row .row-total { margin-right: 10px; }
    .low-stock { border: 2px solid red; }
    .product-item { display: flex; }
    .pagination-controls { margin-top: 15px; }
    .pagination-controls button { margin: 0 5px; }
    .check-product {
        display: none; position: absolute; top: 10px; right: 10px;
        background-color: #007bff; color: white; width: 20px; height: 20px;
        border-radius: 50%; align-items: center; justify-content: center;
        font-size: 14px; font-weight: bold;
    }
    .product-item.selected .check-product { display: flex; }
    .disabled-product { pointer-events: none; opacity: 0.5; cursor: not-allowed; }
</style>

<div id="global-loader">
    <div class="whirly-loader"></div>
</div>

<div class="main-wrapper">
    @include('layouts.header')
    <div class="page-wrapper ms-0">
        <div class="content">
            <div class="row">
                <!-- Partie gauche : Produits -->
                <div class="col-lg-7 col-sm-12 tabs_wrapper">
                    <div class="page-header">
                        <div class="page-title"><h4>POS</h4></div>
                    </div>
                    <div class="form-group position-relative mb-3" style="max-width: 400px;">
                        <input type="text" class="form-control" id="posSearchInput" placeholder="Rechercher produit ou catégorie...">
                        <span id="clearSearchBtn" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer; display:none;">
                            <i class="fa fa-times-circle text-muted"></i>
                        </span>
                    </div>
                    @include('layouts.flash')

                    <!-- Onglets de groupes -->
                    <ul class="tabs owl-carousel owl-theme owl-product border-0">
                        @foreach ($categoryGroups as $i => $group)
                            <li class="tab-item @if($i==0) active @endif" data-tab="{{ $group->base_slug }}">
                                <div class="product-details">
                                    <img src="{{ $group->image }}" alt="{{ $group->name }}" style="width:70px; height:70px; object-fit:cover; border-radius:8px; margin-right:12px;">
                                    <h6>{{ $group->name }}</h6>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Conteneurs de produits -->
                    <div class="tabs_container">
                        @foreach ($categoryGroups as $i => $group)
                            <div class="tab_content @if($i==0) active @endif" data-tab="{{ $group->base_slug }}">
                                <div class="row products-grid">
                                    @foreach ($group->products as $product)
                                        @php
                                            if ($userStoreId) {
                                                $store = $product->stores()->where('store_id', $userStoreId)->first();
                                                $quantity = $store ? $store->pivot->quantity : 0;
                                            } else {
                                                $quantity = $product->stores->sum('pivot.quantity');
                                            }
                                            $isOutOfStock = $quantity == 0;
                                        @endphp
                                        <div class="col-lg-3 col-sm-3 col-6 product-item {{ $isOutOfStock ? 'low-stock disabled-product' : '' }}"
                                             data-sku="{{ $product->sku }}"
                                             data-id="{{ $product->id }}"
                                             data-price="{{ $product->price }}"
                                             data-low-stock="{{ $isOutOfStock ? 'true' : 'false' }}">
                                            <div class="productset flex-fill">
                                                <div class="productsetimg">
                                                    <img src="{{ asset('products/'.$product->image) }}" alt="img" style="height:170px">
                                                    <h6>Qtity: {{ $quantity }}</h6>
                                                    <div class="check-product"><i class="fa fa-check"></i></div>
                                                </div>
                                                <div class="productsetcontent">
                                                    <h6>{{ $product->categories->pluck('slug')->join(', ') }}</h6>
                                                    <h5>{{ $product->libelle }}</h5>
                                                    <h4>{{ $product->sku }}</h4>
                                                    <h4>Unit: {{ $product->price }} GNF</h4>
                                                    <h4>Carton: {{ $product->price_carton }} GNF</h4>
                                                    @if ($isOutOfStock)
                                                        <h6 class="text-danger">⚠️ Rupture de stock</h6>
                                                    @else
                                                        <h6>Qtity: {{ $quantity }}</h6>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <!-- Pagination -->
                                <div class="pagination-controls text-center mt-3">
                                    <button class="btn btn-sm btn-outline-primary prev-page" disabled>← Précédent</button>
                                    <span class="mx-2 page-info">Page 1 / 1</span>
                                    <button class="btn btn-sm btn-outline-primary next-page">Suivant →</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Partie droite : Panier / Commande -->
                <div class="col-lg-5 col-sm-12">
                    <div class="order-list">
                        <div class="orderid">
                            <h4>Order List</h4>
                            <h5>Invoice #{{ $numeroFacture }}</h5>
                        </div>
                        <div class="actionproducts">
                            <ul>
                                <li><a href="{{ route('home') }}"><img src="{{ asset('assets/img/icons/return1.svg') }}" alt="return"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card card-order">
                        <form action="{{ route('sales.store') }}" method="POST" id="posForm">
                            @csrf
                            <div class="card-body pt-0">
                                <div class="product-table" id="product-list">
                                    <h5>Products List</h5>
                                </div>
                            </div>
                            <div class="card-body pt-0 pb-2">
                                <div class="setvalue">
                                    <ul>
                                        <li><h5>Subtotal</h5><h4 id="subtotal">0.00</h4></li>
                                        <li><h5>Autres Frais</h5><input type="text" id="tax" style="width:60px" class="form-control" value="0"></li>
                                        <li><h5>Total</h5><h4 id="finalTotal">0.00</h4><input type="hidden" id="final_total" name="final_total"></li>
                                    </ul>
                                </div>
                                <h5>Information Client</h5>
                                <div class="row">
                                    <div class="col-12">
                                        <select name="customer_id" id="customer_id" class="form-control">
                                            <option value="">Sélectionner Client</option>
                                            @foreach ($customers as $item)
                                                <option value="{{ $item->id }}">{{ $item->customerName . ' ' . $item->mark }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 mt-2" id="addCustomerButton">
                                        <a href="javascript:void(0);" class="btn btn-adds" data-bs-toggle="modal" data-bs-target="#create"><i class="fa fa-plus me-2"></i>Add Customer</a>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <input type="hidden" name="numeroFacture" value="{{ $numeroFacture }}">
                                        <select name="store_id" class="form-control">
                                            <option value="">Sélectionner Magasin</option>
                                            @if ($userStoreId)
                                                <option value="{{ $userStoreId }}" selected>{{ App\Models\Store::find($userStoreId)->store_name }}</option>
                                            @else
                                                @foreach ($boutiques as $item)
                                                    <option value="{{ $item->id }}">{{ $item->store_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-sm-6">
                                        <label>Avance Paid</label>
                                        <input type="text" name="avance" class="form-control">
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Payment Method</label>
                                        <select name="paid_by" class="form-control">
                                            <option value="cash">Cash</option>
                                            <option value="check">Card</option>
                                            <option value="orange money">OM</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <label>Notes</label>
                                        <textarea name="notes" class="form-control"></textarea>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <button type="submit" class="btn btn-primary form-control">Confirmer la commande</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout Client -->
<div class="modal fade" id="create" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Créer Client</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm">
                    @csrf
                    <div class="row">
                        <div class="col-4"><label>Nom</label><input type="text" name="customerName" class="form-control" required></div>
                        <div class="col-4"><label>Marque</label><input type="text" name="mark" class="form-control" required></div>
                        <div class="col-4"><label>Téléphone</label><input type="text" name="tel" class="form-control"></div>
                        <div class="col-12 mt-2"><label>Adresse</label><textarea name="address" class="form-control" rows="3"></textarea></div>
                    </div>
                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-submit me-2">Enregistrer</button>
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts.scripts')
<link rel="stylesheet" href="{{ asset('assets/plugins/owlcarousel/owl.carousel.min.css') }}">
<script src="{{ asset('assets/plugins/owlcarousel/owl.carousel.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ---- Configuration ----
    const PRODUCTS_PER_PAGE = 12;

    // ---- Variables globales ----
    const tabItems       = document.querySelectorAll('.tab-item');
    const tabContents    = document.querySelectorAll('.tab_content');
    const searchInput    = document.getElementById('posSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const productList    = document.getElementById('product-list');
    const subtotalEl     = document.getElementById('subtotal');
    const taxEl          = document.getElementById('tax');
    const finalTotalEl   = document.getElementById('finalTotal');
    const finalTotalInput = document.getElementById('final_total');
    let lineIndex        = 0;

    // ---- Pagination par groupe ----
    const paginationState = {};
    tabContents.forEach(tab => {
        const slug = tab.getAttribute('data-tab');
        const allItems = Array.from(tab.querySelectorAll('.product-item'));
        paginationState[slug] = {
            currentPage: 1,
            allItems: allItems
        };
    });

    function showPage(slug, page) {
        const state = paginationState[slug];
        const tab = document.querySelector(`.tab_content[data-tab="${slug}"]`);
        if (!state || !tab) return;

        const total = Math.max(1, Math.ceil(state.allItems.length / PRODUCTS_PER_PAGE));
        page = Math.max(1, Math.min(page, total));
        state.currentPage = page;

        const start = (page - 1) * PRODUCTS_PER_PAGE;
        const end   = start + PRODUCTS_PER_PAGE;

        state.allItems.forEach((item, idx) => {
            item.style.display = (idx >= start && idx < end) ? 'flex' : 'none';
        });

        const prevBtn  = tab.querySelector('.prev-page');
        const nextBtn  = tab.querySelector('.next-page');
        const pageInfo = tab.querySelector('.page-info');
        if (prevBtn)  prevBtn.disabled  = (page === 1);
        if (nextBtn)  nextBtn.disabled  = (page >= total);
        if (pageInfo) pageInfo.textContent = `Page ${page} / ${total}`;

        const paginationEl = tab.querySelector('.pagination-controls');
        if (paginationEl) paginationEl.style.display = state.allItems.length > PRODUCTS_PER_PAGE ? '' : 'none';
    }

    // ---- Gestion des onglets ----
    function activateTab(slug) {
        localStorage.setItem('activeTab', slug);
        tabItems.forEach(t => t.classList.remove('active'));
        tabContents.forEach(tab => {
            tab.classList.remove('active');
            tab.style.display = 'none';
        });
        const targetTabItem = document.querySelector(`.tab-item[data-tab="${slug}"]`);
        const targetContent = document.querySelector(`.tab_content[data-tab="${slug}"]`);
        if (targetTabItem) targetTabItem.classList.add('active');
        if (targetContent) {
            targetContent.classList.add('active');
            targetContent.style.display = 'block';
            showPage(slug, 1);
        }
    }

    tabItems.forEach(tab => {
        tab.addEventListener('click', () => activateTab(tab.getAttribute('data-tab')));
    });

    tabContents.forEach(tab => {
        const slug = tab.getAttribute('data-tab');
        tab.querySelector('.prev-page')?.addEventListener('click', () => {
            showPage(slug, paginationState[slug].currentPage - 1);
        });
        tab.querySelector('.next-page')?.addEventListener('click', () => {
            showPage(slug, paginationState[slug].currentPage + 1);
        });
    });

    // ---- Recherche ----
    function restoreTabView() {
        const saved = localStorage.getItem('activeTab');
        const validSlugs = Array.from(tabItems).map(t => t.getAttribute('data-tab'));
        const slug = (saved && validSlugs.includes(saved)) ? saved : (tabItems[0]?.getAttribute('data-tab') || null);
        if (slug) activateTab(slug);
    }

    searchInput?.addEventListener('input', function () {
        const term = this.value.toLowerCase().trim();
        clearSearchBtn.style.display = term ? 'inline' : 'none';
        if (!term) { restoreTabView(); return; }

        tabItems.forEach(t => t.classList.remove('active'));
        tabContents.forEach(tab => {
            let hasMatch = false;
            const products = tab.querySelectorAll('.product-item');
            products.forEach(p => {
                const libelle = p.querySelector('.productsetcontent h5')?.textContent.toLowerCase() || '';
                const sku = p.getAttribute('data-sku')?.toLowerCase() || '';
                const cat = p.querySelector('.productsetcontent h6')?.textContent.toLowerCase() || '';
                const match = libelle.includes(term) || sku.includes(term) || cat.includes(term);
                p.style.display = match ? 'flex' : 'none';
                if (match) hasMatch = true;
            });
            tab.style.display = hasMatch ? 'block' : 'none';
            tab.querySelector('.pagination-controls').style.display = 'none';
        });
    });

    clearSearchBtn?.addEventListener('click', () => {
        searchInput.value = '';
        clearSearchBtn.style.display = 'none';
        restoreTabView();
        searchInput.focus();
    });

    // ---- Initialisation des onglets ----
    restoreTabView();

    // ---- Panier : Ajout / Suppression / Calcul total ----
    function updateTotalPrice() {
        let subtotal = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            subtotal += parseFloat(row.querySelector('.row-total-price')?.value) || 0;
        });
        if (subtotalEl) subtotalEl.textContent = subtotal.toFixed(2);
        const tax = parseFloat(taxEl?.value) || 0;
        const finalTotal = subtotal + tax;
        if (finalTotalEl) finalTotalEl.textContent = finalTotal.toFixed(2);
        if (finalTotalInput) finalTotalInput.value = finalTotal;
    }

    taxEl?.addEventListener('input', updateTotalPrice);

    document.addEventListener('click', function (e) {
        const productItem = e.target.closest('.product-item');
        if (!productItem || productItem.classList.contains('disabled-product')) return;

        const sku = productItem.getAttribute('data-sku');
        const id = productItem.getAttribute('data-id');
        const price = parseFloat(productItem.getAttribute('data-price')) || 0;
        const libelle = productItem.querySelector('.productsetcontent h5')?.textContent || '';
        const imageUrl = productItem.querySelector('.productsetimg img')?.src || '';

        // Si déjà présent, on retire la ligne
        const existingRow = productList.querySelector(`li[data-sku="${sku}"]`);
        if (existingRow) {
            existingRow.remove();
            productItem.classList.remove('selected');
            updateTotalPrice();
            return;
        }

        // Ajouter la ligne dans le panier
        lineIndex++;
        const li = document.createElement('li');
        li.className = 'product-row';
        li.setAttribute('data-sku', sku);
        li.innerHTML = `
            <ul class="product-lists" style="display:flex; align-items:center; width:100%">
                <li>
                    <div class="productimg" style="display:flex; gap:10px;">
                        <img src="${imageUrl}" width="50" alt="">
                        <div>
                            <strong>${libelle}</strong><br>
                            <small>${sku}</small>
                        </div>
                    </div>
                </li>
                <li>
                    <input type="hidden" name="sales[${lineIndex}][product_id]" value="${id}">
                    <input type="number" name="sales[${lineIndex}][quantity]" value="1" min="1" class="quantity-field form-control" style="width:70px">
                </li>
                <li>
                    <input type="text" name="sales[${lineIndex}][prix]" value="${price}" class="price-field form-control" style="width:100px">
                </li>
                <li>
                    <input type="text" name="sales[${lineIndex}][total_price]" class="row-total-price form-control" readonly style="width:100px">
                </li>
            </ul>
        `;
        productList.appendChild(li);

        const qtyInput = li.querySelector('.quantity-field');
        const priceInput = li.querySelector('.price-field');
        const totalInput = li.querySelector('.row-total-price');

        function computeRow() {
            const qty = parseFloat(qtyInput.value) || 0;
            const prx = parseFloat(priceInput.value) || 0;
            totalInput.value = (qty * prx).toFixed(2);
            updateTotalPrice();
        }

        qtyInput.addEventListener('input', computeRow);
        priceInput.addEventListener('input', computeRow);
        computeRow();

        productItem.classList.add('selected');
    });

    // ---- Client : affichage bouton ajout si aucun client sélectionné ----
    const customerSelect = document.getElementById('customer_id');
    const addCustBtn = document.getElementById('addCustomerButton');
    const toggleAddBtn = () => {
        if (addCustBtn) addCustBtn.style.display = (customerSelect && customerSelect.value === '') ? 'block' : 'none';
    };
    customerSelect?.addEventListener('change', toggleAddBtn);
    toggleAddBtn();

    // ---- Ajout client via AJAX ----
    const addCustForm = document.getElementById('addCustomerForm');
    addCustForm?.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('{{ route("pos.storeCustomer") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const opt = new Option(`${data.customer.customerName} ${data.customer.mark}`, data.customer.id);
                customerSelect.add(opt);
                customerSelect.value = data.customer.id;
                toggleAddBtn();
                bootstrap.Modal.getInstance(document.getElementById('create')).hide();
                addCustForm.reset();
            } else {
                alert('Erreur lors de la création du client');
            }
        })
        .catch(err => console.error(err));
    });

    // ---- Owl Carousel (onglets) ----
    if ($(".owl-product").length) {
        $(".owl-product").owlCarousel({
            margin: 10, dots: false, nav: true, loop: false,
            touchDrag: false, mouseDrag: false,
            responsive: { 0: {items:2}, 768: {items:4}, 1170: {items:6} }
        });
    }
});
</script>
</body>
</html>