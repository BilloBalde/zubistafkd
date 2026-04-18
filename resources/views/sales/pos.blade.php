<!DOCTYPE html>
<html lang="en">
    @include('layouts.head')
    <body>
        <style>
            .total {
                font-size: 18px;
                font-weight: bold;
                margin-top: 20px;
            }

            .product-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin: 10px 0;
                padding: 10px;
                border: 1px solid #ccc;
            }

            .product-row .quantity-set,
            .product-row .price,
            .product-row .row-total {
                margin-right: 10px;
            }

            .delete-btn {
                background-color: red;
                color: white;
                padding: 5px 10px;
                border: none;
                cursor: pointer;
            }

            .delete-btn:hover {
                background-color: darkred;
            }

            .low-stock {
                border: 2px solid red; /* Highlight low-stock items with a red border */
            }

            .low-stock-alert {
                color: red;
                font-weight: bold;
                font-size: 14px;
                margin-top: 5px;
                display: block;
            }
        </style>
        <div id="global-loader">
            <div class="whirly-loader"> </div>
        </div>
        <div class="main-wrapper">
            @include('layouts.header')
            <div class="page-wrapper ms-0">
                <div class="content">
                    <div class="row">
                        <div class="col-lg-7 col-sm-12 tabs_wrapper">
                            <div class="page-header ">
                                <div class="page-title">
                                    <h4>POS</h4>
                                </div>
                            </div>
                            <div class="form-group position-relative mb-3" style="max-width: 400px;">
                                <input type="text" class="form-control" id="posSearchInput" placeholder="Rechercher produit ou catégorie...">
                                <span id="clearSearchBtn" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer; display:none;">
                                    <i class="fa fa-times-circle text-muted"></i>
                                </span>
                            </div>
                            @include('layouts.flash')
                            <ul class="tabs owl-carousel owl-theme owl-product border-0">
                                @foreach ($categories as $index => $item)
                                <li class="tab-item {{ $index == 0 ? 'active' : '' }}" data-tab="{{ $item->slug }}">
                                    <div class="product-details">
                                        <h6>{{ $item->slug }}</h6>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            <div class="tabs_container">
                                @foreach ($categories as $index => $category)
                                <div class="tab_content {{ $index == 0 ? 'active' : '' }}" data-tab="{{ $category->slug }}">
                                    <div class="row">
                                        @foreach ($produits->filter(fn($p) => $p->categories->contains('slug', $category->slug)) as $dataItem)
                                        @php
                                            // Calculate the quantity
                                            if ($userStoreId) {
                                                $store = $dataItem->stores()->where('store_id', $userStoreId)->first();
                                                $quantity = $store ? $store->pivot->quantity : 0;
                                            } else {
                                                $quantity = $dataItem->stores->sum('pivot.quantity');
                                            }
                                            // Désactiver uniquement si stock == 0
                                            $isOutOfStock = $quantity == 0;
                                        @endphp
                                           <div class="col-lg-3 col-sm-3 col-6 d-flex product-item {{ $isOutOfStock ? 'low-stock disabled-product' : '' }}"
                                                data-numeroFacture="{{ $numeroFacture }}"
                                                data-sku="{{ $dataItem->sku }}"
                                                data-id="{{ $dataItem->id }}"
                                                data-price="{{ $dataItem->price }}"
                                                data-low-stock="{{ $isOutOfStock ? 'true' : 'false' }}"
                                            >
                                         
                                            <div class="productset flex-fill">
                                                <div class="productsetimg">
                                                    <img src="{{ asset('products/' . $dataItem->image) }}" alt="img" style="height: 170px">
                                                    <h6>Qtity: {{ $quantity }}</h6>
                                                    <div class="check-product">
                                                        <i class="fa fa-check"></i>
                                                    </div>
                                                </div>
                                                <div class="productsetcontent">
                                                    <h6>
                                                        @foreach ($dataItem->categories as $category)
                                                        {{ $category->slug }}
                                                        @endforeach
                                                    </h6>
                                                    <h5>{{ $dataItem->libelle }}</h5>
                                                    <h4>{{ $dataItem->sku }}</h4>
                                                    <h4>price unit{{ $dataItem->price }}GNF</h4>
                                                    <h4>Prix Carton{{ $dataItem->price_carton }}GNF</h4>

                                                    @if ($isOutOfStock)
                                                        <h6 class="text-danger" style="font-size: 14px; margin-top: 5px;">
                                                            ⚠️ Rupture de stock
                                                        </h6>
                                                    @else
                                                        <h6>Qtity: {{ $quantity }}</h6>
                                                    @endif
                                                </div>
                                               
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-lg-5 col-sm-12 ">
                            <div class="order-list">
                                <div class="orderid">
                                    <h4>Order List</h4>
                                    <h5>Invoice id : #{{ $numeroFacture }}</h5>
                                </div>
                               <div class="actionproducts">
                                <ul>
                                    <li>
                                        <a href="{{ route('home') }}">
                                            <img src="{{ asset('assets/img/icons/return1.svg') }}" alt="img">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            </div>
                            <div class="card card-order">
                                <form action="{{ route('sales.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="card-body pt-0">
                                        <div class="product-table" id="product-list">
                                            <h5>Products List</h5>
                                            
                                        </div>
                                    </div>
                                    <div class="split-card">
                                    </div>
                                    <div class="card-body pt-0 pb-2">
                                        <div class="setvalue">
                                            <ul>
                                                <li>
                                                    <h5>Subtotal </h5>
                                                    <h4 id="subtotal">0.00</h4>
                                                </li>
                                                <li>
                                                    <h5>Autres Frais </h5>
                                                    <input type="text" id="tax" style="width: 60px" class="form-control" value="0">
                                                    {{-- <h6><span id="tax">0.00$</span></h6> --}}
                                                </li>
                                                <li>
                                                    <h5>Total</h5>
                                                    <h4 id="finalTotal">0.00</h4>
                                                    <input type="hidden" id="final_total" name="final_total">
                                                </li>
                                            </ul>
                                        </div>
                                        <h5>Information Customer</h5>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="select-split form-group">
                                                        <div class="select-group w-100">
                                                            <select name="customer_id" id="customer_id" class="form-control">
                                                                <option value="">Sélectionner Client</option> <!-- Ajout d'une valeur vide -->
                                                                @foreach ($customers as $item)
                                                                    <option value="{{ $item->id }}" {{ old('customer_id') == $item->id ? 'selected' : '' }}>
                                                                        {{ $item->customerName.' '.$item->mark }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12" id="addCustomerButton">
                                                    <a href="javascript:void(0);" class="btn btn-adds" data-bs-toggle="modal" data-bs-target="#create"><i class="fa fa-plus me-2"></i>Add Customer</a>
                                                </div>
                                                <div class="col-lg-12">
                                                    <input type="hidden" name="numeroFacture" value="{{ $numeroFacture }}">
                                                    <div class="select-split form-group">
                                                        <div class="select-group w-100">
                                                            <select name="store_id" id="store_id" class="form-control">
                                                                <option value="">Sélectionner Magasin</option> <!-- Ajout d'une valeur vide -->
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
                                                </div>
                                            </div>
                                        </div>
                                        <div class="split-card">
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6 col-lg-6 col-12">
                                                <div class="form-group">
                                                    <label for="avance">Avance Paid</label>
                                                    <input type="text" id="avance" name="avance" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-lg-6 col-12">
                                                <div class="form-group">
                                                    <label for="paid_by">Method Payement</label>
                                                    <select name="paid_by" id="paid_by" class="form-control">
                                                        <option value="cash">Cash</option>
                                                        <option value="check">Card</option>
                                                        <option value="orange money">OM</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="notes">Notes</label>
                                                <textarea name="notes" id="notes" class="form-control"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary form-control">Confirm Command
                                                </button>
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
        <div class="modal fade" id="create" tabindex="-1" aria-labelledby="create" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Creer Client</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addCustomerForm">
                            <meta name="csrf-token" content="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label for="customerName">Customer Name</label>
                                        <input type="text" name="customerName" id="customerName" required>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label for="mark">Customer Mark</label>
                                        <input type="text" name="mark" id="mark" required>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label for="tel">Phone</label>
                                        <input type="text" name="tel" id="tel">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-sm-12 col-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea name="address" id="address" cols="30" rows="10"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-submit me-2">Submit</button>
                                <a class="btn btn-cancel" data-bs-dismiss="modal">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="delete" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Order Deletion</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="delete-order">
                            <img src="assets/img/icons/close-circle1.svg" alt="img">
                        </div>
                        <div class="para-set text-center">
                            <p>The current order will be deleted as no payment has been <br> made so far.</p>
                        </div>
                        <div class="col-lg-12 text-center">
                            <a class="btn btn-danger me-2">Yes</a>
                            <a class="btn btn-cancel" data-bs-dismiss="modal">No</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .check-product {
                display: none; /* Hide checkbox initially */
                position: absolute;
                top: 10px;
                right: 10px;
                background-color: #007bff;
                color: white;
                width: 20px;
                height: 20px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                font-weight: bold;
            }

            .product-item.selected .check-product {
                display: flex; /* Show checkbox when selected */
            }
            .disabled-product {
    pointer-events: none;
    opacity: 0.5;
    cursor: not-allowed;
}
        </style>
        @include('layouts.scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                
                const productItems = document.querySelectorAll('.product-item');
                const productList = document.getElementById('product-list');
                const totalPriceElement = document.getElementById('totalPrice');
                const taxRate = 0.10;
                let i = 0;

                productItems.forEach(item => {
                    item.addEventListener('click', function() {
                        i++;
                        // const isLowStock = item.getAttribute('data-low-stock') === 'true';
                        // if (isLowStock) {
                        //     alert(`⚠️ Ce produit est en rupture de stock ou a atteint le seuil minimum. Vous ne pouvez pas l'ajouter.`);
                        //     return; // stop ici
                        // }
                        const sku = item.getAttribute('data-sku');
                        const product_id = item.getAttribute('data-id');
                        const numeroFacture = item.getAttribute('data-numeroFacture');
                        const productPrice = parseFloat(item.getAttribute('data-price')) || 0;
                        const libelle = item.querySelector('.productsetcontent h5').textContent;
                        const imageUrl = item.querySelector('.productsetimg img').src;

                        const existingRow = document.querySelector(`#product-list li[data-sku="${sku}"]`);
                        if (existingRow) {
                            existingRow.remove();
                            updateTotalPrice();
                            updateTotalCheckout();
                            return;
                        }

                        const newRow = document.createElement('li');
                        newRow.classList.add('product-row');
                        newRow.setAttribute('data-sku', sku);
                        newRow.innerHTML = `
                            <ul class="product-lists">
                                <li>
                                    <div class="productimg">
                                        <div class="productimgs">
                                            <img src="${imageUrl}" alt="img">
                                        </div>
                                        <div class="productcontet">
                                            <h4>${libelle}</h4>
                                            <div class="productlinkset">
                                                <h5>${sku}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <input type="hidden" name="sales[${i}][product_id]" value="${product_id}">
                                    <div class="quantity-set form-group">
                                        <h6>Qty</h6>
                                        <input type="number" style="width:60px" name="sales[${i}][quantity]" value="1" class="quantity-field form-control">
                                    </div>
                                </li>
                                <li>
                                    <div class="price form-group">
                                        <h6>Prix</h6>
                                        <input type="text" style="width:100px" name="sales[${i}][prix]" value="${productPrice}" class="price-field form-control">
                                        <div class="text-danger price-error mt-1" style="font-size: 0.9rem; display: none;"></div>
                                    </div>
                                </li>
                                <li>
                                    <div class="row-total form-group">
                                        <h6>Total</h6>
                                        <input type="text" name="sales[${i}][total_price]" class="form-control row-total-price" readonly>
                                    </div>
                                </li>  
                            </ul>
                        `;
                        productList.appendChild(newRow);

                        const quantityField = newRow.querySelector('.quantity-field');
                        const priceField = newRow.querySelector('.price-field');
                        const priceError = newRow.querySelector('.price-error');

                        quantityField.addEventListener('input', updateRowTotal);
                        priceField.addEventListener('input', function () {
                            const enteredPrice = parseFloat(priceField.value) || 0;

                            if (enteredPrice < productPrice) {
                                priceError.textContent = `Le prix ne peut pas être inférieur à ${productPrice.toFixed(2)} GNF`;
                                priceError.style.display = 'block';
                                priceField.classList.add('is-invalid');
                            } else {
                                priceError.textContent = '';
                                priceError.style.display = 'none';
                                priceField.classList.remove('is-invalid');
                            }

                            updateRowTotal();
                        });

                        function updateRowTotal() {
                            const quantity = parseFloat(quantityField.value) || 0;
                            const price = parseFloat(priceField.value) || 0;
                            const rowTotal = quantity * price;
                            newRow.querySelector('.row-total-price').value = rowTotal.toFixed(2);
                            updateTotalPrice();
                        }

                        updateRowTotal();
                        updateTotalPrice();
                    });
                });

                function updateTotalPrice() {
                    let subtotal = 0;
                    let total_items = 0;
                    const rows = document.querySelectorAll('.product-row');
                    rows.forEach(row => {
                        const rowTotal = parseFloat(row.querySelector('.row-total-price').value) || 0;
                        subtotal += rowTotal;
                        total_items++;
                    });

                    const subtotalElement = document.getElementById('subtotal');
                    const taxElement = document.getElementById('tax');

                    if (subtotalElement && taxElement) {
                        subtotalElement.textContent = `${subtotal.toFixed(2)}`;
                        const taxAmount = parseFloat(taxElement.value) || 0;
                        const finalTotal = subtotal + taxAmount;
                        const finalTotalElement = document.getElementById('finalTotal');
                        const totalAmountElement = document.getElementById('final_total');
                        totalAmountElement.value = finalTotal;
                        finalTotalElement.textContent = finalTotal.toFixed(2);
                    }
                }

                document.getElementById('tax').addEventListener('input', updateTotalPrice);

                const addCustomerButton = document.getElementById('addCustomerButton');
                const selectCustomer = document.getElementById('customer_id');
                showHideCustomerButton();

                selectCustomer.addEventListener('change', function () {
                    showHideCustomerButton();
                });

                function showHideCustomerButton() {
                    if (!selectCustomer.value || selectCustomer.value === 'Selectionner Client') {
                        addCustomerButton.style.display = 'block';
                    } else {
                        addCustomerButton.style.display = 'none';
                    }
                }

                const addCustomerModal = new bootstrap.Modal(document.getElementById('create'));
                document.getElementById('addCustomerForm').addEventListener('submit', function (e) {
                    e.preventDefault();
                    let formData = new FormData(this);

                    fetch('{{ route("pos.storeCustomer") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const customerSelect = document.getElementById('customer_id');
                            let newOption = document.createElement('option');
                            newOption.value = data.customer.id;
                            newOption.textContent = `${data.customer.customerName} ${data.customer.mark}`;
                            customerSelect.appendChild(newOption);
                            customerSelect.value = data.customer.id;
                            showHideCustomerButton();
                            addCustomerModal.hide();
                            document.getElementById('addCustomerForm').reset();
                        } else {
                            console.log('Erreur lors de l’ajout du client.');
                        }
                    })
                    .catch(error => console.error('Erreur:', error));
                });

                // Bloquer la soumission du formulaire si erreurs de prix
                document.getElementById('soumission').addEventListener('click', function (e) {
                    const invalidInputs = document.querySelectorAll('.is-invalid');
                    if (invalidInputs.length > 0) {
                        e.preventDefault();
                        alert("Veuillez corriger les prix invalides avant de soumettre.");
                    }
                });
            });
        </script>
       
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('posSearchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const tabContents = document.querySelectorAll('.tab_content');
        const tabItems = document.querySelectorAll('.tab-item');

        // Fonction principale de réinitialisation
        function resetToInitialState() {
            const savedTab = localStorage.getItem('activeTab');

            tabItems.forEach(tab => tab.classList.remove('active'));
            tabContents.forEach(tab => {
                tab.classList.remove('active');
                tab.style.display = 'none';
            });

            let targetTab = tabItems[0];
            let targetContent = tabContents[0];

            if (savedTab) {
                const savedTabItem = document.querySelector(`.tab-item[data-tab="${savedTab}"]`);
                const savedContent = document.querySelector(`.tab_content[data-tab="${savedTab}"]`);
                if (savedTabItem && savedContent) {
                    targetTab = savedTabItem;
                    targetContent = savedContent;
                }
            }

            targetTab.classList.add('active');
            targetContent.classList.add('active');
            targetContent.style.display = 'block';
        }

        // Gestion de la recherche
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            clearSearchBtn.style.display = searchTerm ? 'inline' : 'none';

            tabContents.forEach(tab => {
                let hasVisibleProducts = false;
                const products = tab.querySelectorAll('.product-item');

                products.forEach(product => {
                    const libelle = product.querySelector('.productsetcontent h5')?.textContent.toLowerCase() || '';
                    const sku = product.getAttribute('data-sku')?.toLowerCase() || '';
                    const cat = product.querySelector('.productsetcontent h6')?.textContent.toLowerCase() || '';

                    const match = libelle.includes(searchTerm) || sku.includes(searchTerm) || cat.includes(searchTerm);
                    product.style.display = match ? 'flex' : 'none';
                    if (match) hasVisibleProducts = true;
                });

                tab.style.display = hasVisibleProducts ? 'block' : 'none';
                tab.classList.remove('active');
            });

            // On enlève la classe active des onglets pour empêcher l'affichage par défaut
            tabItems.forEach(tab => tab.classList.remove('active'));
        });

        // Bouton pour vider la recherche
        clearSearchBtn.addEventListener('click', function () {
            searchInput.value = '';
            clearSearchBtn.style.display = 'none';
            resetToInitialState();
            searchInput.focus();
        });

        // Gestion du clic sur les onglets (catégories)
        tabItems.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.getAttribute('data-tab');

                // Sauvegarde de l'onglet actif
                localStorage.setItem('activeTab', target);

                // Réinitialisation des classes
                tabItems.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => {
                    c.classList.remove('active');
                    c.style.display = 'none';
                });

                // Activation du bon onglet
                tab.classList.add('active');
                const contentToShow = document.querySelector(`.tab_content[data-tab="${target}"]`);
                if (contentToShow) {
                    contentToShow.classList.add('active');
                    contentToShow.style.display = 'block';
                }
            });
        });

        // Initialisation
        resetToInitialState();
    });
