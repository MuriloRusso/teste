<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//SUBDOMAINs para os consultores

use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//ALTERAR O DOMINIO QUANDO SUBIR OUTRO SISTEMA
/*Route::domain('{domain}.clicksorte.com')->group(function () {
    Route::get('/', 'ConsultantController@index')->name('consultant');
    Route::get('regulamento', 'ConsultantController@regulationConsultant')->name('regulationConsultant');
});*/

//TRAS AS CONFIGS DE CADA CONSULTOR
//Route::post('environment', 'ConsultantController@consultingEnvironment')->name('consultingEnvironment');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
//Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
//Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::get('ganhadores', 'ProductController@ganhadores')->name('ganhadores');
Route::get('cadastra-participanteAuto/{telephone}', 'ProductController@bookProductAuto')->name('bookProductAuto');
Route::group(['middleware' => ['auth']], function () {
    Route::get('home', 'HomeAdminController@index')->name('home');
    Route::get('adicionar-sorteio', 'ProductAdminController@index')->name('adminProduct');
    Route::post('add-sorteio', 'ProductAdminController@addProduct')->name('addProduct');
    Route::put('update/{id}', 'MySweepstakesController@update')->name('update');
    Route::delete('destroy', 'ProductAdminController@destroy')->name('destroy');
    Route::post('agendar-sorteio', 'ProductAdminController@drawDate')->name('drawDate');
    Route::post('previsao-sorteio', 'ProductAdminController@drawPrediction')->name('drawPrediction');
    Route::get('meus-sorteios', 'MySweepstakesController@index')->name('mySweepstakes');
    Route::get('clientes', 'UserController@index')->name('User');

    Route::any('liberar-reservas', 'MySweepstakesController@releaseReservedRafflesNumbers')->name('releaseReservedRafflesNumbers');
    Route::any('pagar-reservas', 'MySweepstakesController@pagarReservas')->name('pagarReservas');
    Route::any('reservar-numeros', 'MySweepstakesController@reservarNumeros')->name('reservarNumeros');
    Route::get('carrega-sorteio', 'MySweepstakesController@getRaffles')->name('getRaffles');
    Route::post('altera-sorteio', 'MySweepstakesController@editRaffles')->name('editRaffles');
    Route::post('altera-produto', 'ProductAdminController@alterProduct')->name('alterProduct');
    Route::get('perfil', 'MySweepstakesController@profile')->name('profile');
    Route::post('perfil', 'MySweepstakesController@updateProfile')->name('updateProfile');
    Route::post('alterar-logo', 'ProductAdminController@alterarLogo')->name('alterarLogo');
    Route::post('pixel', 'MySweepstakesController@pixel')->name('pixel');
    //Route::post('cobrar-reserva', 'MySweepstakesController@chargeReservation')->name('chargeReservation');
    //Route::post('obrigado', 'MySweepstakesController@thanks')->name('thanks');
    Route::post('remover-reservas', 'MySweepstakesController@removeReserved')->name('removeReserved');
    Route::post('altera-status-produto', 'ProductAdminController@alterStatusProduct')->name('alterStatusProduct');
    Route::post('altera-winner-produto', 'ProductAdminController@alterWinnerProduct')->name('alterWinnerProduct');
    Route::post('altera-tipo-produto', 'ProductAdminController@alterTypeRafflesProduct')->name('alterTypeRafflesProduct');
    Route::get('participants', 'TestController@index')->name('test');
    Route::post('favoritar-produto', 'ProductAdminController@favoritarRifa')->name('favoritarRifa');
    Route::patch('edit-product/{id}', 'MySweepstakesController@updateProduct')->name('updateProduct');
    Route::post('add-foto-rifa', 'ProductAdminController@addFoto')->name('addFoto');
    Route::get('resumo-rifa-pdf/{id}', 'MySweepstakesController@resumoPDF')->name('resumoRifaPDF');
    Route::post('/excluir-foto', 'MySweepstakesController@excluirFoto')->name('excluirFoto');


    //  WDW
    
    Route::post('/ranking-admin', 'ProductController@rankingAdmin')->name('ranking.admin');
    Route::post('/definir-ganhador', 'ProductController@definirGanhador')->name('definirGanhador');
    Route::post('/informar-ganhadores', 'ProductController@informarGanhadores')->name('informarGanhadores');
    Route::post('/ver-ganhadores', 'ProductController@verGanhadores')->name('verGanhadores');

    // WDM - Compras
    Route::get('/compras/{idRifa}', 'MySweepstakesController@compras')->name('rifa.compras');
    Route::put('/compras/{idRifa}', 'MySweepstakesController@comprasBusca')->name('rifa.comprasBusca');
    Route::post('/liberar-todas-reservas', 'MySweepstakesController@liberarTodasReservas')->name('compras.liberarReservas');
    Route::post('random-numbers', 'MySweepstakesController@randomNumbers')->name('compras.randomNumbers');
    Route::post('/criar-compra', 'MySweepstakesController@criarCompra')->name('compras.criar');
    Route::post('/build-modal-detalhes-compra', 'MySweepstakesController@detalhesCompra')->name('compras.detalhes');

    // WDM - Whatsapp mensagens
    Route::get('/wpp-mensagens', 'HomeAdminController@wpp')->name('wpp.index');
    Route::post('/wpp-mensagens/salvar', 'HomeAdminController@wppSalvar')->name('wpp.salvar');

    // Ganhadores
    Route::get('/admin-ganhadores', 'MySweepstakesController@ganhadores')->name('painel.ganhadores');
    Route::post('/add-foto-ganhador', 'MySweepstakesController@addFotoGanhador')->name('ganhador.addFoto');

    // WDM - Tutoriais
    Route::get('/tutoriais', 'MySweepstakesController@tutoriais')->name('tutoriais');
    Route::get('/tutoriais/cadastro', 'MySweepstakesController@cadastroVideos');
    Route::post('/tutoriais/cadastro', 'MySweepstakesController@salvarVideo')->name('dev.salvarVideo');
    Route::get('/tutoriais/excluir-video/{id}', 'MySweepstakesController@excluirVideo')->name('dev.excluirVideo');

});

