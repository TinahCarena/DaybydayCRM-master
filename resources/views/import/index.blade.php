@extends('layouts.master')

@section("content")
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg rounded">
                <div class="card-header bg-primary text-white text-center">
                    <h3><i class="fas fa-file-import"></i> Importer des données</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('import.upload') }}" method="POST" enctype="multipart/form-data" class="p-3 border rounded bg-light">
                        @csrf
                        <div class="form-group">
                            <label for="file" class="h5"><i class="fas fa-file-csv"></i> Fichier CSV 1 (Projets)</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                            
                            <label for="file2" class="h5 mt-3"><i class="fas fa-file-csv"></i> Fichier CSV 2 (Tâches)</label>
                            <input type="file" class="form-control @error('file2') is-invalid @enderror" id="file2" name="file2" required>
                            
                            <label for="file3" class="h5 mt-3"><i class="fas fa-file-csv"></i> Fichier CSV 3 (Offres)</label>
                            <input type="file" class="form-control @error('file3') is-invalid @enderror" id="file3" name="file3" required>
                            
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                            <i class="fas fa-upload"></i> Importer
                        </button>
                    </form>

                    @if(session('success') || session('error'))
                        <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} mt-4">
                            @if(session('success'))
                                <h5 class="font-weight-bold text-center">
                                    <i class="fas fa-check-circle"></i> Importation réussie
                                </h5>
                            @else
                                <h5 class="font-weight-bold text-center">
                                    <i class="fas fa-exclamation-circle"></i> Erreur lors de l'importation
                                </h5>
                            @endif
                            
                            <div class="text-center mt-3">
                                <p><strong>Fichiers importés :</strong></p>
                                <p>{{ session('file_name') }}</p>
                                <p>{{ session('file_name2') }}</p>
                                <p>{{ session('file_name3') }}</p>
                            </div>
                            
                            @if(session('success'))
                                <div class="text-center mt-3">
                                    @if(session('imported_projects_rows'))
                                        <span class="badge badge-success mr-2">
                                            Projets: {{ session('imported_projects_rows') }} lignes
                                        </span>
                                    @endif
                                    @if(session('imported_project_tasks_rows'))
                                        <span class="badge badge-success mr-2">
                                            Tâches: {{ session('imported_project_tasks_rows') }} lignes
                                        </span>
                                    @endif
                                    @if(session('imported_offers_rows'))
                                        <span class="badge badge-success">
                                            Offres: {{ session('imported_offers_rows') }} lignes
                                        </span>
                                    @endif
                                </div>
                            @endif
                            
                            @if(session('skipped_rows'))
                                <div class="text-center mt-3">
                                    <span class="badge badge-danger">
                                        Lignes en erreur: {{ session('skipped_rows') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(session('import_errors'))
                        <div class="mt-4">
                            <h4 class="text-danger text-center">
                                <i class="fas fa-exclamation-triangle"></i> Erreurs d'import
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Fichier</th>
                                            <th>Ligne</th>
                                            <th>Champ</th>
                                            <th>Erreur</th>
                                            <th>Valeur incorrecte</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(session('import_errors') as $error)
                                            <tr class="table-danger">
                                                <td>{{ $error['source_file'] ?? 'N/A' }}</td>
                                                <td>{{ $error['row']-1 }}</td>
                                                <td>{{ $error['attribute'] }}</td>
                                                <td>
                                                    <ul class="mb-0">
                                                        @foreach($error['errors'] as $message)
                                                            <li>{{ $message }}</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td>{{ $error['values'][$error['attribute']] ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if(session('projects'))
                        <div class="mt-4">
                            <h4 class="text-success text-center">
                                <i class="fas fa-check-circle"></i> Projets importés
                            </h4>
                            <div class="table-responsive">
                                <table id="tableProjects" class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Ligne originale</th>
                                            <th>Nom du projet</th>
                                            <th>Client</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(session('projects') as $project)
                                            <tr>
                                                <td>{{ $project->import_row }}</td>
                                                <td>{{ $project->project_title }}</td>
                                                <td>{{ $project->client_name }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div id="paginationProjects" class="pagination"></div>
                            </div>
                        </div>
                    @endif

                    @if(session('project_tasks'))
                        <div class="mt-4">
                            <h4 class="text-success text-center">
                                <i class="fas fa-check-circle"></i> Tâches importées
                            </h4>
                            <div class="table-responsive">
                                <table id="tableProjectTasks" class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Ligne originale</th>
                                            <th>Nom du projet</th>
                                            <th>Titre de la tâche</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(session('project_tasks') as $project_task)
                                            <tr>
                                                <td>{{ $project_task->import_row }}</td>
                                                <td>{{ $project_task->project_title }}</td>
                                                <td>{{ $project_task->task_title }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div id="paginationProjectTasks" class="pagination"></div>
                            </div>
                        </div>
                    @endif

                    @if(session('offers'))
                        <div class="mt-4">
                            <h4 class="text-success text-center">
                                <i class="fas fa-check-circle"></i> Offres importées
                            </h4>
                            <div class="table-responsive">
                                <table id="tableOffers" class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Ligne originale</th>
                                            <th>Nom du client</th>
                                            <th>Titre du lead</th>
                                            <th>Type</th>
                                            <th>Produit</th>
                                            <th>Prix</th>
                                            <th>Quantité</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(session('offers') as $offer)
                                            <tr>
                                                <td>{{ $offer->import_row }}</td>
                                                <td>{{ $offer->client_name }}</td>
                                                <td>{{ $offer->lead_title }}</td>
                                                <td>{{ $offer->type }}</td>
                                                <td>{{ $offer->produit }}</td>
                                                <td>{{ $offer->prix }}</td>
                                                <td>{{ $offer->quantite }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div id="paginationOffers" class="pagination"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    function setupPagination(tableId, paginationId, rowsPerPage = 10) {
        let table = document.getElementById(tableId);
        if (!table) return;
        
        let pagination = document.getElementById(paginationId);
        if (!pagination) return;
        
        let rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
        let totalRows = rows.length;
        let totalPages = Math.ceil(totalRows / rowsPerPage);
        let currentPage = 1;

        function showPage(page) {
            let start = (page - 1) * rowsPerPage;
            let end = start + rowsPerPage;
            for (let i = 0; i < totalRows; i++) {
                rows[i].style.display = (i >= start && i < end) ? "table-row" : "none";
            }
        }

        function renderPagination() {
            pagination.innerHTML = "";
            let ul = document.createElement("ul");
            ul.classList.add("pagination-list");

            // Previous button
            if (totalPages > 1) {
                let prevLi = document.createElement("li");
                prevLi.textContent = "«";
                prevLi.classList.add("page-item");
                if (currentPage === 1) {
                    prevLi.classList.add("disabled");
                }
                prevLi.addEventListener("click", function () {
                    if (currentPage > 1) {
                        currentPage--;
                        showPage(currentPage);
                        updateActivePage();
                    }
                });
                ul.appendChild(prevLi);
            }

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                let li = document.createElement("li");
                li.textContent = i;
                li.classList.add("page-item");
                if (i === currentPage) {
                    li.classList.add("active");
                }
                li.addEventListener("click", function () {
                    currentPage = i;
                    showPage(currentPage);
                    updateActivePage();
                });
                ul.appendChild(li);
            }

            // Next button
            if (totalPages > 1) {
                let nextLi = document.createElement("li");
                nextLi.textContent = "»";
                nextLi.classList.add("page-item");
                if (currentPage === totalPages) {
                    nextLi.classList.add("disabled");
                }
                nextLi.addEventListener("click", function () {
                    if (currentPage < totalPages) {
                        currentPage++;
                        showPage(currentPage);
                        updateActivePage();
                    }
                });
                ul.appendChild(nextLi);
            }

            pagination.appendChild(ul);
        }

        function updateActivePage() {
            let items = pagination.querySelectorAll(".page-item");
            items.forEach((item, index) => {
                if (item.textContent === "«" || item.textContent === "»") return;
                item.classList.toggle("active", parseInt(item.textContent) === currentPage);
            });
        }

        showPage(currentPage);
        renderPagination();
    }

    // Initialize pagination for all tables
    setupPagination("tableProjects", "paginationProjects");
    setupPagination("tableProjectTasks", "paginationProjectTasks");
    setupPagination("tableOffers", "paginationOffers");
});
</script>
<style>
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.pagination-list {
    list-style: none;
    padding: 0;
    display: flex;
    gap: 5px;
}

.page-item {
    padding: 5px 10px;
    border: 1px solid #007bff;
    color: #007bff;
    cursor: pointer;
    border-radius: 5px;
    min-width: 35px;
    text-align: center;
}

.page-item:hover:not(.disabled) {
    background-color: #007bff;
    color: white;
}

.page-item.active {
    background-color: #007bff;
    color: white;
}

.page-item.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.table-responsive {
    margin-bottom: 20px;
}
</style>
@endsection