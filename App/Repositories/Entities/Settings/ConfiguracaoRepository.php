<?php

namespace App\Repositories\Entities\Settings;

use App\Config\Database;
use App\Config\SingletonInstance;
use App\Models\Settings\Configuracao;
use App\Repositories\Contracts\Settings\IConfiguracaoRepository;
use App\Repositories\Traits\FindTrait;
use App\Utils\LoggerHelper;

class ConfiguracaoRepository extends SingletonInstance implements IConfiguracaoRepository
{ 
    private const CLASS_NAME = Configuracao::class;
    private const TABLE = 'configuracao';

    use FindTrait;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
        $this->model = new Configuracao();
    }
    
    public function getSettings()
    {
        $stmt = $this->conn->query("SELECT * FROM configuracao LIMIT 1");
        
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::CLASS_NAME);
        $register = $stmt->fetch();  
        if (is_null($register)) {
            return null;
        }

        return $register;    
    }

    public function create(array $params)
    {
        $setting = $this->model->create(
            $params
        );

        try {
            $stmt = $this->conn
            ->prepare(
                "INSERT INTO " . self::TABLE . " 
                  set 
                    company_name = :company_name,
                    email = :email,
                    phone = :phone,
                    cnpj = :cnpj,
                    address = :address,
                    checkin = :checkin,
                    checkout = :checkout,
                    percentage_service_fee = :percentage_service_fee,
                    cleaning_rate = :cleaning_rate,
                    display_values_on_dashboard = :display_values_on_dashboard,
                    allow_booking_online = :allow_booking_online,
                    currency = :currency,
                    advance_booking_days = :advance_booking_days,
                    cancellation_policies = :cancellation_policies
            ");

            $create = $stmt->execute([
                ':company_name'  => $setting->company_name,
                ':email'  => $setting->email,
                ':phone'  => $setting->phone,
                ':cnpj'  => $setting->cnpj,
                ':address'  => $setting->address,
                ':checkin'  => $setting->checkin,
                ':checkout'  => $setting->checkout,
                ':percentage_service_fee'  => $setting->percentage_service_fee,
                ':cleaning_rate'  => $setting->cleaning_rate,
                ':display_values_on_dashboard'  => $setting->display_values_on_dashboard,
                ':allow_booking_online'  => $setting->allow_booking_online,
                ':currency'  => $setting->currency,
                ':advance_booking_days'  => $setting->advance_booking_days,
                ':cancellation_policies'  => $setting->cancellation_policies
            ]);
    
            if (is_null($create)) {
                return null;
            }
    
            return $this->findById($this->conn->lastInsertId());
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }

    public function update(array $params, int $id)
    {
        $existing = $this->findById($id);
        if (!$existing) {
            return null; 
        }

          $setting = $this->model->update(
            $params,
            $existing
        );

        try {
            $stmt = $this->conn
            ->prepare(
                "UPDATE " . self::TABLE . " 
                  set 
                    company_name = :company_name,
                    email = :email,
                    phone = :phone,
                    cnpj = :cnpj,
                    address = :address,
                    checkin = :checkin,
                    checkout = :checkout,
                    percentage_service_fee = :porcentage_service_fee,
                    cleaning_rate = :cleaning_rate,
                    display_values_on_dashboard = :display_values_on_dashboard,
                    allow_booking_online = :allow_booking_online,
                    currency = :currency,
                    advance_booking_days = :advance_booking_days,
                    cancellation_policies = :cancellation_policies
                WHERE 
                    id = :id
            ");
            
            $create = $stmt->execute([
                ':company_name'  => $setting->company_name,
                ':email'  => $setting->email,
                ':phone'  => $setting->phone,
                ':cnpj'  => $setting->cnpj,
                ':address'  => $setting->address,
                ':checkin'  => $setting->checkin,
                ':checkout'  => $setting->checkout,
                ':porcentage_service_fee'  => $setting->percentage_service_fee,
                ':cleaning_rate'  => $setting->cleaning_rate,
                ':display_values_on_dashboard'  => $setting->display_values_on_dashboard,
                ':allow_booking_online'  => $setting->allow_booking_online,
                ':currency'  => $setting->currency,
                ':advance_booking_days'  => $setting->advance_booking_days,
                ':cancellation_policies'  => $setting->cancellation_policies,
                ':id'  => $setting->id
            ]);
    
            if (is_null($create)) {
                return null;
            }
    
            return $this->findById($id);
        } catch (\Throwable $th) {
            LoggerHelper::logInfo($th->getMessage());
            return null;
        }
    }

    
}