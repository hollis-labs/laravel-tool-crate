<?php

namespace HollisLabs\ToolCrate\Console;

use Illuminate\Console\Command;
use HollisLabs\ToolCrate\Support\Exec;

class DevJqCommand extends Command
{
    protected $signature = 'tool:jq {program} {--json=} {--file=} {--raw} {--slurp} {--cwd=}';
    protected $description = 'Run jq via the same implementation backing the MCP json_query tool.';

    public function handle(): int
    {
        $args = [];
        if ($this->option('raw')) $args[] = '--raw-output';
        if ($this->option('slurp')) $args[] = '--slurp';
        $args[] = (string) $this->argument('program');
        $stdin = null;
        if ($j = $this->option('json')) $stdin = (string) $j;
        if ($f = $this->option('file')) $args[] = (string) $f;
        $cwd = $this->option('cwd') ?: null;

        $res = Exec::run(array_merge(['jq'], $args), $stdin, 12.0, $cwd);
        if (!$res->ok) { $this->error(trim($res->stderr ?: $res->stdout)); return self::FAILURE; }
        $this->line($res->stdout);
        return self::SUCCESS;
    }
}
