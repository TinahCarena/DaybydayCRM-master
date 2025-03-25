@extends('layouts.master')

@section('content')
    <!-- Page header -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                {{ __('CSV Import') }}
                <small>{{ __('Import data from CSV files') }}</small>
            </h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="icon fa fa-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="icon fa fa-ban"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Import form boxes -->
    <div class="row">
        <!-- Users Import -->
        <div class="col-lg-4">
            <div class="small-box bg-white">
                <div class="inner" style="min-height: 120px">
                    <h3>{{ __('Import') }}</h3>

                    <p>{{ __('Upload a CSV file to import data') }}</p>
        
                    <form action="{{ route('csv.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="users_csv_file" name="csv_file" accept=".csv">
                                <label class="custom-file-label" for="users_csv_file">{{ __('Choose file') }}</label>
                            </div>
                            @error('csv_file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-upload"></i> {{ __('Import Users') }}
                        </button>
                    </form>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-people-outline"></i>
                </div>
              
            </div>
        </div>
        
    <!-- Help Modal -->
    <div class="modal fade" id="csv-help-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">{{ __('CSV Import Help') }}</h4>
                </div>
                <div class="modal-body">
                    <h4>{{ __('CSV Format Requirements') }}</h4>
                    <ul>
                        <li>{{ __('The first row must contain column headers') }}</li>
                        <li>{{ __('Column headers must match the property names in the DTO') }}</li>
                        <li>{{ __('Date format: DD/MM/YYYY (e.g., 25/12/2023)') }}</li>
                        <li>{{ __('Time format: HH:MM (e.g., 14:30)') }}</li>
                        <li>{{ __('DateTime format: YYYY-MM-DD HH:MM:SS (e.g., 2023-12-25 14:30:00)') }}</li>
                        <li>{{ __('Boolean values: true/false, yes/no, 1/0') }}</li>
                        <li>{{ __('Lists/Arrays: comma-separated values (e.g., item1,item2,item3)') }}</li>
                    </ul>
                    
                    <h4>{{ __('Common Issues') }}</h4>
                    <ul>
                        <li>{{ __('Make sure your CSV is properly formatted and uses commas as separators') }}</li>
                        <li>{{ __('Check that date formats match the expected format') }}</li>
                        <li>{{ __('Ensure column headers exactly match the property names') }}</li>
                        <li>{{ __('For large files, the import may take some time to process') }}</li>
                    </ul>
                    
                    <h4>{{ __('Required Fields') }}</h4>
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_users" data-toggle="tab">{{ __('Users') }}</a></li>
                            <li><a href="#tab_products" data-toggle="tab">{{ __('Products') }}</a></li>
                            <li><a href="#tab_clients" data-toggle="tab">{{ __('Clients') }}</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_users">
                                <p>{{ __('Required fields for users:') }}</p>
                                <ul>
                                    <li>name</li>
                                    <li>email</li>
                                    <li>password</li>
                                    <li>primary_number</li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="tab_products">
                                <p>{{ __('Required fields for products:') }}</p>
                                <ul>
                                    <li>name</li>
                                    <li>code</li>
                                    <li>price</li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="tab_clients">
                                <p>{{ __('Required fields for clients:') }}</p>
                                <ul>
                                    <li>company_name</li>
                                    <li>vat_number</li>
                                    <li>contact_email</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip(); //Tooltip on icons top

            $('.popoverOption').each(function () {
                var $this = $(this);
                $this.popover({
                    trigger: 'hover',
                    placement: 'left',
                    container: $this,
                    html: true,
                });
            });
            
            // Update file input label when file is selected
            $('.custom-file-input').change(function() {
                var fileName = $(this).val().split('\\').pop();
                if (fileName) {
                    $(this).siblings('.custom-file-label').text(fileName);
                } else {
                    $(this).siblings('.custom-file-label').text('{{ __("Choose file") }}');
                }
            });
        });
    </script>
    @endpush
@endsection