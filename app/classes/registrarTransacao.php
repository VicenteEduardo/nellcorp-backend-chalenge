<?php

namespace App\Classes;

use App\Models\Transaction as ModelTransaction;

class registrarTransacao
{
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
