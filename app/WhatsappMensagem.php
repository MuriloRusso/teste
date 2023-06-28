<?php

namespace App;

use App\Models\Participante;
use Illuminate\Database\Eloquent\Model;

class WhatsappMensagem extends Model
{
    protected $fillable = [
        'titulo',
        'msg',
    ];

    public function clearBreak()
    {
        return str_replace("<br />", "", $this->msg);
    }

    public function generateLink(Participante $participante)
    {
        $variaveis = [
            'id',
            'nome',
            'valor',
            'total',
            'cotas',
            'sorteio',
            'link'
        ];

        $link = $participante->linkWpp();

        $link .= '&text=' . $this->msg;

        $link = str_replace("<br />", "%0A", $link);

        foreach ($variaveis as $variavel) {
            $replace = $this->replaceKey($variavel, $participante);

            $var = "{" . $variavel . "}";

            $link = str_replace($var, $replace, $link);
        }

        return $link;
    }

    public function replaceKey($key, Participante $participante)
    {
        switch ($key) {
            case 'id':
                return $this->id;
                break;
            case 'nome':
                return $participante->name;
                break;
            case 'valor':
                return $participante->rifa()->price;
                break;
            case 'total':
                return number_format($participante->valor, 2, ",", ".");
                break;
            case 'cotas': // TODO
                $cotas = '';
                foreach ($participante->cotas() as $key => $value) {
                    if($key != 0){
                        $cotas .= ',';
                    }
                    $cotas .= $value->number;
                }
                return $cotas;
                break;
            case 'sorteio':
                return $participante->rifa()->name;
                break;
            case 'link': // TODO
                return route('pagarReserva', $this->id);
                break;
            default:
                return $key;
                break;
        }
    }
}
