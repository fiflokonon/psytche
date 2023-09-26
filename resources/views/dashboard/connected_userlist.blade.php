@extends('dashboard.index')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title">Liste des utilisateurs connectés</h4>
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead class="bg-danger">
                        <tr>
                            <th>Call-ID</th>
                            <th>Solde</th>
                            <th>Dernière connexion</th>
                            <th>Statut</th>
                            <th>Date de création</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->call_id }}</td>
                            <td>{{ $user->balance }}</td>
                            <td>{{ $user->last_connexion }}</td>
                            @if( $user->status )
                                <td><span class="badge rounded-pill bg-success">Actif</span></td>
                            @else
                                <td><span class="badge rounded-pill bg-danger">Inactif</span></td>
                            @endif
                            <td>{{ $user->created_at }}</td>
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
