<?php

namespace Pkc\ZoomMeeting;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ZoomService
{
    protected string $baseUrl;
    protected string $authUrl;
    protected string $accountId;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('zoom.base_url', 'https://api.zoom.us/v2/');
        $this->authUrl = config('zoom.auth_url', 'https://zoom.us/oauth/token');
        $this->accountId = config('zoom.account_id');
        $this->clientId = config('zoom.client_id');
        $this->clientSecret = config('zoom.client_secret');
    }

    /**
     * Get Access Token using Server-to-Server OAuth
     */
    protected function getAccessToken(): string
    {
        try {
            $response = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post($this->authUrl, [
                    'grant_type' => 'account_credentials',
                    'account_id' => $this->accountId,
                ]);

            if ($response->failed()) {
                Log::error('Zoom Auth Failed', ['response' => $response->json()]);
                throw new Exception('Failed to authenticate with Zoom.');
            }

            return $response->json()['access_token'];
        } catch (Exception $e) {
            Log::error('Zoom Auth Exception', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create a Zoom Meeting
     */
    public function createMeeting(array $data): array
    {
        $accessToken = $this->getAccessToken();

        try {
            $response = Http::withToken($accessToken)
                ->post($this->baseUrl . 'users/me/meetings', [
                    'topic' => $data['topic'] ?? 'Medical Consultation',
                    'type' => 2, // Scheduled meeting
                    'start_time' => $data['start_time'], // ISO 8601 format
                    'duration' => $data['duration'] ?? 30,
                    'timezone' => config('app.timezone', 'UTC'),
                    'settings' => [
                        'host_video' => true,
                        'participant_video' => true,
                        'join_before_host' => false,
                        'mute_upon_entry' => true,
                        'waiting_room' => true,
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Zoom Meeting Creation Failed', ['response' => $response->json()]);
                throw new Exception('Failed to create Zoom meeting.');
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Zoom Meeting Creation Exception', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
