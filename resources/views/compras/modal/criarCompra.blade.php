<style>
    .number {
        border: 1px solid;
        text-decoration: none;
        display: inline-flex;
        width: 10%;
        vertical-align: middle;
        border: 2px solid #132439;
        border-radius: 6px;
        padding: 10px;
        text-align: center;
        justify-content: center;
        font-weight: bold;
        background-origin: border-box;
        -webkit-mask: radial-gradient(circle 10px at right, #000 95%, #000) right, radial-gradient(circle 10px at left, #000 95%, #000) left;
        -webkit-mask-size: 51% 100%;
        -webkit-mask-repeat: no-repeat;
        background-color: #f1f1f1;
        color: #000;
    }

    .item-pago {
        background-color: rgb(226, 117, 117) !important;
    }

    .item-reservado {
        background-color: rgb(226, 117, 117) !important;
    }

    .item-selected {
        background-color: green !important;
    }
</style>

<div class="modal fade" id="modal_criar_compra" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form action="{{ route('compras.criar') }}" method="POST">
        @csrf
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Criar Compra</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        onclick="closeModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-header justify-content-center">
                    <input type="hidden" name="idRifa" value="{{ $rifa->id }}">
                    <div class="row" style="font-size: 12px;">
                        <div class="col-md-4">
                            <label>Nome</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        <div class="col-md-4">
                            <label>Telefone</label>
                            <input type="text" id="telephone" name="telefone" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Status</label>
                            <select name="status" id="" class="form-control">
                                <option value="Pendente">Aguardando Pagamento</option>
                                <option value="Pago">Aprovado</option>
                            </select>
                        </div>
                        <input type="hidden" id="numbers" name="numeros">
                    </div>
                </div>
                <div class="modal-body">
                    <div class="raffles text-center mt-2">
                        @foreach ($rifa->numbers() as $numero)
                            <a href="javascript:void(0);" id="numero-{{ $numero['number'] }}"
                                class="number {{ 'item-' . strtolower($numero['status']) }}"
                                data-number="{{ $numero['number'] }}" data-key="{{ $numero['key'] }}" data-status="{{ strtolower($numero['status']) }}"
                                onclick="selectRaffles(this)">{{ $numero['number'] }}</a>
                        @endforeach
                    </div>

                    <div class="row mt-4 mb-2 justify-content-end">
                        <div class="col-md-3 d-flex">
                            <input type="number" class="form-control text-center" id="qtd" min="1"
                                max="{{ $rifa->qtdNumerosDisponiveis() }}" value="{{ $rifa->qtdNumerosDisponiveis() }}">
                            <button class="btn btn-sm btn-primary" type="button"
                                onclick="aleatorio('{{ $rifa->id }}')"><i class="fas fa-exchange-alt"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary"
                        data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"
                        onclick="return verificaSelected()">Confirmar</button>
                </div>

            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('telephone').addEventListener('input', function(e) {
        var aux = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
        e.target.value = !aux[2] ? aux[1] : '(' + aux[1] + ') ' + aux[2] + (aux[3] ? '-' + aux[3] : '');
    });

    function verificaSelected() {
        var numbers = document.getElementById('numbers');
        if (numbers.value == '') {
            Swal.fire(
                'Erro!',
                'Selecione pelo menos 1 numero para criar a compra',
                'error'
            )
            return false;
        }
        else{
            return true;
        }
    }

    function selectRaffles(el) {
        var status = el.dataset.status;
        var key = el.dataset.key;
        if (status != 'disponivel') return;

        if (el.dataset.selected == "true") {
            clickNumber('-', el.dataset.number, key)
            el.dataset.selected = false;
            el.classList = 'number item-disponivel';
        } else {
            clickNumber('+', el.dataset.number, key)
            el.dataset.selected = true;
            el.classList = 'number item-selected';
        }


    }

    function clickNumber(operacao, number, key) {
        var numbers = document.getElementById('numbers');

        if (operacao == '+') {
            if (numbers.value == '') {
                numbers.value += `${number}-${key}`
            } else {
                numbers.value += `,${number}-${key}`
            }

        } else if (operacao == '-') {
            arrayNumbers = numbers.value.split(",");

            arrayNumbers.forEach(function(numero, i) {
                if (numero == `${number}-${key}`) {
                    arrayNumbers.splice(i, 1);
                }
            })

            numbers.value = arrayNumbers.toString();
        }
    }

    function clearModalCriarCompra() {
        var numbers = document.getElementById('numbers');
        if (numbers.value == '') return
        arrayNumbers = numbers.value.split(",");

        arrayNumbers.forEach(function(numero, i) {
            var el = document.getElementById(`numero-${numero}`);
            el.dataset.selected = false;
            el.classList = 'number item-disponivel';

            clickNumber('-', numero);
        })

        //$('#modal_criar_compra').find('input:text').val('');
    }

    function aleatorio(idRifa) {
        clearModalCriarCompra();

        var qtd = document.getElementById('qtd').value;

        $.ajax({
            url: "{{ route('compras.randomNumbers') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                "id": idRifa,
                "qtd": qtd
            },
            success: function(response) {
                if (response.numbers) {
                    response.numbers.forEach(function(el, i) {
                        var item = document.getElementById(`numero-${el.number}`)

                        item.dataset.selected = true;
                        item.classList = 'number item-selected';

                        clickNumber('+', el.number)
                    })
                } else {
                    Swal.fire(
                        'Erro!',
                        response.error,
                        'error'
                    )
                }
            },
            error: function(error) {
                Swal.fire(
                    'Erro Desconhecido!',
                    '',
                    'error'
                )
            }
        })
    }

    function closeModal() {
        $('#modal_criar_compra').modal('hide')
    }
</script>
