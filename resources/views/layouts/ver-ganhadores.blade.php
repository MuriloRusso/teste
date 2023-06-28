<div class="row">
    <div class="col-3">
        <img src="/products/{{ $rifa->imagem()->name }}" width="100%" style="border-radius: 10px;">
    </div>
    <div class="col-9">
        <h3>{{ $rifa->name }}</h3>
        <h6><strong>Data do sorteio: </strong>{{ date('d/m/Y', strtotime($rifa->draw_date)) }}</h6>
    </div>
</div>

@foreach ($rifa->premios()->where('descricao', '!=', '') as $premio)
    <hr>
    <div class="row mt-2">
        <div class="col-1">
            <h1 style="font-size: 50px;">
                {{ $premio->ordem }}
            </h1>
        </div>
        <div class="col-9">
            <label>{{ $premio->ganhador }}</label> <br>
            <span class="badge bg-success">Cota: {{ $premio->cota }}</span> <br>
            <label>
                Telefone: 
                <span id="tel-hide-{{ $premio->id }}">{{ substr($premio->telefone, 0, 4) }} *****-****</span>
                <span class="d-none" id="tel-show-{{ $premio->id }}">{{ $premio->telefone }}</span>
                <i class="far fa-eye" id="eye-show" onclick="toggleTelefone('{{ $premio->id }}')" style="cursor: pointer"></i>
                <i class="far fa-eye-slash d-none" id="eye-hide" onclick="toggleTelefone('{{ $premio->id }}')" style="cursor: pointer"></i>
            </label>
            <br>
            <label>Prêmio: {{ $premio->descricao }}</label> <br>
            <a href="{{ $premio->linkWpp() }}" target="_blank" class="btn btn-sm btn-success" style="font-size: 12px;"><i class="fab fa-whatsapp"></i>&nbsp; ENTRAR EM CONTATO</a>
        </div>
    </div>
@endforeach

<script>
    function toggleTelefone(id){
        var telHide = document.getElementById(`tel-hide-${id}`)
        var telShow = document.getElementById(`tel-show-${id}`)
        var eyeShow = document.getElementById('eye-show')
        var eyeHide = document.getElementById('eye-hide')

        telHide.classList.toggle('d-none')
        telShow.classList.toggle('d-none')
        eyeShow.classList.toggle('d-none')
        eyeHide.classList.toggle('d-none')
    }
</script>