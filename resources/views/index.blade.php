@extends('layouts.template')
@section('content')
<div class="content">
    <h3>Global Data</h3>
    <div class="row">
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget">
                <div class="dash-widgetimg">
                    <span><img src="{{ asset('assets/img/icons/dash2.svg') }}" alt="img"></span>
                </div>
                <div class="dash-widgetcontent">
                    <h5><span class="counters" data-count="{{ $total_purchases }}">{{ numberDelimiter($total_purchases) }}</span> FG</h5>
                    <h6>Total Achat</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget dash1">
                <div class="dash-widgetimg">
                    <span><img src="{{ asset('assets/img/icons/dash2.svg') }}" alt="img"></span>
                </div>
                <div class="dash-widgetcontent">
                    <h5><span class="counters" data-count="{{ $total_sales }}">{{ numberDelimiter($total_sales) }}</span> FG</h5>
                    <h6>Total Ventes</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget dash3">
                <div class="dash-widgetimg">
                    <span><img src="{{ asset('assets/img/icons/dash2.svg') }}" alt="img"></span>
                </div>
                <div class="dash-widgetcontent">
                    <h5><span class="counters" data-count="{{ $total_expenses }}">{{ numberDelimiter($total_expenses) }}</span> FG</h5>
                    <h6>Total dépenses</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget dash3">
                <div class="dash-widgetimg">
                    <span><img src="{{ asset('assets/img/icons/dash2.svg') }}" alt="img"></span>
                </div>
                <div class="dash-widgetcontent">
                    <h5><span class="counters" data-count="{{ $gains }}">{{ numberDelimiter($gains) }}</span> FG</h5>
                    <h6>Total gains</h6>
                </div>
            </div>
        </div>
    </div>
    @if(auth()->user()->role_id !== 3)
    @foreach (App\Models\Store::all() as $item)
    <h3>{{ $item->store_name }}</h3>
    @php
    $total_purchases = App\Models\Purchase::where('store_id', $item->id)->selectRaw('SUM(price * quantity) as total')->value('total');
    $total_sales = App\Models\Facture::where('store_id', $item->id)->sum('montant_total');
    $total_expenses = App\Models\Expense::whereHas('store', function($query) use ($item) {
        $query->where('user_id', App\Models\Store::find($item->id)->user_id);
    })->sum('amount');
    $gains = App\Models\Store::where('id', $item->id)->first()->balance;
    // Fetch the total stock transfer value for this store
        $total_stock_transfer = DB::table('stock_transfers')
            ->join('products', 'stock_transfers.product_id', '=', 'products.id')
            ->where('to_store_id', $item->id)
            ->selectRaw('SUM(stock_transfers.quantity * products.price) as total_transfer_value')
            ->value('total_transfer_value');
    @endphp
    <div class="row">
        @if ($item->id != 1)
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget">
                <div class="dash-widgetimg">
                    <span><img src="{{ asset('assets/img/icons/dash2.svg') }}" alt="img"></span>
                </div>
                <div class="dash-widgetcontent">
                    <h5><span class="counters" data-count="{{ $total_purchases }}">{{ numberDelimiter($total_purchases) }}</span> FG</h5>
                    <h6>Total Achat</h6>
                </div>
            </div>
        </div>
          <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget dash3">
                <div class="dash-widgetimg">
                    <span><img src="{{ asset('assets/img/icons/dash2.svg') }}" alt="img"></span>
                </div>
                <div class="dash-widgetcontent">
                    <h5><span class="counters" data-count="{{ $total_stock_transfer }}">{{ numberDelimiter($total_stock_transfer) }}</span> FG</h5>
                    <h6>Total Transfert</h6>
                </div>
            </div>
        </div>
       
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget dash1">
                <div class="dash-widgetimg">
                    <span><img src="{{ asset('assets/img/icons/dash2.svg') }}" alt="img"></span>
                </div>
                <div class="dash-widgetcontent">
                    <h5><span class="counters" data-count="{{ $total_sales }}">{{ numberDelimiter($total_sales) }}</span> FG</h5>
                    <h6>Total Ventes</h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget dash3">
                <div class="dash-widgetimg">
                    <span><img src="{{ asset('assets/img/icons/dash2.svg') }}" alt="img"></span>
                </div>
                <div class="dash-widgetcontent">
                    <h5><span class="counters" data-count="{{ $gains }}">{{ numberDelimiter($gains) }}</span> FG</h5>
                    <h6>Total gains</h6>
                </div>
            </div>
        </div>   
        @endif
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="dash-widget dash3">
                <div class="dash-widgetimg">
                    <span><img src="{{ asset('assets/img/icons/dash2.svg') }}" alt="img"></span>
                </div>
                <div class="dash-widgetcontent">
                    <h5><span class="counters" data-count="{{ $total_expenses }}">{{ numberDelimiter($total_expenses) }}</span> FG</h5>
                    <h6>Total dépenses</h6>
                </div>
            </div>
        </div>   
    </div>
    @endforeach
    @endif
    <div class="row">
        <div class="col-lg-3 col-sm-6 col-12 d-flex">
            <div class="dash-count">
                <div class="dash-counts">
                    <h4>{{ $total_customers }}</h4>
                    <h5>Clients</h5>
                </div>
                <div class="dash-imgs">
                    <i data-feather="user"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 d-flex">
            <div class="dash-count das1">
                <div class="dash-counts">
                    <h4>{{ $total_quantities }}</h4>
                    <h5>Quantité en Stock</h5>
                </div>
                <div class="dash-imgs">
                    <i data-feather="database"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 d-flex">
            <div class="dash-count das2">
                <div class="dash-counts">
                    <h4>{{ $total_purchase_invoices }}</h4>
                    <h5>Factures d'achat</h5>
                </div>
                <div class="dash-imgs">
                    <i data-feather="file-text"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12 d-flex">
            <div class="dash-count das3">
                <div class="dash-counts">
                    <h4>{{ $total_sales_invoices }}</h4>
                    <h5>Factures de vente</h5>
                </div>
                <div class="dash-imgs">
                    <i data-feather="file"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 col-sm-12 col-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Ventes & Achats</h5>
                    <div class="graph-sets">
                        <ul>
                            <li>
                                <span>Ventes</span>
                            </li>
                            <li>
                                <span>Achats</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div id="sales_charts"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-12 col-12 d-flex">
            <div class="card flex-fill">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Produits Recemment Ajoutés</h4>
                    <div class="dropdown">
                        <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false" class="dropset">
                            <i class="fa fa-ellipsis-v"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li>
                                <a href="{{ route('produits.index') }}" class="dropdown-item">Liste des Produits</a>
                            </li>
                            <li>
                                <a href="{{ route('boutiques.create') }}" class="dropdown-item">Ajouter Produit</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive dataview">
                        <table class="table datatable ">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Produit</th>
                                    <th>Prix</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latestPurchases as $item)
                                <tr>
                                    <td>{{ $item->numeroPurchase }}</td>
                                    <td class="productimgname">
                                        <a href="#" class="product-img">
                                            <img src="{{ asset('products/' . $item->productImage) }}" alt="product">
                                        </a>
                                        <a href="#">{{ $item->product->libelle }}</a>
                                    </td>
                                    <td>{{ numberDelimiter($item->price) }} FG</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Version avec graphique -->
