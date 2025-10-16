<?php

declare(strict_types=1);

namespace Laler\Dumpers;

use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

final class TauriDumper implements DataDumperInterface
{
    private CliDumper $cliDumper;
    private string $tauriEndpoint;

    public function __construct(string $tauriEndpoint = 'http://localhost:3000')
    {
        $this->cliDumper = new CliDumper();
        $this->cliDumper->setColors(false);
        $this->tauriEndpoint = $tauriEndpoint;
    }

    public function dump(Data $data): void
    {
        $output = $this->cliDumper->dump($data, true);

        if ($output === null) {
            return;
        }

        $this->sendToTauri($output, $data);
    }

    private function sendToTauri(string $output, Data $data): void
    {
        $payload = [
            'type' => 'dump',
            'timestamp' => now()->toISOString(),
            'output' => $output,
            'file' => $this->extractFileInfo($data),
            'line' => $this->extractLineInfo($data),
        ];

        // Use cURL to send data to Tauri app
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->tauriEndpoint . '/api/dump');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

        // Execute and ignore response - fire and forget
        curl_exec($ch);
        curl_close($ch);
    }

    private function extractFileInfo(Data $data): ?string
    {
        $context = $data->getContext();
        return $context['file'] ?? null;
    }

    private function extractLineInfo(Data $data): ?int
    {
        $context = $data->getContext();
        return isset($context['line']) ? (int)$context['line'] : null;
    }
}
