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
                                <tr class="user-tr" data-id="{{ $user->id }}" data-nome="{{ $user->name }}" data-email="{{ $user->email }}" data-telephone="{{ $user->telephone }}" data-status="{{ $user->status }}">

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
                                                <a class="dropdown-item edit" style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#modal_editar_rifa{{ $user->id }}"><i class="bi bi-pencil-square"></i>&nbsp;Editar</a>
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

<style>

    .edit-form{

        position: absolute;
        bottom: 0;
        left: 0;
        z-index: 99999999;
        display: none;
        width: 100%;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.65);
        align-items: center;
        justify-content: center;

    }

    .form-group{

        display: block;

    }

    label{

        color: white;
        width: 100%;

    }

    #btn-close-edit-form{

        position: absolute;
        color: white;
        padding: 10px 20px;
        font-size: 30px;
        right: 0;
        text-decoration: none;
        background-color: gray;
        top: 17px;

    }


</style>

<div class="edit-form">

    <a href="#" id="btn-close-edit-form">X</a>

    <form action="" method="post">


        <input type="hidden" name="id">

        <div class="form-group">

            <label for="">Nome:</label>

            <input type="text" name="nome">

        </div>

        <div class="form-group">

            <label for="">E-mail:</label>

            <input type="email" name="email">

        </div>


        <div class="form-group">

            <label for="">Telefone:</label>

            <input type="text" name="telephone">

        </div>

        <div class="form-group">

            <label for="">Status:</label>

            <input type="status" name="status">

        </div>

        <div class="form-group">

            <input type="submit" value="Atualizar">

        </div>


    </form>


</div>


<script>

setTimeout(function(){

    const btnsEdit = document.querySelectorAll('.edit');

    const trs = document.querySelectorAll('table .user-tr');
    
    for(let cont = 0; cont < btnsEdit.length; cont++){

        btnsEdit[cont].onclick = function(){

            document.querySelector('.edit-form').style.display = 'flex';

            let id = trs[cont].getAttribute('data-id');
    
            document.querySelector('.edit-form form input[name="id"]').value = id;

            let nome = trs[cont].getAttribute('data-nome');
    
            document.querySelector('.edit-form form input[name="nome"]').value = nome;


            // let sobrenome = trs[cont].getAttribute('data-sobrenome');
    
            // document.querySelector('.edit-form form input[name="sobrenome"]').value = sobrenome;


            let telephone = trs[cont].getAttribute('data-telephone');
            
            document.querySelector('.edit-form form input[name="telephone"]').value = telephone;



            let email = trs[cont].getAttribute('data-email');
            
            document.querySelector('.edit-form form input[name="email"]').value = email;



            let status = trs[cont].getAttribute('data-status');
            
            document.querySelector('.edit-form form input[name="status"]').value = status;

        }

    }

    document.querySelector('#btn-close-edit-form').onclick = function(){

        document.querySelector('.edit-form').style.display = 'none';

    }

}, 5000);

</script>