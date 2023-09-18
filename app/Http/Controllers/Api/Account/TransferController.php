<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\registrarTransacao;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{

    private $registrarTransacao;

    public function __construct(registrarTransacao $registrarTransacao)
    {
        $this->registrarTransacao = $registrarTransacao;
    }
    public function store(Request $request)
    {

        // Validação dos campos do formulário


        $rules = [
            'conta_origem_id' => 'required|exists:accounts,id', // Verifica se a conta de origem existe
            'conta_destino_id' => 'required|exists:accounts,id', // Verifica se a conta de destino existe
            'valor' => 'required|numeric|min:0.01', // Verifica se o valor é positivo

        ];

        //retornar erros de validação
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $cliente = Auth::user(); // Obtém o cliente autenticado
            $contaOrigem = Account::find($request->conta_origem_id);
            $contaDestino = Account::find($request->conta_destino_id);

            // Verificar se a conta de origem pertence ao cliente autenticado


            $conta = Account::where('id', $request->conta_origem_id)
                ->where('cliente_id', Auth::user()->id)
                ->first();

            if (!$conta) {

                return response()->json([
                    "message" => "Você não pode transferir de uma conta que não é sua. "
                ], 404);
            }


            // Verifica se a conta de origem tem saldo suficiente para a transferência
            if ($contaOrigem->saldo < $request->valor) {

                return response()->json([
                    "message" => "Saldo insuficiente na conta de origem. "
                ], 400);
            }

            // Realiza a transferência
            $saldoAnteriorcontaOrigem = $contaOrigem->saldo;
            $saldoAnteriorcontaDestino = $contaDestino->saldo;

            $valorTransferencia = $request->valor;
            $contaOrigem->saldo -= $valorTransferencia;
            $contaDestino->saldo += $valorTransferencia;
            $contaOrigem->save();
            $contaDestino->save();

            // Registra as transações de débito e crédito no histórico

            $this->registrarTransacao->index($request->conta_origem_id, $saldoAnteriorcontaOrigem, $contaOrigem->saldo, -$valorTransferencia);
            $this->registrarTransacao->index($request->conta_destino_id, $saldoAnteriorcontaDestino, $contaDestino->saldo, $valorTransferencia);

            return response()->json([
                "message" => "Transferência realizada com sucesso  no valor de " . $valorTransferencia
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Não foi possível interpretar a requisição. Verifique a sintaxe das informações enviadas."
            ], 400);
        }
    }
}