</script>
<script>
  
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.product-entry').forEach(entry => {
            const typeSelect = entry.querySelector('.product-type');
            const quantityInput = entry.querySelector('.product-quantity');
            const priceInput = entry.querySelector('.product-price');
            const totalInput = entry.querySelector('.product-total');
            const priceError = entry.querySelector('.price-error');
            const pcs = parseFloat(entry.dataset.pcs);
            const basePrice = parseFloat(entry.dataset.price);
            const cartonPrice = parseFloat(entry.dataset.priceCarton);
            const stockCarton = parseFloat(entry.dataset.stock);
            const discountInput = entry.querySelector('.product-percentage');

            // Initial load
            updatePriceByType();

            // Events
            typeSelect.addEventListener('change', updatePriceByType);
            quantityInput.addEventListener('input', updateTotal);
            priceInput.addEventListener('input', validatePrice);
            if (discountInput) discountInput.addEventListener('input', updateTotal);

            function updatePriceByType() {
                const selectedType = typeSelect.value;
                if (selectedType === 'piece') {
                    priceInput.value = basePrice;
                } else {
                    priceInput.value = cartonPrice;
                }
                validatePrice();
                updateTotal();
            }

            function validatePrice() {
                const enteredPrice = parseFloat(priceInput.value) || 0;
                const selectedType = typeSelect.value;
                const minAllowed = selectedType === 'piece' ? basePrice : cartonPrice;

                if (enteredPrice < minAllowed) {
                    priceError.textContent = `Le prix ne peut pas être inférieur à ${minAllowed} GNF`;
                    priceError.style.display = 'block';
                    priceInput.classList.add('is-invalid');
                } else {
                    priceError.textContent = '';
                    priceError.style.display = 'none';
                    priceInput.classList.remove('is-invalid');
                }
                updateTotal();
            }

            function updateTotal() {
                const qty = parseFloat(quantityInput.value) || 0;
                const selectedType = typeSelect.value;
                const unitPrice = parseFloat(priceInput.value) || 0;
                let reduction = 0;

                if (discountInput && discountInput.value) {
                    reduction = parseFloat(discountInput.value) || 0;
                }

                const total = qty * unitPrice * (1 - (reduction / 100));
                totalInput.value = total.toFixed(2);
            }
        });

        // Bloque la soumission si prix invalide
        document.querySelector('form').addEventListener('submit', function (e) {
            const invalids = document.querySelectorAll('.is-invalid');
            if (invalids.length > 0) {
                e.preventDefault();
                alert("Corrigez les prix invalides avant de soumettre !");
            }
        });
    });
</script>






        <!-- jQuery & Owl Carousel -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">

        <script>
            $(document).ready(function() {
                $(".owl-carousel").owlCarousel({
                    items: 5,
                    loop: false,
                    margin: 10,
                    nav: true
                });

                $(".tab-item").click(function() {
                    var tab = $(this).data("tab");

                    // Remove active class from all tabs
                    $(".tab-item").removeClass("active");
                    $(this).addClass("active");

                    // Show the corresponding content
                    $(".tab_content").removeClass("active");
                    $(".tab_content[data-tab='" + tab + "']").addClass("active");
                });

                // Ensure the first tab is active on load
                $(".tab-item:first").addClass("active");
                $(".tab_content:first").addClass("active");
            });
        </script>
        <script>
            $(document).ready(function() {
                $(".product-item").click(function() {
                    let productId = $(this).attr("data-id");

                    // Toggle selection for all products with the same data-id
                    $(".product-item[data-id='" + productId + "']").toggleClass("selected");
                });
            });
        </script>
    </body>
</html>
