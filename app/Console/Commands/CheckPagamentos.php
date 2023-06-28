<?php

namespace App\Console\Commands;

use App\Models\Participante;
use App\Models\Raffle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckPagamentos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pix:check-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificando pagamentos pendentes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $codeKeyPIX = DB::table('consulting_environments')
            ->select('key_pix')
            ->where('user_id', '=', 1)
            ->first();

        $secretKey = $codeKeyPIX->key_pix;

        \MercadoPago\SDK::setAccessToken($secretKey);

        $pendentes = DB::table('payment_pix')->where('status', '=', 'Pendente')->where('key_pix', '!=', '')->get();
        foreach ($pendentes as $value) {
            try {
                $realPixID = $value->key_pix;

                $payment = \MercadoPago\Payment::find_by_id($realPixID);

                if ($payment) {
                    if ($payment->status == 'approved') {
                        $participante = Participante::find($payment->external_reference);
                        $rifa = $participante->rifa();
                        $rifa->confirmPayment($participante->id);

                        //Raffle::where('participant_id', '=', $payment->external_reference)->update(['status' => 'Pago']);

                        DB::table('payment_pix')->where('id', '=', $value->id)->update([
                            'status' => 'Aprovado'
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                dd($value);
            }
        }
    }
}
