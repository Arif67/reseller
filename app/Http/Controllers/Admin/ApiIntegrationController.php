<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use App\Models\SmsGateway;
use App\Models\Courierapi;
use App\Models\FraudCheckerConfig;
use Toastr;
use File;
use Str;
use Image;
use DB;
use Illuminate\Support\Facades\Http;

class ApiIntegrationController extends Controller
{
    
     
    public function pay_manage ()
    {
        $bkash = PaymentGateway::firstOrCreate(
            ['type' => 'bkash'],
            ['status' => 0]
        );
        $shurjopay = PaymentGateway::firstOrCreate(
            ['type' => 'shurjopay'],
            ['status' => 0]
        );
        return view('backEnd.apiintegration.pay_manage',compact('bkash','shurjopay'));
    }
    
    public function pay_update(Request $request)
    {
      
        $update_data = PaymentGateway::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status?1:0;
        $update_data->update($input);
        
        Toastr::success('Success','Data update successfully');
        return redirect()->back();
    }
    
    public function sms_manage ()
    {  
        $sms = SmsGateway::first();
        return view('backEnd.apiintegration.sms_manage',compact('sms'));
    }
    
    public function sms_update(Request $request)
    {
      
        $update_data = SmsGateway::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status?1:0;
        $input['order'] = $request->order?1:0;
        $input['forget_pass'] = $request->forget_pass?1:0;
        $input['password_g'] = $request->password_g?1:0;
        $update_data->update($input);
        
        Toastr::success('Success','Data update successfully');
        return redirect()->back();
    }
    
    public function courier_manage ()
    {
        $courierDefinitions = [
            'steadfast' => [
                'title' => 'Steadfast Courier',
                'fields' => ['api_key', 'secret_key', 'url'],
                'labels' => [
                    'api_key' => 'API Key',
                    'secret_key' => 'Secret Key',
                    'url' => 'Base URL',
                ],
            ],
            'pathao' => [
                'title' => 'Pathao Courier',
                'fields' => ['client_id', 'client_secret', 'username', 'password', 'grant_type', 'url', 'token'],
                'labels' => [
                    'client_id' => 'Client ID',
                    'client_secret' => 'Client Secret',
                    'username' => 'Username',
                    'password' => 'Password',
                    'grant_type' => 'Grant Type',
                    'url' => 'Base URL',
                    'token' => 'Access Token',
                ],
            ],
        ];

        foreach (array_keys($courierDefinitions) as $type) {
            Courierapi::firstOrCreate(['type' => $type]);
        }

        $fraudCheckerConfig = FraudCheckerConfig::firstOrCreate([], [
            'name' => 'BD Courier Fraud Checker',
            'status' => 1,
        ]);

        $couriers = Courierapi::whereIn('type', array_keys($courierDefinitions))
            ->orderBy('type')
            ->get()
            ->map(function ($courier) use ($courierDefinitions) {
            $definition = $courierDefinitions[$courier->type] ?? [
                'title' => Str::headline($courier->type),
                'fields' => ['api_key', 'secret_key', 'client_id', 'client_secret', 'username', 'password', 'grant_type', 'url', 'token'],
                'labels' => [],
            ];

            $courier->section_title = $definition['title'];
            $courier->form_fields = $definition['fields'];
            $courier->field_labels = $definition['labels'] ?? [];

            return $courier;
        });

        return view('backEnd.apiintegration.courier_manage', compact('couriers', 'fraudCheckerConfig'));
    }
    
    public function courier_update (Request $request)
    {
        if ($request->input('config_group') === 'fraud_checker') {
            $fraudCheckerConfig = FraudCheckerConfig::findOrFail($request->id);
            $fraudCheckerConfig->update([
                'name' => $request->input('name', $fraudCheckerConfig->name ?: 'BD Courier Fraud Checker'),
                'url' => $request->input('url'),
                'api_key' => $request->input('api_key'),
                'status' => $request->boolean('status') ? 1 : 0,
            ]);

            Toastr::success('Success','Data update successfully');
            return redirect()->back();
        }

        $update_data = Courierapi::findOrFail($request->id);
        $input = $request->only(['api_key', 'secret_key', 'client_id', 'client_secret', 'username', 'password', 'grant_type', 'url']);
        $input['status'] = $request->status ? 1 : 0;

        if ($update_data->type === 'pathao') {
            $pathaoCredentialFields = ['client_id', 'client_secret', 'username', 'password', 'grant_type'];

            foreach ($pathaoCredentialFields as $field) {
                if (($update_data->{$field} ?? null) !== ($input[$field] ?? null)) {
                    $input['token'] = null;
                    break;
                }
            }
        }

        $update_data->update($input);
        
        Toastr::success('Success','Data update successfully');
        return redirect()->back();
    }

