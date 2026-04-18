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
                            <h4>Liste des Paiements</h4>
                            <h6>Gerer vos Paiements</h6>
                        </div>
                        <div class="page-btn">
                            <a href="{{ route('factures.index') }}"><img src="{{ asset('assets/img/icons/return1.svg') }}" alt="img"></a>
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
                            </div>
                            <div class="card" id="filter_inputs">
                                <div class="card-body pb-0">
                                    <form action="{{ route('payments.index') }}" method="GET"> <!-- Update to GET method -->
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-3 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <select name="facture_id" id="facture_id" class="form-control">
                                                        <option value="">Select Facture</option>
                                                        @foreach ($factures as $item)
                                                        <option value="{{ $item->id }}" {{ request('facture_id') == $item->id ? 'selected' : '' }}>{{ $item->numero_facture }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <select name="paid_by" id="paid_by" class="form-control">
                                                        <option value="">Select Moyen Pay</option>
                                                        <option value="cash" {{ request('paid_by') == 'cash' ? 'selected' : '' }}>cash</option>
                                                        <option value="check" {{ request('paid_by') == 'check' ? 'selected' : '' }}>check</option>
                                                        <option value="orange money" {{ request('paid_by') == 'orange money' ? 'selected' : '' }}>orange money</option>
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
                                                    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Reset</a>
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
                                            <th>Facture</th>
                                            <th>Versement</th>
                                            <th>Total Payé</th>
                                            <th>Reste à Payer</th>
                                            <th>Payeé par</th>
                                            <th>Note</th>
                                            <th>Date Payement</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataTable as $item)
                                        <tr>
                                            <td>{{ $item->numeroFacture }}</td>
                                            <td>{{ $item->versement }}</td>
                                            <td>{{ $item->total_paye }}</td>
                                            <td>{{ $item->reste }}</td>
                                            <td>{{ $item->paid_by }}</td>
                                            <td>{{ $item->note }}</td>
                                            <td>{{ $item->created_at }}</td>
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
    </body>
</html>
