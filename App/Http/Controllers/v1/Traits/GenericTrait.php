<?php
 
namespace App\Controllers\v1\Traits;

trait GenericTrait {
    public function sumAbsences($frequencias)
    {
        return array_reduce($frequencias, function ($carry, $item) {
            return $carry + $item->faltas;
        }, 0);
    }

    public function sumMonthlyFees($monthlyfees, $situation = null)
    {
        return array_reduce($monthlyfees, function ($monthly, $item) use ($situation) {
            if (is_null($situation) || $item->situacao === $situation) {
                return $monthly + $item->valor;
            }
            return $monthly; // Não soma se a situação não corresponde
        }, 0);
    }    

    public function calculatePercentage($partial, $total) {
        return $total > 0 ? round(($partial / $total) * 100, 2) : 0;
    }

    public function formatName(string $nome): string
    {
        $nome = mb_strtolower($nome, 'UTF-8');

        $preposicoes = ['da', 'de', 'do', 'das', 'dos'];

        $nome = mb_convert_case($nome, MB_CASE_TITLE, 'UTF-8');

        $palavras = explode(' ', $nome);
        foreach ($palavras as &$palavra) {
            if (in_array(mb_strtolower($palavra, 'UTF-8'), $preposicoes)) {
                $palavra = mb_strtolower($palavra, 'UTF-8');
            }
        }

        return implode(' ', $palavras);
    }

    public function generateSlug($title) 
    {
        // Converter para minúsculas
        $slug = strtolower($title);
        
        // Remover acentos
        $slug = preg_replace(
            array('/[áàãâä]/u', '/[éèêë]/u', '/[íìîï]/u', '/[óòõôö]/u', '/[úùûü]/u', '/[ç]/u', '/[ñ]/u'),
            array('a', 'e', 'i', 'o', 'u', 'c', 'n'),
            $slug
        );
        
        // Remover caracteres especiais
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        
        // Substituir espaços por hífens
        $slug = preg_replace('/[\s]+/', '-', $slug);
        
        // Remover hífens duplicados
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Limitar o tamanho do slug
        $slug = substr($slug, 0, 255);
        
        // Remover hífens do início e final
        $slug = trim($slug, '-');
        
        return $slug;
    }
}