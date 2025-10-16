<?php

declare(strict_types=1);

namespace Laler\Dumpers;

use Laler\Support\BrowserConsoleRecorder;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;

final class BrowserConsoleDumper implements DataDumperInterface
{
    private CliDumper $cliDumper;

    public function __construct(private BrowserConsoleRecorder $recorder)
    {
        $this->cliDumper = new CliDumper();
        $this->cliDumper->setColors(false);
    }

    public function dump(Data $data): void
    {
        $output = $this->cliDumper->dump($data, true);

        if ($output === null) {
            return;
        }

        $this->recorder->push($output);
    }
}