    public function courier_test_connection(Request $request)
    {
        if ($request->input('config_group') === 'fraud_checker') {
            $fraudCheckerConfig = FraudCheckerConfig::findOrFail($request->id);
            $payload = array_merge($fraudCheckerConfig->toArray(), $request->only(['url', 'api_key', 'name']));

            $message = $this->testFraudCheckerConnection($payload);
            $isSuccess = ! str_starts_with($message, 'Failed:');

            if ($isSuccess) {
                Toastr::success($message);
            } else {
                Toastr::error($message);
            }

            return redirect()->back()->withInput()->with([
                'courier_test_type' => 'fraud_checker',
                'courier_test_status' => $isSuccess ? 'success' : 'failed',
                'courier_test_message' => $message,
            ]);
        }

        $courier = Courierapi::findOrFail($request->id);
        $payload = array_merge($courier->toArray(), $request->only([
            'api_key', 'secret_key', 'client_id', 'client_secret', 'username', 'password', 'grant_type', 'url', 'token'
        ]));

        if ($courier->type === 'steadfast') {
            $message = $this->testSteadfastConnection($payload);
            $isSuccess = ! str_starts_with($message, 'Failed:');
        } elseif ($courier->type === 'pathao') {
            $message = $this->testPathaoConnection($payload);
            $isSuccess = ! str_starts_with($message, 'Failed:');
        } else {
            $message = 'Failed: Test connection not configured for this courier type yet';
            $isSuccess = false;
        }

        if ($isSuccess) {
            Toastr::success($message);
        } else {
            Toastr::error($message);
        }

        return redirect()->back()->withInput()->with([
            'courier_test_type' => $courier->type,
            'courier_test_status' => $isSuccess ? 'success' : 'failed',
            'courier_test_message' => $message,
        ]);
    }

    public function pathao_regenerate_token()
    {
        $pathao = Courierapi::where('type', 'pathao')->first();

        if (! $pathao) {
            Toastr::error('Pathao courier config not found');
            return redirect()->back();
        }

        if (! $pathao->client_id || ! $pathao->client_secret || ! $pathao->username || ! $pathao->password) {
            Toastr::error('Pathao credential incomplete');
            return redirect()->back();
        }

        $pathao->token = null;
        $pathao->save();

        $token = $this->generatePathaoToken($pathao);

        if (! $token) {
            Toastr::error('Pathao access token regenerate failed');
            return redirect()->back();
        }

        Toastr::success('Pathao access token regenerated successfully');
        return redirect()->back();
    }

    private function testSteadfastConnection(array $payload): string
    {
        if (empty($payload['api_key']) || empty($payload['secret_key']) || empty($payload['url'])) {
            return 'Failed: Steadfast API key, secret key, and URL are required';
        }

        $normalizedUrl = rtrim((string) $payload['url'], '/');

        if (str_contains($normalizedUrl, '/create_order')) {
            $normalizedUrl = preg_replace('#/create_order(?:/bulk-order)?$#', '', $normalizedUrl) ?? $normalizedUrl;
        }

        if (! str_contains($normalizedUrl, '/api/v1')) {
            $normalizedUrl .= '/api/v1';
        }

        $endpoint = $normalizedUrl . '/create_order';

        $response = Http::withHeaders([
            'Api-Key' => trim((string) $payload['api_key']),
            'Secret-Key' => trim((string) $payload['secret_key']),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($endpoint, [
            'invoice' => 'test-connection-' . now()->timestamp,
            'recipient_name' => 'Test User',
            'recipient_phone' => '01700000000',
            'recipient_address' => 'Test Address',
            'cod_amount' => 0,
        ]);

        if ($response->status() === 401) {
            return 'Failed: ' . trim((string) $response->body());
        }

        if ($response->status() >= 500) {
            return 'Failed: Steadfast server error';
        }

        return 'Steadfast connection reachable. Response: ' . (trim((string) $response->body()) ?: 'OK');
    }

    private function testPathaoConnection(array $payload): string
    {
        if (empty($payload['client_id']) || empty($payload['client_secret']) || empty($payload['username']) || empty($payload['password'])) {
            return 'Failed: Pathao client credentials are incomplete';
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://api-hermes.pathao.com/aladdin/api/v1/issue-token', [
            'client_id' => $payload['client_id'],
            'client_secret' => $payload['client_secret'],
            'username' => $payload['username'],
            'password' => $payload['password'],
            'grant_type' => $payload['grant_type'] ?: 'password',
        ]);

        $json = $response->json();

        if (! empty($json['access_token'])) {
            return 'Pathao connection successful';
        }

        return 'Failed: ' . (is_array($json) ? ($json['message'] ?? json_encode($json)) : trim((string) $response->body()));
    }

    private function testFraudCheckerConnection(array $payload): string
    {
        if (empty($payload['url']) || empty($payload['api_key'])) {
            return 'Failed: Fraud checker API URL and API key are required';
        }

        $token = trim((string) $payload['api_key']);

        if (! str_starts_with(strtolower($token), 'bearer ')) {
            $token = 'Bearer ' . $token;
        }

        $response = Http::withHeaders([
            'Authorization' => $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(trim((string) $payload['url']), [
            'phone' => '01700000000',
        ]);

        if ($response->successful()) {
            return 'Fraud checker connection successful';
        }

        return 'Failed: ' . ($response->json('message') ?? trim((string) $response->body()) ?: 'Connection failed');
    }

    private function generatePathaoToken(Courierapi $pathao): ?string
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://api-hermes.pathao.com/aladdin/api/v1/issue-token', [
            'client_id' => $pathao->client_id,
            'client_secret' => $pathao->client_secret,
            'username' => $pathao->username,
            'password' => $pathao->password,
            'grant_type' => $pathao->grant_type ?: 'password',
        ])->json();

        if (! empty($response['access_token'])) {
            $pathao->token = $response['access_token'];
            $pathao->save();

            return $pathao->token;
        }

        \Log::error('Pathao token regeneration failed', is_array($response) ? $response : ['response' => $response]);

        return null;
    }
}
