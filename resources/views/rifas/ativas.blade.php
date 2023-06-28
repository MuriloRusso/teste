<style>
    .body-ranking {
        background-color: #f1f1f1;
        border: none;
        border-radius: 10px;
        margin-top: 10px;
    }

    .body-ranking.dark {
        background: #222222;
    }

    .title-ranking h5 {
        color: #000;
    }

    .title-ranking span {
        color: #000;
    }

    .title-ranking.dark h5 {
        color: #fff;
    }

    .title-ranking.dark span {
        color: #fff;
    }

    .body-promo {
        background-color: #f1f1f1;
        border: none;
        border-radius: 10px;
        margin-top: 20px;
    }

    .body-promo.dark{
        background: #222222;
    }

    .title-promo.dark{
        color: #fff !important;
    }
</style>

{{-- Ranking de compradores (WDM) --}}
@if (count($ranking) > 0)
    <div class="card" style="border: none;border-radius: 10px;background-color: transparent;">
        <div class="card-body body-ranking {{ $config->tema }}">
            <div class="" style="">
                <?php $resultNumber = $totalPago; ?>
            </div>
            <div class="title-ranking {{ $config->tema }}" style="margin-bottom: 10px;">
                <h5 style="font-weight: bold;">RANKING DE COMPRADORES</h5>
                <span>Quem compra mais ganha.</span><br>
            </div>


            <div class="row" style="display: flex;justify-content:center;position:relative">
                @foreach ($ranking as $key => $rk)
                    <div class="btn-auto item-ranking" onclick="addQtd('5')">
                        {{ $key + 1 }}º {{ $productModel->medalhaRanking($key) }}<br>
                        <span style="font-size: 20px;font-weight: bold;">{{ $rk->name }}</span>
                        <br>
                        <span style="font-size: 12px;">Qtd. de Bilhetes
                            {{ $rk->totalReservas }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Promoções --}}
@if ($productModel->promocoes()->where('qtdNumeros', '>', 0)->count() > 0)
    <div class="card" style="border: none;border-radius: 10px;background-color: transparent;">
        <div class="card-body body-promo {{ $config->tema }}">
            <div class="" style="margin-bottom: 10px;">
                <h1 class="mt-1 title-promo {{ $config->tema }}">
                    📣 Promoção
                    <small class="text-muted title-promo {{ $config->tema }}" style="font-size: 15px;">Compre mais
                        barato!</small>
                </h1>
            </div>
            <div class="row">
                @foreach ($productModel->promocoes()->where('qtdNumeros', '>', 0) as $promo)
                    <div class="col-6" style="margin-bottom: 8px;" data-toggle="modal" data-target="#staticBackdrop"
                        onclick="addQtd('{{ $promo->qtdNumeros }}', '{{ $promo->valorFormatted() }}')">
                        <div class="bg-success" style="color: #fff;text-align: center;border-radius:6px;"><strong>
                                {{ $promo->qtdNumeros }} POR - R$:
                                {{ $promo->valorFormatted() }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Ver meus premios e Parcial --}}
<div class="col-auto mb-3">
    <div class="row mt-2">
        <div class="{{ env('APP_URL') == 'rifasonline.link' ? 'col-md-12 col-12' : 'col-md-6 col-6' }}">
            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="openModal()"
                class="btn btn-secondary btn-sm bg-secondary"
                style="font-size: 12px; width: 100%; {{ env('APP_URL') == 'rifasonline.link' ? 'background-color: red !important' : 'background: #198754 !important' }}">
                <i class="fas fa-shopping-cart"></i>&nbsp;Ver meus números
            </button>
        </div>
        <div class="{{ env('APP_URL') == 'rifasonline.link' ? 'col-md-12 col-12 mt-2' : 'col-md-6 col-6' }}">


            @if (env('APP_URL') != 'rifasonline.link')
                <button type="button" data-bs-toggle="modal" data-bs-target="#modal-premios"
                    class="btn btn-secondary btn-sm bg-secondary" style="width: 100%; font-size: 12px; ">
                    <i class="fas fa-trophy"></i>&nbsp;Prêmios
                </button>
            @endif
        </div>
    </div>

    @if ($productModel->parcial)
        <div class="row mt-4 justify-content-center">
            <div class="col-md-12">
                <div class="progress-sell">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                            role="progressbar" style="width: {{ $productModel->porcentagem() }}%" aria-valuenow="10"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ $productModel->porcentagem() }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
