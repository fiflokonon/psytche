@extends('dashboard.index')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Les types de forfaits</h4>
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead class="bg-danger">
                        <tr>
                            <th>Type de forfait</th>
                            <th>Statut</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($types as $type)
                        <tr>
                            <td>{{ $type->title }}</td>
                            @if( $type->status )
                            <td><span class="badge rounded-pill bg-success">Actif</span></td>
                            @else
                            <td><span class="badge rounded-pill bg-danger">Inactif</span></td>
                            @endif
                            <td>{{ $type->created_at }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
</div>
@endsection
