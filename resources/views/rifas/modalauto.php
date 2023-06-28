<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 999999999;">
    <div class="modal-dialog">
        <!-- <form action="{{ route('bookProductManualy') }}" method="POST"> -->

        <!-- <form action="{{ route('product') }}" method="POST"> -->

        <!-- <form action="" method="POST" id="myForm"> -->
        

        <!-- <form action="" method="POST"> -->

            {{ csrf_field() }}
            <div class="modal-content" style="border: none;">
                <div class="modal-header" style="background-color: #939393;color: #fff;">
                    <h5 class="modal-title" id="staticBackdropLabel">INICIANDO RESERVA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background: #efefef;color: #939393;">
                    <div class="form-group">
                        
                        <input type="hidden" class="form-control" name="productName" value="{{ $product[0]->name }}">
                        <input type="hidden" class="form-control" name="productID" value="{{ $product[0]->id }}">
                        <input type="hidden" class="form-control" name="numberSelected" id="numberSelectedInput">
                    
                    </div>
                    <!--<legend>Por favor, preencha os dados abaixo:</legend>-->
                  
                    <div class="form-group">
                        <label>CELULAR (Whatsapp)</label>
                        <input type="text" class="form-control numbermask"
                            style="background-color: #fff;border: none;color: #333;" name="telephonePre" id="telephonePre"
                            placeholder="Informe seu telefone com DDD" maxlength="15" required>
                    </div>
                    <input type="hidden" id="promo" name="promo">
                    <!--<small class="form-text text-muted">Reservando seu(s) número(s), você declara que leu e concorda com nossos <a href="{{ url('terms-of-use') }}">Termos de Uso</a>.</small>-->
                </div>
                <div class="modal-footer" style="background: #939393;color: #fff;">
                    <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>-->
                    <!-- <a onClick="validarCel()"
                        class="btn btn-success"
                        style="width: 100%;min-height: 60px;border: none;color: #fff;font-weight: bold;width: 100%;background-color: green">PROSSEGUIR</a>
                 -->
                    <button type="button" class="btn btn-danger reservation btn-amount blob bg-success"
                    style="color: #fff;border: none;width: 100%;margin-top: 5px;font-weight: bold;"
                    onclick="dadosUsuarios(); this.disabled=true; this.innerHTML='PROCESSANDO...';">PROSSEGUIR<span id="" style="color: #fff;float:right"></span></button>
<!-- 
                    <input type="submit" class="btn btn-danger reservation btn-amount blob bg-success"
                    style="color: #fff;border: none;width: 100%;margin-top: 5px;font-weight: bold;"
                     value="PROSSEGUIR"> -->


                    </div>

            </div>
        <!-- </form> -->

     

      

       


        <!-- <script>




        </script> -->

    </div>
</div>

<div id="loading">

<p>Carregando...</p>

</div>




<style>




#loading{
    
    background-color: white !important;
    text-align: center !important;
    justify-content: center !important;
    align-items: center !important;
    display: none !important;
    height: 200vw !important;
    width: 100% !important;
    position: absolute !important;
    z-index: 999999999999999999999 !important;
    left: 0 !important;
    top: 0 !important;
    
}

#loading p{
    
    color: #333 !important;	
    
}

</style>




<script>

function dadosUsuarios(){

    let telephone = document.getElementById("telephonePre").value;

    fetch(`http://127.0.0.1:8000/cadastra-participanteAuto/${telephone}`)

    .then(function(res){ return res.json(); })
    
    .then(function(data){ 

        console.log( data) 

        if(data.usuario != '' && data.usuario != undefined){

            document.querySelector('#loading').style = 'display: flex !important';

            setTimeout(function(){
                
                document.querySelector('#staticBackdrop input[name="productName"]').value = document.querySelector('#staticBackdrop2 input[name="productName"]').value;
                    
                document.querySelector('#staticBackdrop input[name="productID"]').value = document.querySelector('#staticBackdrop2 input[name="productID"]').value;

                document.querySelector('#staticBackdrop input[name="numberSelected"]').value = document.querySelector('#staticBackdrop2 input[name="numberSelected"]').value;

                document.querySelector('#staticBackdrop2 input[name="name"]').value = data.usuario;

                document.querySelector('#staticBackdrop2 input[name="email"]').value = data.email;

                document.querySelector('#staticBackdrop2 input[name="telephone"]').value = data.telephone;

                $('#staticBackdrop2').modal('show');

                setTimeout(function(){

                    document.querySelector('#staticBackdrop2 button[type="submit"]').click();

                }, 500);

            }, 2000);

        }        
        
        else{

            document.querySelector('#staticBackdrop input[name="productName"]').value = document.querySelector('#staticBackdrop2 input[name="productName"]').value;
                    
            document.querySelector('#staticBackdrop input[name="productID"]').value = document.querySelector('#staticBackdrop2 input[name="productID"]').value;

            document.querySelector('#staticBackdrop input[name="numberSelected"]').value = document.querySelector('#staticBackdrop2 input[name="numberSelected"]').value;

            document.querySelector('#staticBackdrop2 input[name="telephone"]').value = telephone;

            $('#staticBackdrop2').modal('show');

        }

    })

}