<div class="row mt-4">
    <div class="col-lg-6 col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-chart-bar me-2"></i>
                    Top 10 Produits les plus vendus
                </h5>
            </div>
            <div class="card-body">
                <canvas id="topProductsChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-trophy me-2"></i>
                    Classement des produits
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Produit</th>
                                <th>Ventes</th>
                                <th>Chiffre d'affaires</th> <!-- Nouvelle colonne -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts ?? [] as $index => $product)
                            <tr>
                                <td>
                                    @if($index == 0)
                                        <span class="badge bg-warning">🥇 1er</span>
                                    @elseif($index == 1)
                                        <span class="badge bg-secondary">🥈 2ème</span>
                                    @elseif($index == 2)
                                        <span class="badge bg-danger">🥉 3ème</span>
                                    @else
                                        <span class="badge bg-info">{{ $index + 1 }}ème</span>
                                    @endif
                                </td>
                                <td>{{ $product->product->libelle ?? $product->product->name ?? 'N/A' }}</td>
                                <td>{{ number_format($product->total_quantity) }} unités</td>
                                <td>
                                    <strong class="text-success">
                                        {{ number_format($product->total_revenue, 0, ',', ' ') }} GNF
                                    </strong>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total :</th>
                                <th class="text-success">
                                    {{ number_format($topProducts->sum('total_revenue'), 0, ',', ' ') }} GNF
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique des top produits (quantités)
        const ctx = document.getElementById('topProductsChart');
        if (ctx) {
            const topProducts = @json($topProducts ?? []);
            const labels = topProducts.map(p => p.product?.libelle || p.product?.name || 'N/A');
            const quantities = topProducts.map(p => p.total_quantity);
            const revenues = topProducts.map(p => p.total_revenue);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Quantité vendue',
                            data: quantities,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Chiffre d\'affaires (FCFA)',
                            data: revenues,
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            type: 'line', // Pour différencier, on met en ligne
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    let value = context.raw;
                                    if (context.dataset.label.includes('GNF')) {
                                        return label + ': ' + new Intl.NumberFormat('fr-FR').format(value) + ' GNF';
                                    }
                                    return label + ': ' + new Intl.NumberFormat('fr-FR').format(value) + ' unités';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantité vendue'
                            },
                            position: 'left'
                        },
                        y1: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Chiffre d\'affaires (FCFA)'
                            },
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
    <div class="card mb-0">
        <div class="card-body">
            <h4 class="card-title">Ventes Récentes</h4>
            <div class="table-responsive dataview">
                <table class="table datatable ">
                    <thead>
                        <tr>
                            <th>Numero Facture</th>
                            <th>Info Produit</th>
                            <th>Quantité</th>
                            <th>Prix</th>
                            <th>Soustotal</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($latestSales as $item)
                        <tr>
                            <td>{{ $item->numeroFacture }}</td>
                            <td class="productimgname">
                                <a href="" class="product-img">
                                    <img src="{{ asset('products/' . $item->produitImage) }}" alt="product">
                                </a>
                                <a href="">{{ $item->produit }}</a>
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ numberDelimiter($item->price) }} FG</td>
                            <td>{{ numberDelimiter($item->prixTotal) }} FG</td>
                            <td>{{ $item->updated_at }} FG</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        if ($('#sales_charts').length > 0) {
            var options = {
                series: [
                    {
                        name: 'Ventes',
                        data: @json($sales),  // Pass sales data from the controller
                    },
                    {
                        name: 'Achats',
                        data: @json($purchases),  // Pass purchases data from the controller
                    }
                ],
                colors: ['#28C76F', '#EA5455'],
                chart: {
                    type: 'bar',
                    height: 300,
                    stacked: true,
                    zoom: {
                        enabled: true
                    }
                },
                responsive: [
                    {
                        breakpoint: 280,
                        options: {
                            legend: {
                                position: 'bottom',
                                offsetY: 0
                            }
                        }
                    }
                ],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '20%',
                        endingShape: 'rounded'
                    }
                },
                xaxis: {
                    categories: @json($months),  // Pass month names from the controller
                },
                legend: {
                    position: 'right',
                    offsetY: 40
                },
                fill: {
                    opacity: 1
                }
            };

            var chart = new ApexCharts(document.querySelector("#sales_charts"), options);
            chart.render();
        }
    });
</script>
@endsection
