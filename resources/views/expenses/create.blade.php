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
                            <h4>{{ isset($expense) ? 'Modifier Depense' : 'Ajouter Depense' }}</h4>
                            <h6>{{ isset($expense) ? 'Modifier une depense existante' : 'Ajouter une nouvelle depense' }}</h6>
                        </div>
                    </div>
                    @include('layouts.flash')
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ isset($expense) ? route('expenses.update', $expense->id) : route('expenses.store') }}" method="post" id="Register">
                                @csrf
                                @if(isset($expense))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            
                                    <!-- Reference Field -->
                                    <div class="col-lg-4 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="reference">Reference</label>
                                            <input type="text" id="reference" name="reference" class="form-control" value="{{ isset($expense) ? $expense->reference : $ref }}" readonly>
                                            @error('reference')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="exp_mode">Expense Mode</label>
                                            <select id="exp_mode" name="exp_mode" class="form-control" required>
                                                <option value="others" {{ isset($expense) && $expense->exp_mode == 'others' ? 'selected' : '' }}>Others</option>
                                                <option value="ukexpense" {{ isset($expense) && $expense->exp_mode == 'ukexpense' ? 'selected' : '' }}>UK Expense</option>
                                            </select>
                                        </div>
                                    </div>
                            
                                    {{-- <!-- Balance Field (Only Visible for 'others') -->
                                    <div class="col-lg-3 col-sm-6 col-12" id="balance_field" style="display: {{ isset($expense) && $expense->exp_mode == 'ukexpense' ? 'none' : 'block' }};">
                                        <div class="form-group">
                                            <label for="balance">Balance</label>
                                            <input type="text"  id="balance" name="balance" class="form-control" value="{{ isset($expense) ? $expense->balance : old('balance') }}">
                                            @error('balance')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> --}}
                                    <!-- Expense Category Field (Only Visible for 'others') -->
                                    <div class="col-lg-4 col-sm-6 col-12" id="expense_category_field">
                                        <div class="form-group">
                                            <label for="expense_categories_id">Category</label>
                                            <select id="expense_categories_id" name="expense_categories_id" class="form-control">
                                                <option>Select Category</option>
                                                @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ isset($expense) && $expense->expense_categories_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->categoryName }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('expense_categories_id')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                            
                                    <!-- Store Field (Only Visible for 'others') -->
                                    <div class="col-lg-6 col-sm-6 col-12" id="store_field">
                                        <div class="form-group">
                                            <label for="store_id">Store</label>
                                            <select id="store_id" name="store_id" class="form-control">
                                                <option>Select Store</option>
                                                @foreach($stores as $store)
                                                    <option value="{{ $store->id }}" {{ isset($expense) && $expense->store_id == $store->id ? 'selected' : '' }}>
                                                        {{ $store->store_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('store_id')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- Amount Field (Only Visible for 'others') -->
                                    <div class="col-lg-6 col-sm-6 col-12" id="amount_field">
                                        <div class="form-group">
                                            <label for="amount">Amount</label>
                                            <input type="text"  id="amount" name="amount" class="form-control" value="{{ isset($expense) ? $expense->amount : old('amount') }}">
                                            @error('amount')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- Description Field -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" class="form-control">{{ isset($expense) ? $expense->description : old('description') }}</textarea>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                            
                                    <!-- Submit and Cancel Buttons -->
                                    <div class="col-lg-12">
                                        <button type="submit" class="btn btn-submit me-2">Submit</button>
                                        <a href="{{ route('expenses.index') }}" class="btn btn-cancel">Cancel</a>
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
    <script>
        // JavaScript to toggle visibility of fields based on selected exp_mode
        document.getElementById('exp_mode').addEventListener('change', function() {
            var mode = this.value;
            var expenseCategoryField = document.getElementById('expense_category_field');
            var storeField = document.getElementById('store_field');
            var amountField = document.getElementById('amount_field');
            var balanceField = document.getElementById('balance_field');
    
            /* if (mode === 'ukexpense') {
                expenseCategoryField.style.display = 'block';
                storeField.style.display = 'block';
                amountField.style.display = 'none';
                balanceField.style.display = 'block';
            } else {
                expenseCategoryField.style.display = 'block';
                storeField.style.display = 'block';
                amountField.style.display = 'block';
                balanceField.style.display = 'none';
            } */
        });
    
        // Trigger the change event to apply the default visibility when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('exp_mode').dispatchEvent(new Event('change'));
        });
    </script>
</html>
