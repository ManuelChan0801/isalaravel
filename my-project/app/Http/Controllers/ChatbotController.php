<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ChatbotController extends Controller
{
    protected $httpClient;

    public function __construct()
    {
        $apiKey = env('YOUR_OPENAI_API_KEY');

        if (empty($apiKey)) {
            throw new \Exception('No se proporcionó una clave de API para OpenAI.');
        }

        $this->httpClient = new Client([
            'base_uri' => 'https://api.openai.com/v1/chat/completions',
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function askToChatGpt(Request $request)
    {
        try {
            $userQuestion = $request->input('question', 'what is laravel');
            $chatbotResponse = null;

            if (!$userQuestion) {
                return view('chatbot', ['error' => 'La pregunta no puede estar vacía']);
            }

            $response = $this->httpClient->post('', [
                'json' => [
                    'model' => 'gpt-3.5-turbo-0613',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are'],
                        ['role' => 'user', 'content' => $userQuestion],
                    ],
                ],
            ]);

            $chatbotResponse = json_decode($response->getBody(), true)['choices'][0]['message']['content'];

            $coordinates = $this->getCoordinatesFromGeocodingAPI($chatbotResponse);

            return view('chatbot', compact('chatbotResponse', 'userQuestion', 'coordinates'));

        } catch (ClientException $exception) {
            $errorMessage = $exception->getResponse()->getBody()->getContents();
            return view('chatbot', ['error' => $errorMessage]);
        }
    }

    protected function getCoordinatesFromGeocodingAPI($placeName)
    {

    $apiKey = env('YOUR_GOOGLE_MAPS_API_KEY'); // Asegúrate de tener esta clave en tu archivo .env

    if (empty($apiKey)) {
        throw new \Exception('No se proporcionó una clave de API para Google Maps.');
    }

    $client = new Client();
    $response = $client->get("https://maps.googleapis.com/maps/api/geocode/json", [
        'query' => [
            'address' => $placeName,
            'key' => $apiKey
        ]
    ]);

    $data = json_decode($response->getBody(), true);

    if (empty($data['results'])) {
        return null;
    }

    return [
        'lat' => $data['results'][0]['geometry']['location']['lat'],
        'lng' => $data['results'][0]['geometry']['location']['lng']
    ];
    }

}
