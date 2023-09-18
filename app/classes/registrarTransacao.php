<?php

namespace App\Classes;

use App\Models\Transaction as ModelTransaction;

class registrarTransacao
{
    /**class responsavel por cadastrar as transações */
    public function index($conta,$saldoAnterior,$saldoAtualizado, $valor)
    {
        ModelTransaction::create([
            'conta_id' => $conta,
            'valor' => $valor,
            'saldoAnterior' => $saldoAnterior,
            'saldoAtualizado' => $saldoAtualizado
        ]);

    }
}
