<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use App\Classes\registrarTransacao;

class DepositController extends Controller
{
    private $registrarTransacao;

    public function __construct(registrarTransacao $registrarTransacao)
    {
        $this->registrarTransacao = $registrarTransacao;
    }
    public function store(Request $request)
    {
        //validação do formulario
        $rules = [
            'conta_destino_id' => 'required|exists:accounts,id', // Verifica se a conta de destino existe
            'valor' => 'required|numeric|min:0.01', // Verifica se o valor é positivo
        ];

        //retornar erros de validação
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            //localizar conta de cliente
            $contaDestino = Account::find($request->conta_destino_id);

            // Realiza o depósito
            $saldoAnterior= $contaDestino->saldo;
            $valorDeposito = $request->valor;
            $contaDestino->saldo += $valorDeposito;
            $contaDestino->save();



            // Registra a transação de depósito no histórico
            $this->registrarTransacao->index($contaDestino->id, $saldoAnterior,$contaDestino->saldo, $valorDeposito);

            return response()->json([
                "message" => "Deposito realizado com sucesso no valor de " . $valorDeposito
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Não foi possível interpretar a requisição. Verifique a sintaxe das informações enviadas."
            ], 400);
        }
    }
}
