<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        //validação do formulario
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ];

        //retornar erros de validação
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }



        // Cria um novo usuário
        $user = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        // Cria conta do user

        $user->save();
        Account::create([
            'cliente_id'=>$user->id,
            'saldo'=>"0"
        ]);

        return response()->json(['message' => 'Parabéns sua conta foi criada faça o login para ter acesso ao seu saldo'], 201);
    }

    /**
     * Realiza o login e retorna um token de acesso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validação dos campos do formulário
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $user = $request->user();
        /**gerar o token */
        $token = $user->createToken('nellcorp')->plainTextToken;

        return response()->json(['token' => $token], 200);
    }

    /**
     * Realiza o logout do usuário autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function issueToken(Request $request)
    {
        $token = parent::issueToken($request);

        // Personalize o token aqui
        $user = $request->user();
        $token->user_id = $user->id;
        $token->custom_data = 'Alguma informação personalizada';

        return $token;
    }
}
