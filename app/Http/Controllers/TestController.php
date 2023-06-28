<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use App\Models\Product;
use App\Models\Raffle;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Http;
use Mpdf\Mpdf;

//use Barryvdh\DomPDF\Facade\Pdf;

class TestController extends Controller
{
    public function index()
    {
        $lines = file(storage_path('pipocas.txt'));

        $list = array_unique($lines);

        //dd($list);
        foreach ($list as $line) {

            $result = str_replace(array("(", "'", ";", ")", "-"), '', $line);

            $resultNumbers = substr($result, 0, -1);


            $countNumberLine = strlen(str_replace(' ', '', $resultNumbers));
            //strlen($result)

            //dd($resultNumbers);

            if ($countNumberLine == 11) {
                if (str_replace(' ', '', $resultNumbers) == "41999999999" || str_replace(' ', '', $resultNumbers) == "99999999999" || str_replace(' ', '', $resultNumbers) == "13997531259") {
                } else {
                    echo str_replace(' ', '', $resultNumbers) . "<br>";
                }
            } else {
            }
        }
    }

    public function wdm()
    {
        $rifas = Product::where('modo_de_jogo', '=', 'numeros')->get();

        foreach ($rifas as $rifa) {
            $path = 'numbers/' . $rifa->id . '.json';
            $oldNumbers = Raffle::where('product_id', '=', $rifa->id)->get();

            if (!file_exists($path) && $oldNumbers->count() > 0) {
                $arr = [];
                for ($x = 0; $x < $rifa->qtd; $x++) {
                    $arr[$x] = [
                        'key' => $x,
                        'number' => str_pad($x, strlen((string)$rifa->qtd),  '0', STR_PAD_LEFT),
                        'status' => $oldNumbers[$x]->status == 'Disponível' ? 'Disponivel' : $oldNumbers[$x]->status,
                        'participant_id' => $oldNumbers[$x]->participant_id,
                    ];
                }

                $numbers = json_encode($arr);

                $req = fopen(public_path() . '/' . $path, 'w') or die('Cant open the file');
                fwrite($req, $numbers);
                fclose($req);
                
            }

            foreach ($rifa->participantes() as $participante) {
                $arr = [];

                $participanteNumbers = Raffle::where('participant_id', '=', $participante->id)->get();

                $arr = [];
                foreach ($participanteNumbers as $key => $raffle) {
                    $arr[$key] = [
                        'key' => intval($raffle->number),
                        'number' => $raffle->number,
                        'status' => $raffle->status == 'Disponível' ? 'Disponivel' : $raffle->status,
                        'participant_id' => $raffle->participant_id,
                    ];
                }

                $pagos = array_filter($arr, function($num){
                    return $num['status'] == 'Pago';
                });

                $reservados = array_filter($arr, function($num){
                    return $num['status'] == 'Reservado';
                });

                $participante->update([
                    'numbers' => json_encode($arr),
                    'pagos' => count($pagos),
                    'reservados' => count($reservados)
                ]);
                
            }
        }

        dd('finalizado');
    }
}
