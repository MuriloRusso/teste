<?php

namespace App\Models;

use App\CreateProductimage;
use App\Promocao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $table = 'products';

    public function numbers()
    {
        if ($this->modo_de_jogo == 'numeros') {
            $path = 'numbers/' . $this->id . '.json';
            $jsonString = file_get_contents($path);
            $numbersRifa = json_decode($jsonString, true);

            return $numbersRifa;
        } else {
            return $this->hasMany(Raffle::class, 'product_id', 'id')->get();
        }
    }

    public function saveNumbers($numbersArray)
    {
        $arquivo = 'numbers/' . $this->id . '.json';
        $req = fopen($arquivo, 'w') or die('Cant open the file');
        fwrite($req, json_encode($numbersArray));
        fclose($req);
    }

    public function qtdNumerosDisponiveis()
    {
        if($this->modo_de_jogo == 'numeros'){
            return $this->qtd - $this->qtdNumerosReservados() - $this->qtdNumerosPagos();
        }
        else{
            return $this->hasMany(Raffle::class, 'product_id', 'id')->where('status', '=', 'Disponível')->get()->count();
        }
    }

    public function randomNumbers($qtd)
    {
        $randomNumbers = DB::table('raffles')
            ->select('number')
            ->where('raffles.product_id', '=', $this->id)
            ->where('raffles.status', '=', 'Disponível')
            ->inRandomOrder()
            ->limit($qtd)
            ->get();

        return $randomNumbers;
    }

    public function numerosDisponiveis()
    {
        $response = [];
        $numeros = $this->hasMany(Raffle::class, 'product_id', 'id')->where('status', '=', 'Disponível')->get();

        foreach ($numeros as $numero) {
            array_push($response, $numero->number);
        }

        return $response;
    }

    public function qtdNumerosReservados()
    {
        if($this->modo_de_jogo == 'numeros'){
            return $this->participantes()->sum('reservados');
        }
        else{
            return $this->hasMany(Raffle::class, 'product_id', 'id')->where('status', '=', 'Reservado')->get()->count();
        }
    }

    public function numerosReservados()
    {
        return $this->hasMany(Raffle::class, 'product_id', 'id')->where('status', '=', 'Reservado')->get();
    }

    public function qtdNumerosPagos()
    {
        if($this->modo_de_jogo == 'numeros'){
            return $this->participantes()->sum('pagos');
        }
        else{
            return $this->hasMany(Raffle::class, 'product_id', 'id')->where('status', '=', 'Pago')->get()->count();
        }
    }

    public function porcentagem()
    {
        $numerosUtilizados = $this->qtdNumerosReservados() + $this->qtdNumerosPagos();
        $totalDaRifa = $this->qtd;
        
        $percentual = ($numerosUtilizados * 100) / $totalDaRifa;

        return $percentual;
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class, 'product_id', 'id')->orderBy('id', 'desc')->get();
    }

    public function participantesReservados()
    {
        $numeros = Raffle::select('participant_id')
            ->where('product_id', '=', $this->id)
            ->where('status', '=', 'Reservado')
            ->groupBy('participant_id')
            ->get();

        return $numeros;
    }

    public function promocoes()
    {
        return $this->hasMany(Promocao::class, 'product_id', 'id')->orderBy('ordem', 'asc')->get();
    }

    public function promosAtivas()
    {
        $promocoes = $this->promocoes()->where('qtdNumeros', '>', 0);
        $result = [];
        foreach ($promocoes as $promocao) {
            array_push($result, [
                'numeros' => $promocao->qtdNumeros,
                'desconto' => $promocao->desconto
            ]);
        }

        return json_encode($result);
    }

    public function imagem()
    {
        return $this->hasOne(CreateProductimage::class, 'product_id', 'id')->first();
    }

    public function fotos()
    {
        return $this->hasMany(CreateProductimage::class, 'product_id', 'id')->limit(3)->get();
    }

    public function numbersRelatorio()
    {
        return $this->hasMany(Raffle::class, 'product_id', 'id')->where('participant_id', '!=', null)->orderBy('number', 'asc')->get();
    }

    public function medalhaRanking($posicao)
    {
        switch ($posicao) {
            case '0':
                return '🥇';
                break;
            case '1':
                return '🥈';
                break;
            case '2':
                return '🥉';
                break;
            default:
                return '🏅';
                break;
        }
    }

    public function ranking()
    {

        $ranking = DB::table('raffles')
            ->select(DB::raw('COUNT(raffles.id) as totalReservas'), 'participant.telephone', 'participant.name')
            ->where('raffles.product_id', '=', $this->id)
            ->where('raffles.participant_id', '!=', null)
            ->where('raffles.status', '=', 'Pago')
            ->join('participant', 'participant.id', '=', 'raffles.participant_id')
            ->groupBy('participant.telephone')
            ->orderBy('totalReservas', 'desc')
            ->limit($this->qtd_ranking)
            ->get();

        return $ranking->toArray();
    }

    public function rankingAdmin()
    {

        $ranking = DB::table('raffles')
            ->select(DB::raw('COUNT(raffles.id) as totalReservas'), 'participant.telephone', 'participant.name')
            ->where('raffles.product_id', '=', $this->id)
            ->where('raffles.participant_id', '!=', null)
            ->where('raffles.status', '=', 'Pago')
            ->join('participant', 'participant.id', '=', 'raffles.participant_id')
            ->groupBy('participant.telephone')
            ->orderBy('totalReservas', 'desc')
            ->limit(8)
            ->get();

        return $ranking->toArray();
    }

    public function descricao()
    {
        $desc = $this->hasOne(DescricaoProduto::class, 'product_id', 'id')->first();
        if ($desc) {
            return $desc->description;
        } else {
            return '';
        }
    }

    public function premios()
    {
        $premios = $this->hasMany(Premio::class, 'product_id', 'id')->orderBy('ordem', 'asc')->get();

        if ($premios->count() === 0) {
            for ($i = 1; $i <= 10; $i++) {
                Premio::create([
                    'product_id' => $this->id,
                    'ordem' => $i,
                    'descricao' => '',
                    'ganhador' => '',
                    'cota' => ''
                ]);
            }

            return $this->hasMany(Premio::class, 'product_id', 'id')->orderBy('ordem', 'asc')->get();
        } else {
            return $premios;
        }
    }

    public function status()
    {
        // teste 123
        switch ($this->status) {
            case 'Ativo':
                if ($this->porcentagem() >= 80) {
                    $status = '<span class="badge mt-2 blink" style="color: #fff; background-color: #f38e02">Corre que está acabando!</span>';
                } else {
                    $status = '<span class="badge mt-2 bg-success blink">Adquira já!</span>';
                }
                break;
            case 'Finalizado':
                if ($this->premios()->where('descricao', '!=', '')->where('ganhador', '!=', '')->count() == 0) {
                    $status = '<span class="badge bg-primary mt-2 blink" style="color: #fff">Esgotado! Aguarde sorteio!</span>';
                } else {
                    $status = '<span class="badge mt-2 bg-danger">Finalizado</span>';
                }

                break;
            default:
                $status = '';
                break;
        }

        return $status;
    }

    public function dataSorteio()
    {
        switch ($this->status) {
            case 'Ativo':
                if ($this->porcentagem() >= 80) {
                    $sorteioStatus = '<span class="badge mt-2 bg-warning" style="color: #000">' . date('d/m/Y', strtotime($this->draw_date)) . '</span>';
                } else {
                    $sorteioStatus = '<span class="badge mt-2 bg-success">' . date('d/m/Y', strtotime($this->draw_date)) . '</span>';
                }
                break;
            case 'Finalizado':
                if ($this->premios()->where('descricao', '!=', '')->where('ganhador', '!=', '')->count() == 0) {
                    $sorteioStatus = '<span class="badge mt-2" style="background: orange; color: #000">' . date('d/m/Y', strtotime($this->draw_date)) . '</span>';
                } else {
                    $sorteioStatus = '<span class="badge mt-2 bg-danger">' . date('d/m/Y', strtotime($this->draw_date)) . '</span>';
                }

                break;
            default:
                $sorteioStatus = '';
                break;
        }

        return $sorteioStatus;
    }

    public function getParticipanteById($id)
    {
        return Participante::find($id);
    }

    public function confirmPayment($participanteId)
    {
        if ($this->modo_de_jogo == 'numeros') {
            $participante = Participante::find($participanteId);

            $numbersParticipante = $participante->numbers();
            $rifaNumbers = $participante->rifa()->numbers();

            foreach ($numbersParticipante as $number) {
                $number->status = 'Pago';
                $rifaNumbers[$number->key]['status'] = 'Pago';
            }

            $participante->update([
                'numbers' => json_encode($numbersParticipante),
                'reservados' => 0,
                'pagos' => count($numbersParticipante)
            ]);

            $this->saveNumbers($rifaNumbers);
        } else {
            Raffle::where('participant_id', '=', $participanteId)->update(['status' => 'Pago']);
        }
    }
}
