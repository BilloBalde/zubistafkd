<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    h3{
        background-color: #373a37;
        color: white;
        padding: 10px;
        text-align: center;
        border: 1px solid #dddddd;
    }
    th {
        background-color: #4CAF50;
        color: white;
        padding: 10px;
        text-align: left;
        border: 1px solid #dddddd;
    }
    td {
        padding: 8px;
        text-align: left;
        border: 1px solid #dddddd;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
</style>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <h3>Tableau recapitulatif de tous les Produits</h3>
            <table class="table  datanew">
                <thead>
                    <tr>
                        <th>Nom Produit</th>
                        <th>Identifiant Stock</th>
                        <th>Category</th>
                        <th>Qtité en Stock</th>
                        <th>Volume (cbm)</th>
                        <th>Image</th>
                        <th>Date d'ajout</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataTable as $dataItem)
                        @php
                            // Calculate the quantity for the connected user's store or total quantity
                            if ($userStoreId) {
                                // For users with role_id 3, we get the quantity from the specific store
                                $store = $dataItem->stores()->where('store_id', $userStoreId)->first();
                                $quantity = $store ? $store->pivot->quantity : 0; // Access pivot quantity
                            } else {
                                // For other roles, we sum the quantity across all stores
                                $quantity = $dataItem->stores->sum('pivot.quantity');
                            }
                        @endphp
                        <tr>
                            <td>{{ $dataItem->libelle }}</td>
                            <td>{{ $dataItem->sku }}</td>
                            <td>
                                @foreach ($dataItem->categories as $category)
                                    {{ $category->slug . ' (' . $category->category_type . ')' }}
                                @endforeach
                            </td>
                            <td>{{ $quantity }}</td>
                            <td>{{ ($dataItem->longueure * $dataItem->largeure * $dataItem->profondeure) * 0.000001 }}</td>
                            <td>
                                <img src="{{ public_path('products/' . $dataItem->image) }}" alt="product" style="width: 150px; height: 150px;">
                            </td>
                            <td>{{ $dataItem->updated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