Route::get('/pagar-reserva/{id}', 'CheckoutController@pagarReserva')->name('pagarReserva');

Route::get('/', 'ProductController@index')->name('inicio');
Route::get('sorteio/{id}', 'ProductController@product')->name('product');
Route::post('buscar-numeros', 'ProductController@getRaffles')->name('getRafflesAjax');
//QUANDO UTILIZAR O PIX MANUAL COLOCAR O bookProductManualy NA VIEW DE RESERVAR NUMERO
Route::post('cadastra-participante', 'ProductController@bookProduct')->name('bookProduct');
Route::post('cadastra-participante1', 'ProductController@bookProductManualy')->name('bookProductManualy');
//Route::post('cadastra-participanteAuto', 'ProductController@bookProductAuto')->name('bookProductAuto');
Route::get('regulamento', 'RegulationController@index')->name('regulation');
Route::post('participantes', 'ProductController@participants')->name('participants');
Route::post('pagamento-pix', 'CheckoutController@paymentPix')->name('paymentPix');
Route::post('pagamento-credito', 'CheckoutController@paymentCredit')->name('paymentCredit');
Route::post('pesquisa-numeros', 'ProductController@searchNumbers')->name('searchNumbers');
Route::post('pesquisa-pix', 'ProductController@searchPIX')->name('searchPIX');
//QUANDO UTILIZAR O PIX MANUAL COLOCAR O checkoutManualy
Route::get('checkout', 'CheckoutController@index')->name('checkout');
Route::get('checkout-manualy', 'CheckoutController@checkoutManualy')->name('checkoutManualy');
Route::get('checkout-pixsuccess', 'CheckoutController@checkPixPaymment')->name('checkPixPaymment');
Route::any('checkout-success/{id}', 'CheckoutController@findPixStatus')->name('findPixStatus');
Route::any('checkout-visualizar-pedidos/{id}', 'CheckoutController@findPedidoStatus')->name('findPedidoStatus');
//QUANDO UTILIZAR O PIX MANUAL COLOCAR AS DUAS FUNC ABAIXO
Route::post('consultar-reserva', 'CheckoutController@consultingReservation')->name('consultingReservation');
Route::get('reserva/{productID}/{telephone}', 'CheckoutController@consultingReservationTelephone')->name('consultingReservationTelephone');
Route::post('minhas-reservas/', 'CheckoutController@minhasReservas')->name('minhasReservas');
//Route::post('consultar-reserva-manual', 'CheckoutController@consultingReservationManualy')->name('consultingReservationManualy');
//Route::get('reserva-manual/{productID}/{telephone}', 'CheckoutController@consultingReservationTelephoneManualy')->name('consultingReservationTelephoneManualy');

Route::get('terms-of-use', 'TermsOfUse@index')->name('terms');

Route::post('/random-participant', 'ProductController@randomParticipant')->name('randomParticipant');
Route::get('/reset-pass', 'Controller@resetPass');



// WDM Routes
Route::get('/pull-wdm', 'Controller@pull');
Route::get('/migrate', 'Controller@migrate');
Route::get('/update', 'Controller@updateOldRaffles');
Route::get('/update-footer', 'Controller@updateFooter');

Route::get('teste123', 'TestController@wdm');





/*Route::get('/send', function () {
    broadcast(new \App\Events\SendMessage);
    return 'done';
});*/

/*Route::get('/messages', function () {
    return view('message');
});*/

/*Route::post('/messages', function () {
    $data = request()->all();
    
    $message = \App\Message::create($data);
    broadcast(new \App\Events\SendMessage($message));

    return redirect('/messages');
});*/
