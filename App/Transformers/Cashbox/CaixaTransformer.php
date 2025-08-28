<?php

namespace App\Transformers\Cashbox;

use App\Models\Cashbox\Caixa;
use App\Repositories\Entities\Cashbox\TransacaoCaixaRepository;
use App\Repositories\Entities\User\UsuarioRepository;

class CaixaTransformer
{
    private const TYPE_INPUT = 'entrada';
    private const TYPE_OUTPUT = 'saida';
    private const TYPE_BLOOD = 'sangria';

    public function transform(Caixa $caixa)
    {
        if (!$caixa) {
            return null;
        }

        if (is_array($caixa)) {
            $caixa = (object) $caixa;
        }

        return [
            'code' => (int)$caixa->id,
            'id' => $caixa->uuid,
            'name' => $this->transformUser($caixa->id_usuario_opened),
            'opened_at' => $caixa->opened_at,
            'closed_at' => $caixa->closed_at,
            'initial_amount' => $caixa->initial_amount ?? 0,
            'current_balance' => $caixa->current_balance ?? 0,
            'final_amount' => $caixa->final_amount ?? 0,
            'difference' => $caixa->difference ?? 0,
            'total_input' => $this->releaseTotalTypeTransactions($caixa->id, self::TYPE_INPUT),
            'total_output' => $this->releaseTotalTypeTransactions($caixa->id, self::TYPE_OUTPUT),
            'total_bleed_box' => $this->releaseTotalTypeTransactions($caixa->id, self::TYPE_BLOOD),
            'status' => $caixa->status,
            'obs' => $caixa->obs ?? null
        ];
    }

    private function releaseTotalTypeTransactions($caixaId, string $type)
    {
        $transactionRepository = TransacaoCaixaRepository::getInstance();
        $transactions = $transactionRepository->findByCashboxIdAndType($caixaId, $type);

        if (!$transactions) {
            return 0;
        }

        $total = 0;
        foreach ($transactions as $transaction) {
            $total += $transaction->amount;
        }

        return $total;
    }

    public function transformCollection($caixas)
    {
        return array_map(fn($caixa) => $this->transform($caixa), $caixas);
    }

    private function transformUser($userId)
    {
        $userRepository = UsuarioRepository::getInstance();
        $user = $userRepository->findById($userId);
        return $user ? $user->name : 'Usuário Desconhecido';
    }
}
