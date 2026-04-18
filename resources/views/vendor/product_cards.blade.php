@foreach($products as $key => $product)
    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ 100 * ($key + 1) }}">
        <div class="product-card h-100">
            <div class="product-image">
                <img src="{{ asset('products/' . $product->image) }}" alt="{{ $product->libelle }}">
            </div>
            <div class="product-body">
                <h5>{{ $product->libelle }}</h5>
                <p>{{ $product->description }}</p>
            </div>
        </div>
    </div>
@endforeach