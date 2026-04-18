<div class="modal fade" id="addpurchase" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter les infos de l'Achat</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('logistics.store') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            {{-- <div class="form-group">
                                <label for="typeLogistic">Type Produit </label>
                                <select class="select" name="typeLogistic" id="typeLogistic" class="form-control">
                                    <option value="">Choose Production </option>
                                    <option value="matelas"> Matelas</option>
                                    <option value="table"> Table</option>
                                    <option value="table"> Chair</option>
                                </select>
                                <span class="text-danger">
                                    <strong id="typeLogistic-error"></strong>
                                </span>
                              
                            </div> --}}
                            {{-- <div class="col-12">
                                <div class="form-group">
                                    <label for="typeLogistic">Type Produit</label>
                                    <select class="form-control" name="typeLogistic" id="typeLogistic">
                                        <option value="">Choose Category</option>
                                        <!-- Dynamically populate the category dropdown -->
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->category_type }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">
                                        <strong id="typeLogistic-error"></strong>
                                    </span>
                                </div>
                            </div> --}}
                           
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="numeroPurchase">Numero Identification</label>
                                <input type="text" name="numeroPurchase" id="numeroPurchase" class="form-control">
                                <span class="text-danger">
                                    <strong id="numeroPurchase-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="store_id">Stock</label>
                                <select class="select" name="store_id" class="form-control">
                                    <option value="">Choisir Stock</option>
                                    @foreach($boutiques as $boutique)
                                    <option value="{{ $boutique->id }}">{{ $boutique->store_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger">
                                    <strong class="store_id-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="quantity">Quantité</label>
                                <input type="text" name="quantity" id="quantity" class="form-control">
                                <span class="text-danger">
                                    <strong id="quantity-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="depense">Dépense Total</label>
                                <input type="text" name="depense" id="depense" class="form-control">
                                <span class="text-danger">
                                    <strong id="depense-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="dateEmis">Date Emis</label>
                                <input type="date" name="dateEmis" id="dateEmis" class="form-control">
                                <span class="text-danger">
                                    <strong id="dateEmis-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="dateFournis">Date Fournis</label>
                                <input type="date" name="dateFournis" id="dateFournis" class="form-control">
                                <span class="text-danger">
                                    <strong id="dateFournis-error"></strong>
                                </span>
                            </div>
                        </div>
                       
                    </div>
                </div>
                <div class="modal-footer ">
                    <button type="submit" class="btn btn-submit">Confirm</button>
                    <button type="reset" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