</script>














<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    style="z-index: 9999999;">
    <div class="modal-dialog">
        <div class="modal-content" style="border: none;">
            <div class="modal-header" style="background-color: #020f1e;">
                <h5 class="modal-title" id="exampleModalLabel" style="color: #fff;">CONSULTAR RESERVAS</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                    style="color: #fff;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: #020f1e;">
                <div class="row">
                    <div class="col-md-12">
                        <!-- <form action="{{ route('consultingReservation') }}" method="POST" style="display: flex;"> -->
                            {{ csrf_field() }}
                            <input type="hidden" name="productID" value="{{ $product[0]->id }}">
                            <input type="text" id="telephone3" name="telephone"
                                style="background-color: #fff;border: none;color: #000;margin-right:5px;"
                                aria-describedby="passwordHelpBlock" maxlength="15" placeholder="Celular com DDD"
                                class="form-control" required>
                            <button type="submit" class="btn btn-danger">Buscar</button>
                        <!-- </form> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border: none;">
            <div class="modal-header" style="background-color: #020f1e;">
                <h5 class="modal-title" id="exampleModalLabel" style="color: #fff;">DÚVIDAS FREQUENTES</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="color: #fff;background-color: red!important;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: #020f1e;color: #ffffff;">
                <b style="text-transform: uppercase;">- É confiável?</b><br>
                <span style="color: #999999;">R: Sim, sorteio pela milhar da loteria federal.</span><br>
                <b style="text-transform: uppercase;">- Que dia é o sorteio?</b><br>
                <span style="color: #999999;">R: Após a venda de todas as cotas, no site você pode acompanhar as
                    vendas!</span><br>
                <b style="text-transform: uppercase;">- Como participar da nossa rifa?</b><br>
                <span style="color: #999999;">R: Existe duas formas compra automática e compra manual.</span><br>
                <b style="text-transform: uppercase;">- Forma de pagamento</b><br>
                <span style="color: #999999;">R: Somente PIX Copia e Cola ou CNPJ</span><br>
                <b style="text-transform: uppercase;">- Se eu escolher o veículo</b><br>
                <span style="color: #999999;">R: Vamos entregar na sua garagem o prêmio.</span>
            </div>
            <div class="modal-footer" style="background-color: #020f1e;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


{{-- Modal Premios --}}
<div class="modal fade" id="modal-premios" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
    style="z-index: 9999999;">
    <div class="modal-dialog">
        <div class="modal-content" style="border: none;">
            <div class="modal-header" style="background-color: #020f1e;">
                <h5 class="modal-title" id="exampleModalLabel" style="color: #fff;">PRÊMIOS</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                    style="color: #fff;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="">
                <div class="col-md-12 text-center">
                    Estes são os prêmios disponíveis no sorteio <strong>{{ $productModel->name }}</strong>
                </div>
                <hr>
                @foreach ($productModel->premios()->where('descricao', '!=', '') as $premio)
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <label><strong>Prêmio {{ $premio->ordem }}: </strong>{{ $premio->descricao }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>


<div class="blob green" id="messageIn"
    style="position: fixed;
bottom: 15px;
z-index: 99999;
color: #fff;
padding: 3px;
font-weight: bold;
font-size: 12px;
width: 180px;
text-align: center;
z-index: 99999;border-radius: 20px;left: 10px;">
</div>
