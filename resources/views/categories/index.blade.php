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
                            <h4>Liste des Catégories</h4>
                            <h6>{{ $grouped->count() }} groupes · {{ $grouped->flatten()->count() }} sous-catégories</h6>
                        </div>
                        <div class="page-btn">
                            <a href="{{ route('categories.create') }}" class="btn btn-added">
                                <img src="assets/img/icons/plus.svg" alt="img" class="me-2">Ajouter une Catégorie
                            </a>
                        </div>
                    </div>

                    @include('layouts.flash')

                    @foreach($grouped as $type => $items)
                    <div class="card mb-3">
                        <div class="card-header d-flex align-items-center justify-content-between py-2 px-3"
                             style="background:#f8f9fa;border-bottom:2px solid #e9ecef;cursor:pointer;"
                             data-bs-toggle="collapse"
                             data-bs-target="#group-{{ Str::slug($type) }}"
                             aria-expanded="true">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning text-dark fw-bold" style="font-size:13px;">
                                    {{ $type }}
                                </span>
                                <span class="text-muted small">{{ $items->count() }} sous-catégorie{{ $items->count() > 1 ? 's' : '' }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-muted small"></i>
                        </div>
                        <div class="collapse show" id="group-{{ Str::slug($type) }}">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr style="background:#fff;">
                                                <th class="ps-3">Identifiant</th>
                                                <th>Description</th>
                                                <th class="text-end pe-3">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $cat)
                                            <tr>
                                                <td class="ps-3">
                                                    <span class="fw-semibold text-secondary">{{ $cat->slug }}</span>
                                                </td>
                                                <td>{{ $cat->description }}</td>
                                                <td class="text-end pe-3">
                                                    <a class="me-3" href="{{ route('categories.edit', $cat->id) }}" title="Modifier">
                                                        <img src="assets/img/icons/edit.svg" alt="modifier">
                                                    </a>
                                                    <a class="me-1 deleteButtionItem"
                                                        type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#confirmDeleteModal"
                                                        data-slug="{{ $cat->slug }}"
                                                        data-action="{{ route('categories.destroy', $cat->id) }}">
                                                        <img src="assets/img/icons/delete.svg" alt="supprimer">
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>

        @include('layouts.scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.deleteButtionItem').forEach(btn => {
                    btn.addEventListener('click', function () {
                        document.getElementById('deleteId').textContent = this.dataset.slug;
                        document.getElementById('deleteForm').action = this.dataset.action;
                    });
                });
            });
        </script>
        @include('categories.delete')
    </body>
</html>
