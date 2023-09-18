<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use App\Classes\registrarTransacao;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HistoryAccountController extends Controller
{
    public function index(Request $request)
    {
        //validação do formulario
        $rules = [
            'conta_id' => 'required|exists:accounts,id', // Verifica se a conta de de cliente existe

        ];

        //retornar erros de validação
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {


            // Recupere a conta associada ao usuário autenticado
            $conta = Account::where('id', $request->conta_id)
                ->where('cliente_id', Auth::user()->id)
                ->first();

            if (!$conta) {

                return response()->json([
                    "message" => "Conta não encontrada ou não pertence ao usuário autenticado "
                ], 404);
            }

            //localizar historico de cliente
            $historico = Transaction::where('conta_id', $request->conta_id)->orderBy('created_at', 'desc')
                ->get();


            if (!isset($historico)) {
            } else {
                return response()->json([
                    "message" => "historico da sua conta " . $historico
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Não foi possível interpretar a requisição. Verifique a sintaxe das informações enviadas."
            ], 400);
        }
    }
}
