@extends('layouts.admin')
<style>
    .hidden {
        display: none;
    }

    .promo {
        border: solid;
        border-width: thin;
        border-radius: 10px;
        padding: 20px;
    }
</style>

@section('content')


<div class="row">
        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session()->has('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{{ session('success') }}</li>
                    </ul>
                </div>
            @endif

            <div class="container mt-3" style="max-width:100%;min-height:100%;">
                <div class="table-wrapper ">
                    <div class="table-title">

                        <table class="table table-striped table-bordered table-responsive-md table-hover align=center"
                                id="table_users">
                                <thead>
                                    <tr>
                                        <th>Nome</th>

                                    </tr>
                                </thead>

                            @foreach ($users as $key => $user)
                                <tr>

                                    <td>{{ $user->id }}</td>

                                    <td>{{ $user->name }}</td>

                                    <td>{{ $user->email }}</td>

                                    <td>{{ $user->telephone }}</td>

                                    <td>{{ $user->status }}</td>

                                    <td style="width: 20%">
                                     
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            
                                            </button>
                                                <div class="dropdown-menu">
                                                <a class="dropdown-item" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#modal_editar_rifa{{ $user->id }}"><i class="bi bi-pencil-square"></i>&nbsp;Editar</a>
                                                <a class="dropdown-item" href="#deleteEmployeeModal{{ $user->id }}" style="cursor: pointer" data-toggle="modal" data-bs-target="#deleteEmployeeModal{{ $user->id }}" data-id="{{ $user->id }}"><i class="bi bi-trash3"></i>&nbsp;Excluir</a>

                                            </div>
                                        </div>
                                               
                                    </td>
                                    
                                </tr>

                            @endforeach

                        </table>

                    </div>

                </div> 
            
            </div>   

    </div>

</div>




@endsection