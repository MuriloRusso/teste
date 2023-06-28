<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use App\Models\Raffle;
use App\WhatsappMensagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeAdminController extends Controller
{
    public function index()
    {
        $countSweepstakes = DB::table('products')
            ->select('id')
            ->where('user_id', '=', Auth::user()->id)
            ->count();

        $from = date('Y-m-d');
        $to = date('Y-m-d', strtotime("-20 days", strtotime($from)));

        //dd($to);

        $sales = DB::table('raffles')
            ->selectRaw('raffles.status as payment, raffles.updated_at')
            ->join('products', 'products.id', '=', 'raffles.product_id')
            ->join('users', 'users.id', '=', 'products.user_id')
            ->whereBetween('raffles.updated_at', [$to . ' 00:00:00', $from . ' 23:59:59'])
            ->where('raffles.status', '=', 'Pago')
            ->where('users.id', '=', Auth::user()->id)
            ->orderBy('raffles.updated_at', 'ASC')
            ->get();

        //dd($sales == '[]');

        if ($sales == '[]') {
        } else {
            foreach ($sales as $sale) {
                $key[] = substr($sale->updated_at, 0, 10);
            }

            //dd($key);

            $contagem = array_count_values($key);

            foreach ($contagem as $data => $vezes) {

                //$result[] = $vezes . ' ' . $data;
                $resultCountPayment[] = $vezes;
                $resultCountDate[] = date("d-m-Y", strtotime($data));
            }

            $resultPayment = implode(',', $resultCountPayment);
            $resultDate = implode(',', $resultCountDate);
            $resultDateStr = str_replace('-', '', $resultDate);
        }


        // Calculo dos cards
        $cotasPagas = Raffle::select('raffles.status', 'raffles.participant_id', 'participant.valor')
                            ->join('participant', 'participant.id', '=', 'raffles.participant_id')
                            ->where('raffles.status', '=', 'Pago')
                            ->groupBy('raffles.status', 'raffles.participant_id')->get();

        $pedidos = Participante::all();

        $aguardandoPgto = Raffle::select('raffles.status', 'raffles.participant_id', 'participant.valor')
                            ->join('participant', 'participant.id', '=', 'raffles.participant_id')
                            ->where('raffles.status', '=', 'Reservado')
                            ->groupBy('raffles.status', 'raffles.participant_id')->get();

        return view('home-admin', [
            'cotasPagas' => $cotasPagas,
            'pedidos' => $pedidos,
            'aguardandoPgto' => $aguardandoPgto,
            'countSweepstakes' => $countSweepstakes,
            'resultPayment' => @$resultPayment,
            'resultDate' => @$resultDateStr
        ]);
    }

    public function wpp()
    {
        if(WhatsappMensagem::all()->count() == 0){
            for ($i=0; $i < 6; $i++) { 
                WhatsappMensagem::create([]);
            }
        }

        $data = [
            'msgs' => WhatsappMensagem::all()
        ];


        return view('wpp-msgs.index', $data);
    }

    public function wppSalvar(Request $request)
    {
        foreach ($request->id as $key => $value) {
            WhatsappMensagem::find($value)->update([
                'titulo' => $request->titulo[$value],
                'msg' => nl2br($request->msg[$value]),
            ]);
        }

        return redirect()->back()->with('success', 'Mensagens atualizadas com sucesso!');
    }
}
