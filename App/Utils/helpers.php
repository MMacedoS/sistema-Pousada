<?php

use App\Utils\LoggerHelper;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}  

if (!function_exists('isPermissionChecked')) {
    function isPermissionChecked($permissionId, $userPermissoes) {
        foreach ($userPermissoes as $userPermissao) {
            if ($userPermissao['permissao_id'] === $permissionId) {
                return true;
           }
        }
        return false;
    }
}

if (!function_exists('reservaFilteredById')) {
    function reservaFilteredById($reservaId, $reservas) {
        foreach ($reservas as $reserva) {
            if ($reserva->id === $reservaId) {
                return $reserva;
           }
        }
        return null;
    }
}

if (!function_exists('hasPermission')) {    
    function hasPermission($permissio_name) {
        $my_permissions = $_SESSION['my_permissions'] ?? [];
        foreach ($my_permissions as $permission) {
            if ($permission->name === $permissio_name) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('getCustomers')) {    
    function getCustomers($data) {
        if (is_array($data)) {
            $names = '';
            foreach ($data as $customer) {
                $names.= $customer->name; 
                if(count($data) > 1) {
                    $names .= ' | ';
                }
            }
            return $names;
        }
        return "Não identificado";
    }
}

if (!function_exists('brDate')) {    
    function brDate($date) {
        if (!is_null($date)) {
            $date = implode('/', array_reverse(explode('-', $date)));
            return $date;
        }
        return "Não identificado";
    }
}

if (!function_exists('brCurrency')) {    
    function brCurrency($value) {
        if (!is_null($value) && is_numeric($value)) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        }
        return null;
    }
}



if (!function_exists('publicPath')) {
    function publicPath($file, $path)
    {
        if (empty($file['name']) || empty($file['tmp_name'])) {
            return null;
        }

        $path_full = rtrim($_SERVER['DOCUMENT_ROOT'] . '/Public' . $path, '/') . '/';

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $name = strtolower(pathinfo($file['name'], PATHINFO_FILENAME));

        $new_name = uniqid() . "_" . time() . "." . $ext;

        if (!is_dir($path_full)) {
            if (!mkdir($path_full, 0755, true)) {
                return null;
            }
        }

        $destination = $path_full . $new_name;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return [
                'name' => $name,
                'new_name' => $new_name,
                'ext' => $ext,
                'path' => $path . $new_name
            ];
        } 
        
        return null;
    }
}

if (!function_exists('dd')) {
    function uploadFile($uploadDir, $file) {
        try {
            // Definir o tamanho máximo em bytes (30 MB)
            $maxFileSize = 30 * 1024 * 1024;
    
            // Verifica se o upload ocorreu sem erros
            if ($file['error'] === UPLOAD_ERR_OK) {
                // Verifica o tipo MIME do arquivo
                $fileType = mime_content_type($file['tmp_name']);
                if ($fileType === 'application/pdf') {
                    // Verifica o tamanho do arquivo
                    if ($file['size'] <= $maxFileSize) {
                        // Gera um nome único para o arquivo
                        $fileName = uniqid() . '_' . basename($file['name']);
                        $uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/Public' . $uploadDir . $fileName;
    
                        // Move o arquivo para o diretório de upload
                        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                            return [
                                'original_name' => $fileName, // Nome do arquivo salvo
                                'ext_archive' => 'pdf', // Extensão fixa para PDF
                                'path' =>  $uploadDir . '/' . $fileName // Caminho completo do arquivo
                            ];
                        }
                    }
                }
            }
    
            return [];
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return [];
        }
    }    
}

if (!function_exists('dd')) {    
    function dd($value) {
        echo "<pre>" . var_dump($value) . "</pre>";
        die;
    }
}

if (!function_exists('alertaBootstrap')) {
    function alertaBootstrap()
    {
        $html = "<!-- Row end -->\n";

        if (!isset($_GET['error'])) {
            return '';
        }

        $error = $_GET['error'];

        $mensagens = [
            '401' => 'Acesso não autorizado. Verifique suas credenciais.',
            '422' => 'Erro de validação. Verifique os dados informados.',
            '500' => 'Erro interno no servidor. Tente novamente mais tarde.'
        ];

        if (array_key_exists($error, $mensagens)) {
            $html .= <<<HTML
            <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
            <b>Erro {$error}:</b> {$mensagens[$error]}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            HTML;
        }

        return $html;
    }    
}

if(!function_exists('checkboxToInt')) {
    function checkboxToInt($value = null) {
        if(is_null($value)) {
            return null;
        }

        // Se o valor existe e não está vazio
        if (isset($value) && !empty($value)) {
            // Converte diferentes tipos de valores "truthy" para 1
            if ($value === 'on' || $value === '1' || $value === 1 || $value === true) {
                return 1;
            }
        }
        return 0;
    }
}
