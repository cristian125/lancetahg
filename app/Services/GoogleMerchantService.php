<?php

namespace App\Services;

use Google_Client;
use Google_Service_ShoppingContent;
use Illuminate\Support\Facades\Log;

class GoogleMerchantService
{
    protected $service;

    public function __construct()
    {   $client_id = '109377232162453913824';
        $clientSecret = '9c21b301bb7cc9fdd874a680ceaa110b9604fb83';
        // Genera la ruta absoluta dentro de la carpeta storage
        $credentialsPath = storage_path('app/' . env('GOOGLE_APPLICATION_CREDENTIALS'));
    
        // Verifica si el archivo existe
        if (!file_exists($credentialsPath)) {
            Log::error('El archivo de credenciales de Google no existe', ['ruta' => $credentialsPath]);
            throw new \Exception('El archivo de credenciales de Google no existe. Verifica la configuración.');
        }
    
        try {
            $client = new \Google_Client();
            $client->setApplicationName('Laravel Google Merchant Integration');
            $client->setClientId($client_id);
            $client->setClientSecret($clientSecret);
            $client->setAuthConfig($credentialsPath);
            $client->addScope(\Google_Service_ShoppingContent::CONTENT);
            // dd($client);
            // Deshabilitar la verificación SSL (solo para pruebas locales)
            $client->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
        
            // Log para confirmar credenciales
            Log::info('Credenciales cargadas correctamente', ['credenciales' => $credentialsPath]);
        
            $this->service = new \Google_Service_ShoppingContent($client);
            // dd($this->service);
        } catch (\Exception $e) {
            Log::error('Error al inicializar el cliente de Google', ['error' => $e->getMessage()]);
            throw $e;
        }
        
    }
    
    

    public function insertProduct($productData)
    {
        $this->__construct();

        $merchantId = env('MERCHANT_ID');
        if (empty($merchantId)) {
            Log::error('El Merchant ID no está configurado');
            throw new \Exception('El Merchant ID no está configurado. Configúralo en el archivo .env.');
        }
    
        $product = new \Google_Service_ShoppingContent_Product($productData);
        // dd($this->service,$productData);
        // Log para depuración
        Log::info('Enviando producto a Google Merchant', ['merchantId' => $merchantId, 'productData' => $productData]);
    
        return $this->service->products->insert($merchantId, $product);
    }
}
