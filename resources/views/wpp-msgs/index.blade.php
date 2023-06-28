@extends('layouts.admin')

@section('content')
<div class="container mt-3" style="max-width:100%;min-height:100%;">
    <div class="table-wrapper ">
        <div class="table-title">
            <div class="row mb-3">
                <div class="col d-flex justify-content-center">
                    <h2>Whatsapp <b>Mensagens</b></h2>
                </div>
            </div>

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
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h4>Varíaveis</h4>
        </div>

        <span>{id}: Código da compra</span> <br>
        <span>{nome}: Nome do cliente</span> <br>
        <span>{valor}: Valor por cota</span> <br>
        <span>{total}: Total da compra</span> <br>
        <span>{cotas}: Cotas da compra</span> <br>
        <span>{sorteio}: Título do sorteio</span> <br>
        <span>{link}: Link de pagamento</span> <br>
    </div>

    <form action="{{ route('wpp.salvar') }}" method="POST">
        @csrf
        <div class="row">
            @foreach ($msgs as $msg)
                <hr>
                <input type="hidden" name="id[{{ $msg->id }}]" value="{{ $msg->id }}">
                <div class="col-md-12 mt-2">
                    <label>Título</label>
                    <input type="text" name="titulo[{{ $msg->id }}]" class="form-control" value="{{ $msg->titulo }}">
                </div>
                <div class="col-md-12 mt-2 mb-2">
                    <label>Mensagem</label>
                    <textarea name="msg[{{ $msg->id }}]" rows="10" class="form-control" style="resize: none">{{ $msg->clearBreak() }}</textarea>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-sm btn-success mt-2 mb-4 float-right">Salvar</button>
    </form>
@endsection