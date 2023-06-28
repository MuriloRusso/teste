<?php

namespace App\Http\Controllers;

use App\Participant;
use App\Product;
use App\CreateProductimage;
use App\Models\Order;
use App\Models\Participante;
use App\Models\Premio;
use App\Models\Product as ModelsProduct;
use App\Models\Raffle;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use QRcode;

class ProductController extends Controller
{
    public function index()
    {
        $ganhadores = Premio::where('descricao', '!=', null)->where('ganhador', '!=', '')->get();

        $products = ModelsProduct::where('visible', '=', 1)->orderBy('id', 'desc')->get();

        $winners = ModelsProduct::where('status', '=', 'Finalizado')->where('visible', '=', 1)->where('winner', '!=', null)->get();
        
        $config = DB::table('consulting_environments')->where('id', '=', 1)->first();

        return view('welcome', [
            'products' => $products,
            'winners' => $winners,
            'ganhadores' => $ganhadores,
            'user' => User::find(1),
            'productModel' => ModelsProduct::find(4),
            'config' => $config
        ]);
    }

    public function product($slug)
    {
        //dd($idProductURL);
        $productSlug = DB::table('products')
            ->select('id')
            ->where('products.slug', '=', $slug)
            ->get();
        //dd($productSlug[0]->id);
        $productID = $productSlug[0]->id;

        // Verificando se sorteio ja expirou para finalizar automatico.
        //$this->verificaSorteio($productID);



        //dd($productID);
        $user = DB::table('users')
            ->select('users.name', 'users.telephone', 'products.type_raffles')
            ->leftJoin('products', 'products.user_id', 'users.id')
            ->leftJoin('consulting_environments', 'consulting_environments.user_id', 'users.id')
            ->where('products.id', '=', $productID)
            ->first();
        $imagens = DB::table('products_images')
            ->select('products_images.name')
            ->join('products', 'products.id', '=', 'products_images.product_id')
            ->where('products.id', '=', $productID)
            ->get();
        //dd($imagens);



        $productDetail = DB::table('products')
            ->select('products.id', 'products.name', 'products.subname', 'products.product', 'products.price', 'products_images.name as image', 'products.status', 'products.draw_date', 'products.draw_prediction', 'products.winner')
            ->leftJoin('products_images', 'products.id', 'products_images.product_id')
            ->where('products.id', '=', $productID)
            ->orderBy('products_images.name', 'ASC')
            ->get();

        $bookProduct = DB::table('products')
            ->select('products.name', 'products.price', 'raffles.number', 'raffles.status', 'products.status as statusProduct', 'participant.name as participant', 'participant.created_at')
            ->join('raffles', 'products.id', '=', 'raffles.product_id')
            ->leftJoin('participant', 'raffles.id', 'participant.raffles_id')
            ->where('products.id', '=', $productID)
            ->get();

        $productDescription = DB::table('product_description')
            ->select('product_description.description', 'product_description.video')
            ->join('products', 'products.id', '=', 'product_description.product_id')
            ->where('products.id', '=', $productID)
            ->first();
        //dd($productDescription->description);


        //TOTAIS DE NÚMEROS
        $totalNumbers = DB::table('products')
            ->select('raffles.status')
            ->join('raffles', 'products.id', '=', 'raffles.product_id')
            ->where('products.id', '=', $productID)
            ->count();

        $totalDispo = DB::table('products')
            ->select('raffles.status')
            ->join('raffles', 'products.id', '=', 'raffles.product_id')
            ->where('products.id', '=', $productID)
            ->where('raffles.status', '=', 'Disponível')
            ->count();

        $totalReser = DB::table('products')
            ->select('raffles.status')
            ->join('raffles', 'products.id', '=', 'raffles.product_id')
            ->where('products.id', '=', $productID)
            ->where('raffles.status', '=', 'Reservado')
            ->count();

        $totalPago = DB::table('products')
            ->select('raffles.status')
            ->join('raffles', 'products.id', '=', 'raffles.product_id')
            ->where('products.id', '=', $productID)
            ->where('raffles.status', '=', 'Pago')
            ->count();

        $valueProduct = DB::table('products')
            ->select('price')
            ->where('products.id', '=', $productID)
            ->first();

        $value50 = str_replace(',', '.', $valueProduct->price) * 50;
        $value100 = str_replace(',', '.', $valueProduct->price) * 100;
        $value150 = str_replace(',', '.', $valueProduct->price) * 150;
        $value200 = str_replace(',', '.', $valueProduct->price) * 200;

        $result50 = $value50 - ($value50 * 10 / 100);
        $result100 = $value100 - ($value100 * 10 / 100);
        $result150 = $value150 - ($value150 * 10 / 100);
        $result200 = $value200 - ($value200 * 10 / 100);

        $productModel = ModelsProduct::find($productID);

        $config = DB::table('consulting_environments')->where('id', '=', 1)->first();

        $arrayProducts = [
            'imagens' => $imagens,
            'product' => $productDetail,
            'bookProduct' => $bookProduct,
            'productDescription' => $productDescription ? $productDescription->description : '',
            'productDescriptionVideo' => $productDescription ? $productDescription->video : '',
            'totalNumbers' => $totalNumbers,
            'totalDispo' => $totalDispo,
            'totalReser' => $totalReser,
            'totalPago' => $totalPago,
            'user' => $user->name,
            'telephone' => $user->telephone,
            'type_raffles' => $user->type_raffles,
            'result50' => number_format($result50, 2, ",", "."),
            'result100' => number_format($result100, 2, ",", "."),
            'result150' => number_format($result150, 2, ",", "."),
            'result200' => number_format($result200, 2, ",", "."),
            'productModel' => $productModel,
            'ranking' => $productModel->ranking(),
            'config' => $config
        ];


        return view('product-detail', $arrayProducts);
    }

