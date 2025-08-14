<?php

namespace App\Transformers\Cashbox;

use App\Models\Cashbox\Caixa;
use App\Repositories\Entities\User\UsuarioRepository;

class CaixaTransformer
{

    public function transform(Caixa $caixa)
    {
        if (!$caixa) {
            return null;
        }

        // Convert to object if it's an array
        if (is_array($caixa)) {
            $caixa = (object) $caixa;
        }

        return [
            'code' => (int)$caixa->id,
            'id' => $caixa->uuid,
            'opened_at' => $caixa->opened_at,
            'closed_at' => $caixa->closed_at,
            'initial_amount' => $caixa->initial_amount ?? 0,
            'current_balance' => $caixa->current_balance ?? 0,
            'final_amount' => $caixa->final_amount ?? 0,
            'difference' => $caixa->difference ?? 0,
            'name' => $this->transformUser($caixa->id_usuario_opened),
            'status' => $caixa->status,
            'obs' => $caixa->obs ?? null
        ];
    }

    public function transformCollection($caixas)
    {
        return array_map(fn($caixa) => $this->transform($caixa), $caixas);
    }

    private function transformUser($userId)
    {
        $userRepository = UsuarioRepository::getInstance();
        dd($userRepository); // Debugging line to check the user ID
        $user = $userRepository->findById($userId);
        return $user ? $user->name : 'Usuário Desconhecido';
    }
}
