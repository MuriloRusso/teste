<style>
    /* width */
    #div-cotas::-webkit-scrollbar {
        width: 10px;
    }

    /* Track */
    #div-cotas::-webkit-scrollbar-track {
        box-shadow: inset 0 0 5px grey;
        border-radius: 10px;
    }

    /* Handle */
    #div-cotas::-webkit-scrollbar-thumb {
        background: #28a745 !important;
        border-radius: 10px;
    }

    /* Handle on hover */
    #div-cotas::-webkit-scrollbar-thumb:hover {
        background: #28a745 !important;
    }
</style>

<div style="font-size: 14px;">
    <div class="row mt-2">
        @foreach ($msgs as $msg)
            <div class="col-md-4">
                <a href="{{ $msg->generateLink($participante) }}" target="_blank" class="btn btn-sm btn-secondary"
                    style="width: 100%;"><i class="fab fa-whatsapp"></i>&nbsp; {{ $msg->titulo }}</a>
            </div>
        @endforeach
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <label>Nome</label> <br>
            <span>{{ $participante->name }}</span>
        </div>
        <div class="col-md-6">
            <label>Email</label> <br>
            <span>{{ $participante->email }}</span>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <label>Telefone</label> <br>
            <a style="text-decoration: none; color: #000" target="_blank"
                href="{{ $participante->linkWpp() }}"><span>{{ $participante->telephone }}</span></a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <label>Sorteio</label> <br>
            <span>{{ $participante->rifa()->name }}</span>
        </div>
    </div>

    <hr>

    <div class="raffles mt-2">
        <label>Cotas</label> <br>
        <div id="div-cotas" style="max-height: 200px;overflow: auto;">
            @foreach ($participante->pagos() as $reserva)
                <span class="badge bg-success"> <i class="fa fa-check"></i> {{ $reserva->number }}</span>
            @endforeach
            @foreach ($participante->reservados() as $reserva)
                <span class="badge bg-secondary"> <i class="fa fa-clock"></i> {{ $reserva->number }}</span>
            @endforeach
        </div>
    </div>

    <hr>

    <div class="row mt-4">
        <div class="col-md-4">
            <label>Subtotal</label> <br>
            <span>R$ {{ number_format($participante->valor, 2, ',', '.') }}</span>
        </div>

        <div class="col-md-4">
            <label>Desconto</label> <br>
            <span>R$ 0,00</span>
        </div>

        <div class="col-md-4">
            <label>Subtotal</label> <br>
            <span>R$ {{ number_format($participante->valor, 2, ',', '.') }}</span>
        </div>

        <div class="col-md-4 mt-4">
            <label>Situação da compra</label> <br>
            <span>{{ $participante->status() }}</span>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6 text-center historico">
            Reserva efetuada
        </div>
        <div class="col-md-6 text-center historico">
            {{ date('d/m/Y H:i:s', strtotime($participante->created_at)) }}
        </div>
    </div>

    @if ($participante->qtdPagos() > 0)
        <div class="row">
            <div class="col-md-6 text-center historico">
                Pagamento efetuado (PIX)
            </div>
            <div class="col-md-6 text-center historico">
                {{ date('d/m/Y H:i:s', strtotime($participante->updated_at)) }}
            </div>
        </div>
    @endif

    <hr>
    <div class="row">
        <div class="col-md-4">
            <a href="{{ route('reservarNumeros', ['participante' => $participante->id]) }}"
                onClick="this.disabled=true; this.innerHTML='Processando...';this.classList.add('disabled')"
                class="btn sm btn-info" style="width: 100%"><i class="far fa-clock"></i>&nbsp;Reservar</a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('releaseReservedRafflesNumbers', ['release_reservervations' => $participante->id]) }}"
                onClick="this.disabled=true; this.innerHTML='Processando...';this.classList.add('disabled')"
                class="btn sm btn-danger" style="width: 100%"><i class="fas fa-times"></i>&nbsp;Recusar</a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('pagarReservas', ['participante' => $participante->id]) }}" class="btn sm btn-success"
                onClick="this.disabled=true; this.innerHTML='Processando...';this.classList.add('disabled')"
                style="width: 100%"><i class="fas fa-check"></i>&nbsp;Aprovar</a>
        </div>
    </div>
</div>

<style>
    .historico {
        border: 1px solid gray;
        padding-top: 10px;
        padding-bottom: 10px;
        font-weight: bold;
    }

    .historico label {
        align-items: center;
    }
</style>
