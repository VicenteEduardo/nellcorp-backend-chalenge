<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\AccessTokenController as BaseAccessTokenController;
class AccessTokenController extends Controller
{
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
