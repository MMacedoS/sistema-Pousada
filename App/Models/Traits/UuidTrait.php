<?php

namespace App\Models\Traits;

trait UuidTrait {
    function generateUUID() {
        // Gera 16 bytes (128 bits) de dados aleatórios
        $data = random_bytes(16);
    
        // Configura a versão para 4
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    
        // Configura os bits de clock_seq_hi_res para 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        // Formata o UUID
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}