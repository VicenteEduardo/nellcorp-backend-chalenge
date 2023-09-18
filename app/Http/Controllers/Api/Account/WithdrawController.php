<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use App\Classes\registrarTransacao;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;


class WithdrawController extends Controller
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
            'conta_origem_id' => 'required|exists:accounts,id', // Verifica se a conta de destino existe
            'valor' => 'required|numeric|min:0.01', // Verifica se o valor é positivo
        ];

        //retornar erros de validação
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }


        try {

            $contaOrigem = Account::find($request->conta_origem_id);

            $valorLevantamento = $request->valor;

            // Verifica se a conta de origem pertence ao cliente autenticado
            if ($contaOrigem->cliente_id !== Auth::user()->id) {

                return response()->json([
                    "message" => "Você não pode realizar um levantamento em uma conta que não é sua."
                ], 403);
            }

            // Verifica se o saldo é suficiente para o levantamento
            if ($contaOrigem->saldo < $valorLevantamento) {

                return response()->json([
                    "message" => "Saldo insuficiente para o levantamento."
                ], 400);
            } else {
                // Realiza o levantamento
                $saldoAnterior= $contaOrigem->saldo;
                $contaOrigem->saldo -=  $valorLevantamento;
                $contaOrigem->save();

                // Registra a transação de levantamento no histórico


                $this->registrarTransacao->index($contaOrigem->id, $saldoAnterior,$contaOrigem->saldo,-$valorLevantamento);


                return response()->json([
                    "message" => "Levantamento realizado com sucesso no valor de " . $valorLevantamento
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Não foi possível interpretar a requisição. Verifique a sintaxe das informações enviadas."
            ], 400);
        }
    }
}
