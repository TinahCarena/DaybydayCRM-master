@extends('layouts.master')

@section('content')
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

    <div class="row">
        <div class="col-lg-4">
            <div class="small-box bg-white">
                <div class="inner" style="min-height: 120px">
                    <h3>{{ __('Import Data') }}</h3>
                    <p>{{ __('Upload CSV files for Clients, Tasks, or Leads') }}</p>
                    
                    <form action="{{ route('csv.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">   
                            <label for="client_csv_file">Clients</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="client_csv_file" name="client" accept=".csv,.txt">
                            </div>
                            @error('client')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="task_csv_file">Tasks</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="task_csv_file" name="task" accept=".csv,.txt">
                            </div>
                            @error('task')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="lead_csv_file">Leads</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="lead_csv_file" name="lead" accept=".csv,.txt">
                            </div>
                            @error('lead')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                                                
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-upload"></i> {{ __('Import') }}
                        </button>
                    </form>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-people-outline"></i>
                </div>
                <a href="#" class="small-box-footer" data-toggle="modal" data-target="#csv-help-modal">
                    {{ __('Need help?') }} <i class="fa fa-question-circle"></i>
                </a>
            </div>
        </div>
    </div>

    @if(session('import_errors') && !empty(session('import_errors')))
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-warning">
                    <h4>Import Errors</h4>
                    <ul>
                        @foreach(session('import_errors') as $error)
                            <li>
                                File: <strong>{{ $error['file'] }}</strong> - 
                                Line: <strong>{{ $error['line'] }}</strong> - 
                                Errors: 
                                <ul>
                                    @foreach($error['errors'] as $msg)
                                        <li>{{ $msg }}</li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
@endsection