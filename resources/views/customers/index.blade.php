<!DOCTYPE html>
<html lang="en">
    @include('layouts.head')
    <body>
        <div id="global-loader">
            <div class="whirly-loader"> </div>
        </div>

        <div class="main-wrapper">
            @include('layouts.header')
            @include('layouts.flash')
            @include('layouts.sidebar')
            <div class="page-wrapper">
                <div class="content">
                    <div class="page-header">
                        <div class="page-title">
                            <h4>Liste Clients</h4>
                            <h6>Gerer vos Clients</h6>
                        </div>
                        <div class="page-btn">
                            <a href="{{ route('customers.create') }}" class="btn btn-added"><img src="assets/img/icons/plus.svg" alt="img" class="me-2">Add Customer</a>
                        </div>
                    </div>
                    @include('layouts.flash')
                    <div class="card">
                        <div class="card-body">
                            <div class="table-top">
                                <div class="search-set">
                                    <div class="search-input">
                                        <a class="btn btn-searchset">
                                            <img src="assets/img/icons/search-white.svg" alt="img">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table  datanew">
                                    <thead>
                                        <tr>
                                            <th>Mark</th>
                                            <th>Nom</th>
                                            <th>Phone</th>
                                            <th>email</th>
                                            <th>Address</th>
                                            <th>Fidelité</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataTable as $item)
                                        <tr>
                                            <td>{{ $item->mark }}</td>
                                            <td>{{ $item->customerName }}</td>
                                            <td>{{ $item->tel }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->address }}</td>
                                            <td>
                                                <div class="status-toggle d-flex justify-content-between align-items-center">
                                                    <input type="checkbox" id="user1" class="check" {{ ($item->fidelite == 1) ?  'checked' : ''}}>
                                                    <label for="user1" class="checktoggle">checkbox</label>
                                                </div>
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
    </body>
</html>

