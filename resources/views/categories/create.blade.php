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
                            <h4>Gestion de Categorie</h4>
                            <h6>Ajouter/Modifier Categorie Emballage</h6>
                        </div>
                        <div class="page-btn">
                            <a href="javascript:history.back();" class="btn btn-added"><img src="{{ asset('assets/img/icons/return1.svg') }}" alt="img" class="me-2"></a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ isset($categoryEmballage) ? route('categories.update', $categoryEmballage->id) : route('categories.store') }}" method="post" id="Register" enctype="multipart/form-data">
                                @csrf
                                @if(isset($categoryEmballage))
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-lg-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="slug">Identifiant</label>
                                            <input type="text" id="slug" name="slug" value="{{ old('slug', $categoryEmballage->slug ?? '') }}" class="form-control">
                                            @error('slug')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="slug">Nom Categorie</label>
                                            <input type="text" id="category_type" name="category_type" value="{{ old('category_type', $categoryEmballage->category_type ?? '') }}" class="form-control">
                                            @error('category_type')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                  
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" rows="5" class="form-control">{{ old('description', $categoryEmballage->description?? '') }}</textarea>
                                            @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="image">Image de la catégorie</label>
                                            <input type="file" id="image" name="image" accept="image/*" class="form-control" onchange="previewCatImage(this)">
                                            @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                            @if(isset($categoryEmballage) && $categoryEmballage->image)
                                            <div class="mt-2">
                                                <img id="cat-img-preview" src="{{ asset('categories/' . $categoryEmballage->image) }}" alt="Image actuelle" style="width:100px;height:100px;object-fit:cover;border-radius:8px;">
                                            </div>
                                            @else
                                            <div class="mt-2">
                                                <img id="cat-img-preview" src="" alt="" style="display:none;width:100px;height:100px;object-fit:cover;border-radius:8px;">
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <button type="submit" class="btn btn-submit me-2">{{ isset($categoryEmballage) ? 'Update' : 'Submit' }}</button>
                                        <a href="{{ route('categories.index') }}" class="btn btn-cancel">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.scripts')
        <script>
        function previewCatImage(input) {
            const preview = document.getElementById('cat-img-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(input.files[0]);
            }
        }
        </script>
    </body>
</html>