    public function verificaSorteio($productId)
    {
        // Verificando se sorteio ja expirou ou se ja foi vendido todas as cotas para finalizar automatico.
        $product = ModelsProduct::find($productId);
        if ($product->numbers()->count() == $product->qtdNumerosPagos()) {
            $product->status = 'Finalizado';
            $product->update();
        } else if ($product->draw_date != null && $product->draw_date <= date('Y-m-d H:i:s')) {
            $product->status = 'Finalizado';
            $product->update();
        }
    }

    public function randomParticipant()
    {
        $userRandom = Participant::inRandomOrder()->select('name')->first();
        $resultUserRandom = explode(' ', $userRandom->name);

        return json_encode($resultUserRandom);
    }

    public function getRaffles(Request $request)
    {
        $bookProduct = DB::table('products')
            ->select('products.name', 'products.price', 'raffles.number', 'raffles.status', 'products.status as statusProduct', 'participant.name as participant', 'participant.created_at', 'products.qtd_zeros')
            ->join('raffles', 'products.id', '=', 'raffles.product_id')
            ->leftJoin('participant', 'raffles.id', 'participant.raffles_id')
            ->where('products.id', '=', $request->idProductURL)
            ->get();
        
        $rifa = ModelsProduct::find($request->idProductURL);
        $numbers = $rifa->numbers();

        foreach ($numbers as $number) {
            if ($rifa->status === 'Ativo') {
                if ($number['status'] === 'Disponivel') {
                    $resultRaffles[] = "<a href='javascript:void(0);' class='number filter disponivel' onclick=\"selectRaffles('" . $number['number'] . "', '" . $number['key'] . "')\" style='background-color: #585858;color: #000;' id=" . $number['number'] . ">" . $number['number'] . "</a>";
                } else if ($number['status']  == "Reservado") {
                    $resultRaffles[] = "<a href='javascript:void(0);' class='number filter reservado' onclick=\"timeNumbers(this)\" style='background-color: #d7d5d5;color: #000;display:none;' id=" . $number['number'] . " data-toggle='tooltip' data-placement='top' data-html='true' title='Reservado por: " . $rifa->getParticipanteById($number['participant_id']) . "'><input type='hidden' id='createdAt" . $number['number'] . "' value=''>" . $number['number'] . "</a>";
                } else if ($number['status'] == "Pago") {
                    $resultRaffles[] = "<a href='javascript:void(0);' class='number filter pago' style='background-color: #28a745;color: #fff;display:none;' id='" . $number['number'] . "' data-toggle='tooltip' data-placement='top' title='Pago por: " . $rifa->getParticipanteById($number['participant_id']) . "'>" . $number['number'] . "</a>";
                }
            } else if ($rifa->status === 'Agendado') {
                if ($number['status'] === 'Disponivel') {
                    $resultRaffles[] = "<a href='javascript:void(0);' class='number filter disponivel' onclick=\"alert('Sorteio agendado não é mais possível reservar!')\" style='background-color: #f1f1f1;color: #000;' id=" . $number['number'] . ">" . $number['number'] . "</a>";
                } else if ($number['status']  == "Reservado") {
                    $resultRaffles[] = "<a href='javascript:void(0);' class='number filter reservado' style='background-color: #0F9EE2;color: #fff;' id=" . $number['number'] . " data-toggle='tooltip' data-placement='top' data-html='true' title='Reservado por: " . $rifa->getParticipanteById($number['participant_id']) . "'><input type='hidden' id='createdAt" . $number['number'] . "' value=''>" . $number['number'] . "</a>";
                } else if ($number['status'] == "Pago") {
                    $resultRaffles[] = "<a href='javascript:void(0);' class='number filter pago' style='background-color: #28a745;color: #fff;' id='" . $number['number'] . "' data-toggle='tooltip' data-placement='top' title='Pago por: " . $rifa->getParticipanteById($number['participant_id']) . "'>" . $number['number'] . "</a>";
                }
            } else if ($rifa->status === 'Finalizado') {
                if ($number['status'] === 'Disponivel') {
                    $resultRaffles[] = "<a href='javascript:void(0);' class='number filter disponivel' onclick=\"alert('Sorteio finalizado não é mais possível reservar!')\" style='background-color: #f1f1f1;color: #fff;' id=" . $number['number'] . ">" . $number['number'] . "</a>";
                } else if ($number['status']  == "Reservado") {
                    $resultRaffles[] = "<a href='javascript:void(0);' class='number filter reservado' style='background-color: #0F9EE2;color: #fff;' id=" . $number['number'] . " data-toggle='tooltip' data-placement='top' data-html='true' title='Reservado por: " . $rifa->getParticipanteById($number['participant_id']) . "'><input type='hidden' id='createdAt" . $number['number'] . "' value=''>" . $number['number'] . "</a>";
                } else if ($number['status'] == "Pago") {
                    $resultRaffles[] = "<a href='javascript:void(0);' class='number filter pago' style='background-color: #28a745;color: #fff;' id='" . $number['number'] . "' data-toggle='tooltip' data-placement='top' title='Pago por: " . $rifa->getParticipanteById($number['participant_id']) . "'>" . $number['number'] . "</a>";
                }
            } else {
                $resultRaffles[] = null;
            }
        }


        // foreach ($bookProduct as $raffles) {

        //     // Mostrando a qtd de zeros qe esta configurado no painel
        //     if ($raffles->qtd_zeros != null || $raffles->qtd_zeros == 0) {
        //         $number = intval($raffles->number); // retira os 0 convertendo para inteiro
        //         $number = strval($number);          // converte novamente para string
        //         for ($i = 0; $i < $raffles->qtd_zeros; $i++) {
        //             $number = '0' . $number;
        //         }

        //         $number = $raffles->number;
        //     } else {
        //         $number = $raffles->number;
        //     }

        //     if ($raffles->statusProduct === 'Ativo') {
        //         if ($raffles->status === 'Disponível') {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter disponivel' onclick=\"selectRaffles('" . $number . "')\" style='background-color: #585858;color: #000;' id=" . $number . ">" . $number . "</a>";
        //         } else if ($raffles->status  == "Reservado") {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter reservado' onclick=\"timeNumbers(this)\" style='background-color: #d7d5d5;color: #000;display:none;' id=" . $number . " data-toggle='tooltip' data-placement='top' data-html='true' title='Reservado por: " . $raffles->participant . "'><input type='hidden' id='createdAt" . $number . "' value=''>" . $number . "</a>";
        //         } else if ($raffles->status == "Pago") {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter pago' style='background-color: #28a745;color: #fff;display:none;' id='" . $number . "' data-toggle='tooltip' data-placement='top' title='Pago por: " . $raffles->participant . "'>" . $number . "</a>";
        //         }
        //     } else if ($raffles->statusProduct === 'Agendado') {
        //         if ($raffles->status === 'Disponível') {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter disponivel' onclick=\"alert('Sorteio agendado não é mais possível reservar!')\" style='background-color: #f1f1f1;color: #000;' id=" . $number . ">" . $number . "</a>";
        //         } else if ($raffles->status  == "Reservado") {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter reservado' style='background-color: #0F9EE2;color: #fff;' id=" . $number . " data-toggle='tooltip' data-placement='top' data-html='true' title='Reservado por: " . $raffles->participant . "'><input type='hidden' id='createdAt" . $number . "' value=''>" . $number . "</a>";
        //         } else if ($raffles->status == "Pago") {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter pago' style='background-color: #28a745;color: #fff;' id='" . $number . "' data-toggle='tooltip' data-placement='top' title='Pago por: " . $raffles->participant . "'>" . $number . "</a>";
        //         }
        //     } else if ($raffles->statusProduct === 'Finalizado') {
        //         if ($raffles->status === 'Disponível') {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter disponivel' onclick=\"alert('Sorteio finalizado não é mais possível reservar!')\" style='background-color: #f1f1f1;color: #fff;' id=" . $number . ">" . $number . "</a>";
        //         } else if ($raffles->status  == "Reservado") {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter reservado' style='background-color: #0F9EE2;color: #fff;' id=" . $number . " data-toggle='tooltip' data-placement='top' data-html='true' title='Reservado por: " . $raffles->participant . "'><input type='hidden' id='createdAt" . $number . "' value=''>" . $number . "</a>";
        //         } else if ($raffles->status == "Pago") {
        //             $resultRaffles[] = "<a href='javascript:void(0);' class='number filter pago' style='background-color: #28a745;color: #fff;' id='" . $number . "' data-toggle='tooltip' data-placement='top' title='Pago por: " . $raffles->participant . "'>" . $number . "</a>";
        //         }
        //     } else {
        //         $resultRaffles[] = null;
        //     }
        // }

        return json_encode($resultRaffles);
    }

