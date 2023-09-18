<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\registrarTransacao;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class ReimbursementController extends Controller
{
    private $registrarTransacao;

    public function __construct()
    {
        $this->registrarTransacao = new registrarTransacao();
    }
    public function store(Request $request)
    {


        //validação do formulario
        $rules = [
            'transacao_id' => 'required|exists:transactions,id',  // Verifique se a transação existe
        ];

        //retornar erros de validação
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {

        //localizar pedido  de transação
        $transacao = Transaction::find($request->transacao_id);

        // Período de tempo permitido para reembolso (30 dias)
        $periodoPermitidoParaReembolso = now()->subDays(30);

        // Verifique se a data da transação está dentro do período permitido
        if ($transacao->created_at >= $periodoPermitidoParaReembolso) {
            // A transação está dentro do período permitido para reembolso


            //localizar conta de cliente
            $contaCliente = Account::find($transacao->conta_id);

            // Realiza o reembolso
            $saldocliente =  $contaCliente->saldo ;
            $valorReembolso =  $transacao->valor;
            $contaCliente->saldo -= $valorReembolso;
            $contaCliente->save();

            // Registra a transação de reembolso no histórico

            $this->registrarTransacao->index($contaCliente->cliente_id, $saldocliente, $contaCliente->saldo, -$valorReembolso);
            return response()->json([
                "message" => " reembolso feito com sucesso"
            ], 200);
        } else {
            return response()->json([
                "message" => "A transação não está dentro do período permitido para reembolso"
            ], 400);
        }

        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Não foi possível interpretar a requisição. Verifique a sintaxe das informações enviadas."
            ], 400);
        }
    }
}