    public function formatMoney($value)
    {
        $value = str_replace('.', "", $value);
        $value = str_replace(',', ".", $value);

        return $value;
    }
    public function bookProductAuto($parametro){
            try {
                
                $user = User::where('telephone', $parametro)->first();
                $responseArray = [
                    'usuario' => $user->name,
                    'telephone' => $user->telephone,
                    'email'=>$user->email
                ];               
                $responseJson = json_encode($responseArray);
               return($responseJson);
            } catch (\Throwable $th) {
                print_r('{"status":"usuario não cadastrado"}');
            }
            
         
    }

    //REVERSA OS NÚMEROS DO SORTEIO X SEM INTEGRAÇÃO COM O PIX
    public function bookProductManualy(Request $request)
    {

     

        // print_r($request->nome);


        //dd($request->all());
        DB::beginTransaction();
        try {
            $prod = ModelsProduct::find($request->productID);

            if($request->qtdNumbers > 10000){
                return Redirect::back()->withErrors('Você só pode comprar no máximo 10.000 números por vez');
            }   

            if(!$request->name){
                return Redirect::back()->withErrors('Campo nome é obrigatório!');
            }
            if(!$request->telephone){
                return Redirect::back()->withErrors('Campo telefone é obrigatório!');
            }

            // Para o gateway ASAAS é obrigatorio o CPF
            if ($prod->gateway == 'asaas') {
                $request->validate([
                    'cpf' => 'required',
                ]);
            }

          /* print_r( $request->validate([
                'cpf' => 'required',
            ]));*/

            $codeKeyPIX = DB::table('consulting_environments')
                ->select('key_pix', 'token_asaas')
                ->where('user_id', '=', 1)
                ->first();

            $integracaoGateway = true;
            if ($prod->gateway == 'mp' && $codeKeyPIX->key_pix == null) {
                $integracaoGateway = false;
            }
            if ($prod->gateway == 'asaas' && $codeKeyPIX->token_asaas == null) {
                $integracaoGateway = false;
            }

            if (!$integracaoGateway) {
                return Redirect::back()->withErrors('Administrador precisa adicionar a integração com o banco!');
            } else {

                $statusProduct = DB::table('products')
                    ->select('status')
                    ->where('products.id', '=', $request->productID)
                    ->first();

                //dd($request->productID);

                if ($statusProduct->status == "Ativo") {

                    $user = DB::table('users')
                        ->select('users.name', 'users.telephone', 'products.type_raffles')
                        ->leftJoin('products', 'products.user_id', 'users.id')
                        ->leftJoin('consulting_environments', 'consulting_environments.user_id', 'users.id')
                        ->where('products.id', '=', $request->productID)
                        ->first();

                    if ($user->type_raffles == 'manual') {
                        $validatedData = $request->validate([
                            'name' => 'required|max:255',
                            'telephone' => 'required|max:15',
                        ]);
                    } else if ($user->type_raffles == 'mesclado') {
                        if ($request->qtdNumbers == null) {
                            $validatedData = $request->validate([
                                'name' => 'required|max:255',
                                'telephone' => 'required|max:15',
                            ]);
                        } else {
                            $validatedData = $request->validate([
                                'name' => 'required|max:255',
                                'telephone' => 'required|max:15',
                                'qtdNumbers' => 'numeric|min:1|max:500'
                            ]);
                        }
                    } else if ($user->type_raffles == 'mesclado2') {
                        if ($request->qtdNumbers == null) {
                            $validatedData = $request->validate([
                                'name' => 'required|max:255',
                                'telephone' => 'required|max:15',
                            ]);
                        } else {
                            $validatedData = $request->validate([
                                'name' => 'required|max:255',
                                'telephone' => 'required|max:15',
                                'qtdNumbers' => 'numeric|min:1|max:500'
                            ]);
                        }
                    } else {
                        // $validatedData = $request->validate([
                        //     'name' => 'required|max:255',
                        //     'telephone' => 'required|max:15',
                        //     'qtdNumbers' => 'numeric|min:1|max:5000'
                        // ]);
                    }

                    print_r('teste'); 


                    if (str_starts_with($prod->modo_de_jogo, 'fazendinha')) {
                        
                        $numbers = $request->numberSelected;
                        $resutlNumbers = explode(",", $numbers);

                    } else {
                        print_r('teste dentro função 1'); 

                        if ($request->qtdNumbers == 1 && $request->rifaManual == 1) {

                            
                            $numbers = $request->numberSelected;
                            $resutlNumbers = explode(",", $numbers);
                            
                            $numbersRifa = $prod->numbers();

                            $selecionados = [];
                            foreach ($resutlNumbers as $key => $value) {
                                $expl = explode("-", $value);
                                $keyNumber = end($expl);

                                array_push($selecionados, $numbersRifa[$keyNumber]);
                            }

                        } else {

                            print_r('teste dentro função 2'); 

                            // print_r(json_encode($prod));

                            $numbersRifa = $prod->numbers();
                            
                            print_r($numbersRifa); 

                            $disponiveis = array_filter($numbersRifa, function ($number) {
                                return $number['status'] == 'Disponivel';

                            });

                            

                            print_r($disponiveis); 

                            shuffle($disponiveis);

                            $selecionados = [];
                            for ($i = 0; $i < $request->qtdNumbers; $i++) {
                                $n = $disponiveis[$i];
                                array_push($selecionados, $n);
                            }

                            if (count($disponiveis) < $request->qtdNumbers) {
                                return Redirect::back()->withErrors('Quantidade indisponível para a rifa selecionada. A quantidade disponível é: ' . count($disponiveis));
                            }

                            foreach ($selecionados as $resultNumber) {
                                $resutlNumbers[] = $resultNumber['number'];
                            }

                            $numbers = implode(",", $resutlNumbers);
                        }
                    }

                    print_r('teste 2'); 

                    $product = DB::table('products')
                        ->select('products.*', 'products_images.name as image')
                        ->join('products_images', 'products.id', 'products_images.product_id')
                        ->where('products.id', '=', $request->productID)
                        ->first();



                    // Validando minimo e maximo de compra da rifa
                    if (isset($randomNumbers)) {
                        if ($randomNumbers->count() < $product->minimo) {
                            return Redirect::back()->withErrors('Você precisa comprar no mínimo ' . $product->minimo . ' números');
                        }
                        if ($randomNumbers->count() > $product->maximo) {
                            return Redirect::back()->withErrors('Você só pode comprar no máximo ' . $product->maximo . ' números');
                        }
                    } else {
                        if (count($resutlNumbers) < $product->minimo) {
                            return Redirect::back()->withErrors('Você precisa comprar no mínimo ' . $product->minimo . ' números');
                        }
                        if (count($resutlNumbers) > $product->maximo) {
                            return Redirect::back()->withErrors('Você só pode comprar no máximo ' . $product->maximo . ' números');
                        }
                    }

                    

                    $new = str_replace(",", ".", $product->price);

                    $price = count($resutlNumbers) * $new;
                    $resultPrice = number_format($price, 2, ",", ".");

                    $resultPricePIX = number_format($price, 2, ".", ",");

                
                    if ($request->promo != null && $request->promo > 0) {
                        $resultPrice = $request->promo;
                        $resultPricePIX = $this->formatMoney($request->promo);
                    }

                    // Validando valor abaixo de 5.00 para gateway ASAAS
                    if ($prod->gateway == 'asaas' && $price < 5) {
                        return Redirect::back()->withErrors('Sua aposta deve ser de no mínimo R$ 5,00');
                    }

                    

                    // Verifica se algum numero escolhido ja possui reserva (WDM New)
                    $verifyReserved = DB::table('raffles')
                        ->select('*')
                        ->where('raffles.product_id', '=', $request->productID)
                        ->whereIn('raffles.number', $resutlNumbers)
                        ->where('raffles.status', '<>', 'Disponível')
                        ->get();

                       


                    if ($verifyReserved->count() > 0) {
                        return Redirect::back()->withErrors('Acabaram de reservar um ou mais numeros escolhidos, por favor escolha outros números :)');
                    } else {
                        $participante = DB::table('participant')->insertGetId([
                            'name' => $request->name,
                            'telephone' => $request->telephone,
                            'email' => '',
                            'cpf' => '',
                            'valor' => $resultPricePIX,
                            'reservados' => count($resutlNumbers),
                            'product_id' => $request->productID,
                            'numbers' => isset($selecionados) ? json_encode($selecionados) : json_encode($resutlNumbers),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);

                        if(isset($selecionados)){
                            foreach ($selecionados as $selecionado) {
                                if ($numbersRifa[$selecionado['key']]['status'] != 'Disponivel') {
                                    return Redirect::back()->withErrors('Acabaram de reservar um ou mais numeros escolhidos, por favor escolha outros números :)');
                                }
    
                                $numbersRifa[$selecionado['key']]['status'] = 'Reservado';
                                $numbersRifa[$selecionado['key']]['participant_id'] = $participante;
                            }
    
                            $arquivo = 'numbers/' . $prod->id . '.json';
                            $req = fopen($arquivo, 'w') or die('Cant open the file');
                            fwrite($req, json_encode($numbersRifa));
                            fclose($req);
                        }


                        $gateway = $this->gerarPIX($prod, $resultPricePIX, $request->email, $request->name, $participante, $request->cpf, $request->telephone);

                        if (isset($gateway['error'])) {
                            return back()->withErrors($gateway['error']);
                        }

                        $codePIXID = $gateway['codePIXID'];
                        $codePIX = $gateway['codePIX'];
                        $qrCode = $gateway['qrCode'];

                        // $codePIXID = $object->id;
                        // $codePIX = $object->point_of_interaction->transaction_data->qr_code;
                        // $qrCode = $object->point_of_interaction->transaction_data->qr_code_base64;

                        $paymentPIX = DB::table('payment_pix')->insert([
                            'key_pix' => $codePIXID,
                            'full_pix' => $codePIX,
                            'status' => 'Pendente',
                            'participant_id' => $participante,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);




                        // Atualiza os numeros escolhidos para reservados
                        DB::table('raffles')
                            ->where('product_id', '=', $request->productID)
                            ->whereIn('raffles.number', $resutlNumbers)
                            ->update(
                                [
                                    'status' => 'Reservado',
                                    'participant_id' => $participante,
                                    'updated_at' => Carbon::now()
                                ]
                            );
                    }

                    $order = Order::create([
                        'key_pix' => $codePIXID,
                        'participant_id' => $participante,
                        'valor' => $price,
                    ]);


                    $countRaffles = count($resutlNumbers);
                    $priceUnicFormat = str_replace(',', '.', $product->price);

                    $percentage = 5;

                    $percentagePriceUnic = ($percentage / 100) * $priceUnicFormat;
                    $resultPriceUnic = $priceUnicFormat + $percentagePriceUnic + 0.50;

                    //dd(number_format($resultPriceUnic, 2, ".", ","));

                    $dadosSave = [
                        'participant_id' => $participante,
                        'participant' => $request->name,
                        'cpf' => $request->cpf,
                        'email' => $request->email,
                        'telephone' => $request->telephone,
                        'price' => $resultPrice,
                        'product' => $product->name,
                        'productID' => $product->id,
                        'drawdate' => $product->draw_date,
                        'image' => $product->image,
                        'PIX' => $resultPricePIX,
                        'countRaffles' => $countRaffles,
                        'priceUnic' => number_format($resultPriceUnic, 2, ".", ","),
                        'codePIX' => $codePIX,
                        'qrCode' => $qrCode,
                        'codePIXID' => $codePIXID
                    ];

                    $order->dados = json_encode($dadosSave);
                    $order->update();

                    DB::commit();
                    return redirect()->route('checkoutManualy', $dadosSave)->withInput();
                } elseif ($statusProduct->status == "Agendado") {
                    return Redirect::back()->withErrors('Sorteio agendado não é mais possível reservar!');
                } else {
                    return Redirect::back()->withErrors('Sorteio finalizado não é mais possível reservar!');
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
        }
    }

    public function gerarPIX(ModelsProduct $product, $resultPricePIX, $email, $name, $participante, $cpf, $telefone)
    {

        $codeKeyPIX = DB::table('consulting_environments')
            ->select('key_pix', 'token_asaas', 'paggue_client_key', 'paggue_client_secret')
            ->where('user_id', '=', 1)
            ->first();

        if ($product->gateway == 'mp') {
            \MercadoPago\SDK::setAccessToken($codeKeyPIX->key_pix);

            $resultPricePIX = str_replace(",", "", $resultPricePIX);

            $payment = new \MercadoPago\Payment();
            $payment->transaction_amount = $resultPricePIX;
            $payment->description = "Participação da ação " . $product->id . ' - ' . $product->name;
            $payment->payment_method_id = "pix";


            $payment->payer = array(
                "email" => $email ? $email : "teste.nienow@email.com",
                "first_name" => $name,
                "identification" => array(
                    "type" => "hash",
                    "number" => date('YmdHis')
                )
            );

            $payment->notification_url   = env('APP_ENV') == 'local' ? '' : route('api.notificaoMP');
            $payment->external_reference = $participante;
            $payment->save();

            $object = (object) $payment;

            if (isset($object->error->message) == 'payer.email must be a valid email') {
                $response['error'] = 'Erro ao gerar o QR Code!';
                return $response;
            }

            if (isset($object->error->message) == 'Invalid user identification number') {
                $response['error'] = 'CPF inválido!';
                return $response;
            }

            $codePIXID = $object->id;
            $codePIX = $object->point_of_interaction->transaction_data->qr_code;
            $qrCode = $object->point_of_interaction->transaction_data->qr_code_base64;

            $response['codePIXID'] = $codePIXID;
            $response['codePIX'] = $codePIX;
            $response['qrCode'] = $qrCode;

            return $response;
        } else if ($product->gateway == 'asaas') {
            $idCliente = $this->getOrCreateClienteAsaas($name, $email, $cpf, $telefone);

            $minutosExpiracao = $product->expiracao;
            $dataDeExpiracao = date('Y-m-d H:i:s', strtotime("+" . $minutosExpiracao . " minutes"));

            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'access_token' => $codeKeyPIX->token_asaas
                ]
            ]);

            $pixURL = 'https://www.asaas.com/api/v3/payments';
            $requestPIX = $client->post($pixURL, [
                'form_params' => [
                    "externalReference" => $participante,
                    "description" => "Participação da ação " . $product->id . ' - ' . $product->name,
                    "customer" =>  $idCliente,
                    "billingType" =>  "PIX",
                    'dueDate' => date('Y-m-d', strtotime($dataDeExpiracao)),
                    "value" =>  $resultPricePIX,
                ]
            ]);

            $responsePIX = json_decode($requestPIX->getBody()->getContents());

            // Capturando QR Code gerado
            $QRURL = $pixURL . '/' . $responsePIX->id . '/pixQrCode';
            $reqQR = $client->get($QRURL);
            $respQR = json_decode($reqQR->getBody()->getContents());

            $response['codePIXID'] = $responsePIX->id;
            $response['codePIX'] = $respQR->payload;
            $response['qrCode'] = $respQR->encodedImage;

            return $response;
        } else if ($product->gateway == 'paggue') {
            include(app_path() . '/ThirdParty/phpqrcode/qrlib.php');

            $payload = array(
                "client_key"    => $codeKeyPIX->paggue_client_key,
                "client_secret" => $codeKeyPIX->paggue_client_secret
            );

            $paggue_curl = curl_init();

            curl_setopt_array($paggue_curl, array(
                CURLOPT_URL => 'https://ms.paggue.io/payments/api/auth/login',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query($payload),
            ));

            $auth_response = json_decode(curl_exec($paggue_curl));

            curl_close($paggue_curl);

            $paggue_token = $auth_response->access_token;
            $paggue_company_id = $auth_response->user->companies[0]->id;

            // Faz a requisição do pagamento
            $payload = array(
                "payer_name" => $name,
                "amount" => $resultPricePIX * 100,
                "external_id" => $participante,
                "description" => "Participação da ação " . $product->id . ' - ' . $product->name,
            );


            $headers = array();
            $headers[] = 'Accept: application/json';
            $headers[] = 'Authorization: Bearer ' . $paggue_token;
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'X-Company-ID: ' . $paggue_company_id;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://ms.paggue.io/payments/api/billing_order',
                CURLOPT_RETURNTRANSFER => true,
                curl_setopt($curl, CURLOPT_POST, 1),
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => $headers
            ));

            $payment_response = json_decode(curl_exec($curl));

            curl_close($curl);

            ob_start();
            \QRCode::png($payment_response->payment, null);
            $imageString = base64_encode(ob_get_contents());
            ob_end_clean();

            $response['codePIXID'] = $payment_response->hash;
            $response['codePIX'] = $payment_response->payment;
            $response['qrCode'] = $imageString;

            $req = fopen('create_paggue.json', 'w') or die('Cant open the file');
            fwrite($req, json_encode($response));
            fclose($req);

            return $response;
        } else {
            $response['codePIXID'] = '';
            $response['codePIX'] = '';
            $response['qrCode'] = '';

            return $response;
        }
    }

    public function getOrCreateClienteAsaas($nome, $email, $cpf, $telefone)
    {
        $codeKeyPIX = DB::table('consulting_environments')
            ->select('key_pix', 'token_asaas')
            ->where('user_id', '=', 1)
            ->first();

        $clientURL = 'https://www.asaas.com/api/v3/customers';
        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'access_token' => $codeKeyPIX->token_asaas
            ]
        ]);

        $params = [
            'query' => [
                'cpfCnpj' => $cpf,
            ]
        ];

        $request = $client->get($clientURL, $params);

        $response = json_decode($request->getBody()->getContents());

        if (count($response->data) > 0) {
            $idCliente = $response->data[0]->id;
        } else {
            $requestClient = $client->post($clientURL, [
                'form_params' => [
                    "name" => $nome,
                    "email" => $email,
                    "cpfCnpj" =>  $cpf,
                    "mobilePhone" => $telefone
                ]
            ]);

            $responseCliente = json_decode($requestClient->getBody()->getContents());
            $idCliente = $responseCliente->id;
        }

        return $idCliente;
    }

    public function participants(Request $request)
    {
        //dd($request->product);

        $participants = Participant::select('name')
            ->join('raffles', 'participant.raffles_id', 'raffles.id')
            ->where('raffles.status', '=', 'Pago')
            ->where('raffles.product_id', '=', $request->product)
            ->inRandomOrder()
            ->count();

        //dd($teste->name);

        return $participants;
    }

    public function searchNumbers(Request $request)
    {
        $substr = substr($request->telephone, 0, 2);
        $ddd = '(' . $substr . ')';
        $substr1 = ' ' . substr($request->telephone, 2, 5) . '-';
        $substr2 = substr($request->telephone, 7);
        $resultTelephone = $ddd . $substr1 . $substr2;

        $numbersPaid = DB::table('participant')
            ->select('raffles.number', 'raffles.status', 'products.name')
            ->join('raffles', 'participant.raffles_id', 'raffles.id')
            ->join('products', 'products.id', 'raffles.product_id')
            ->where('participant.telephone', '=', $resultTelephone)
            ->where('products.id', '=', $request->productID)
            ->where('raffles.status', '=', 'Pago')
            ->get();

        return $numbersPaid;
    }

    public function searchPIX(Request $request)
    {
        $substr = substr($request->telephone, 0, 2);
        $ddd = '(' . $substr . ')';
        $substr1 = ' ' . substr($request->telephone, 2, 5) . '-';
        $substr2 = substr($request->telephone, 7);
        $resultTelephone = $ddd . $substr1 . $substr2;

        $pix = DB::table('participant')
            ->select('raffles.number', 'key_pix')
            ->leftJoin('payment_pix', 'participant.id', 'payment_pix.participant_id')
            ->join('raffles', 'participant.raffles_id', 'raffles.id')
            ->where('participant.telephone', '=', $resultTelephone)
            ->where('participant.product_id', '=', $request->product)
            ->get();

        return $pix;
    }

    public function callbackPaymentMercadoPago(Request $request)
    {

        //$resultCallBacks = $request->all();

        /*$teste = array(
            'action' => 'payment.updated',
            'api_version' => 'v1',
            'data' =>
            array(
                'id' => '53385711687',
            ),
            'date_created' => '2023-01-07T13:07:19Z',
            'id' => 104558878009,
            'live_mode' => true,
            'type' => 'payment',
            'user_id' => '197295574',
            'data_id' => '53385711687',
        );

        Log::info($teste['data']['id']);*/

        //QUANDO FOR TESTAR PELO POSTMAN COLOCAR O VALOR DO ARRAY 0 no request\/
        //dd($request[0]['action']);

        if ($request['action'] == 'payment.updated') {

            DB::table('payment_pix')
                ->where('key_pix', $request['data']['id'])
                ->update(['status' => 'Concluída']);

            $updatingRaffles = DB::table('payment_pix')
                ->join('participant', 'participant.id', '=', 'payment_pix.participant_id')
                ->join('raffles', 'raffles.id', '=', 'participant.raffles_id')
                ->where('payment_pix.key_pix', $request['data']['id'])
                ->update(
                    [
                        'raffles.status' => 'Pago',
                        'raffles.updated_at' => Carbon::now()
                    ]
                );

            $participantEmail = DB::table('payment_pix')
                ->select('participant.name', 'participant.email', 'participant.telephone', 'raffles.*', 'products.name as product', 'products.id as productID', 'products.ebook')
                ->join('participant', 'participant.id', '=', 'payment_pix.participant_id')
                ->join('raffles', 'raffles.id', '=', 'participant.raffles_id')
                ->join('products', 'products.id', '=', 'participant.product_id')
                ->where('payment_pix.key_pix', $request['data']['id'])
                ->get();

            $rafflesNumber = [];

            foreach ($participantEmail as $raffle) {
                array_push($rafflesNumber, $raffle->number);
            }

            $raffleImplode = implode(',', $rafflesNumber);

            $consultingEnvironment = DB::table('consulting_environments')
                ->first();

            $dddTelephone = substr($participantEmail[0]->telephone, 1, 2);
            $n1Telephone = substr($participantEmail[0]->telephone, 5, 5);
            $n2Telephone = substr($participantEmail[0]->telephone, 11, 4);

            $dados = array(
                'name' => $participantEmail[0]->name,
                'email' => $participantEmail[0]->email,
                'product' => $participantEmail[0]->product,
                'productID' => $participantEmail[0]->productID,
                'url' => url('/products/' . $participantEmail[0]->ebook),
                'raffles' => $raffleImplode,
                'environment' => $consultingEnvironment->name,
                'instagram' => $consultingEnvironment->instagram,
                'facebook' => $consultingEnvironment->instagram,
                'searchMyRaffles' => url('reserva/' . $participantEmail[0]->productID . '/' . $dddTelephone . $n1Telephone . $n2Telephone)
            );

            $emailUser = $participantEmail[0]->email;
            $environment = $consultingEnvironment->name;

            Mail::send('mails.payment', ['dados' => $dados], function ($message) use ($emailUser, $environment) {
                $message->from('contato@gosolution.com.br', $environment);
                $message->to($emailUser);
                $message->subject('Ação entre amigos');
            });

            return response()->json(['success' => 'success'], 200);
        } else {

            return response()->json(['error' => 'error'], 404);
        }

        //Log::info($request->all());
    }

    public function ganhadores()
    {

        // $winners = DB::table('products')
        //     ->select('*')
        //     ->where('products.status', '=', 'Finalizado')
        //     ->where('products.visible', '=', 1)
        //     ->orderBy('products.id', 'desc')
        //     ->get();

        $winners = ModelsProduct::where('status', '=', 'Finalizado')->get();

        return view('ganhadores', [
            'winners' => $winners
        ]);
    }

    public function teste()
    {
        $keyPix = 56565819514;
        $codeKeyPIX = DB::table('consulting_environments')
            ->select('key_pix')
            ->where('user_id', '=', 1)
            ->first();

        if (env('APP_ENV') == 'local') {
            $secretKey = 'TEST-330207199077363-081623-283cea3525fa71a8e4d1afa279bf8e8c-197295574';
        } else {
            $secretKey = $codeKeyPIX->key_pix;
        }

        \MercadoPago\SDK::setAccessToken($secretKey);

        $payment = new \MercadoPago\Payment();

        $payment = \MercadoPago\Payment::find_by_id($keyPix);
        $payment->capture = true;
        $payment->update();
        dd($payment);
    }

    public function notificacoesMP(Request $request)
    {
        Storage::put('request.txt', $request);
        try {
            $codeKeyPIX = DB::table('consulting_environments')
                ->select('key_pix')
                ->where('user_id', '=', 1)
                ->first();

            if (env('APP_ENV') == 'local') {
                $accessToken = 'TEST-330207199077363-081623-283cea3525fa71a8e4d1afa279bf8e8c-197295574';
            } else {
                $accessToken = $codeKeyPIX->key_pix;
            }


            \MercadoPago\SDK::setAccessToken($accessToken);
            $payment = \MercadoPago\Payment::find_by_id(56795236797);


            if ($payment) {
                if ($payment->status == 'approved') {
                    //return json_encode($payment->external_reference . ' => aqui');
                    Raffle::where('participant_id', '=', $payment->external_reference)->update(['status' => 'Pago']);
                }
            }

            return response('OK', 200)->header('Content-Type', 'text/plain');
        } catch (\Throwable $th) {
            //throw $th;
            return response('Erro', 404)->header('Content-Type', 'text/plain');
        }
    }

    public function rankingAdmin(Request $request)
    {
        $rifa = ModelsProduct::find($request->id);

        $data = [
            'rifa' => $rifa,
            'ranking' => $rifa->rankingAdmin()
        ];

        $response['html'] = view('ranking-admin', $data)->render();

        return $response;
    }

    public function definirGanhador(Request $request)
    {
        $rifa = ModelsProduct::find($request->id);

        $data = [
            'rifa' => $rifa,
        ];

        $response['html'] = view('layouts.definir-ganhador', $data)->render();

        return $response;
    }

    public function verGanhadores(Request $request)
    {
        $rifa = ModelsProduct::find($request->id);

        $data = [
            'rifa' => $rifa,
        ];

        $response['html'] = view('layouts.ver-ganhadores', $data)->render();

        return $response;
    }

    public function informarGanhadores(Request $request)
    {
        try {
            $rifa = ModelsProduct::find($request->idRifa);
            $premios = $rifa->premios();

            // if (str_starts_with($rifa->modo_de_jogo, 'fazendinha')) {
            //     foreach ($request->cotas as $key => $cota) {
            //         $numero = $rifa->numbers()->where('cota_fazendinha', '=', $cota)->first();
            //         $participante = $numero->participante();
            //         $premios->where('ordem', '=', $key)->first()->update([
            //             'ganhador' => $participante->name,
            //             'telefone' => $participante->telephone,
            //             'cota' => $cota
            //         ]);
            //     }
            // } else {
            //     foreach ($request->cotas as $key => $cota) {
            //         $numero = $rifa->numbers()->where('number', '=', $cota)->first();
            //         $participante = $numero->participante();
            //         $premios->where('ordem', '=', $key)->first()->update([
            //             'ganhador' => $participante->name,
            //             'telefone' => $participante->telephone,
            //             'cota' => $cota
            //         ]);
            //     }
            // }

            foreach ($request->cotas as $key => $cota) {
                $numero = $rifa->numbers()->where('number', '=', $cota)->first();
                $participante = $numero->participante();
                $premios->where('ordem', '=', $key)->first()->update([
                    'ganhador' => $participante->name,
                    'telefone' => $participante->telephone,
                    'cota' => $cota
                ]);
            }

            return redirect()->back()->with('success', 'Ganhadores informados com sucesso!');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function notificacoesPaggue(Request $request)
    {
        $req = fopen('webhook_paggue.json', 'w') or die('Cant open the file');
        fwrite($req, $request);
        fclose($req);

        $participante = Participante::find($request->external_id);
        if ($participante && $request->status == '1') {

            

            Raffle::where('participant_id', '=', $participante->id)->update(['status' => 'Pago']);

            DB::table('payment_pix')->where('participant_id', '=', $participante->id)->update([
                'status' => 'Aprovado'
            ]);

            return response('OK', 200)->header('Content-Type', 'text/plain');
        }
    }
}
