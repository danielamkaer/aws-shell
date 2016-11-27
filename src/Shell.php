<?php


class Shell
{

    protected $pwd = '/ec2';

    protected $prompt = '[%{pwd}]> ';

    protected $commands = [];

    protected $mounts = [];

    public function __construct(array $config) {
        $this->addMount(new RootMount($this));

        foreach ($config['mounts'] as $mount) {
            $this->addMount(new $mount($this));
        }

        foreach ($config['commands'] as $command) {
            $this->addCommand(new $command($this));
        }
    }

    protected function getPrompt()
    {
        return str_replace([
            '%{pwd}',
        ],
        [
            $this->pwd,
        ],
        $this->prompt);
    }

    public function getCommands() {
        return $this->commands;
    }

    public function getMounts() {
        return $this->mounts;
    }

    public function getPwd()
    {
        return $this->pwd;
    }

    public function setPwd($pwd)
    {
        $this->pwd = $pwd;
    }

    protected function resolveCompletion($line)
    {
        $lineBuffer = readline_info()['line_buffer'];
        if ($line == $lineBuffer) {
            $matches = [];
            foreach ($this->commands as $cmd) {
                $matches[] = $cmd->getName();
            }
            return $matches;
        }
        return [];
//        list($command) = explode(" ", $lineBuffer, 2);
//
//        $matches = [];
//
//        foreach ($this->commands as $cmd) {
//            if (strncmp($cmd->getName(), $command, strlen($command)) == 0) {
//                $matches[] = $command;
//            }
//        }
//        return $matches;
    }

    public function loop()
    {
        $self = $this;
        readline_completion_function(function ($line) use ($self) {
            return $this->resolveCompletion($line);
        });

        while (true) {

            $line = readline($this->getPrompt());

            readline_add_history($line);

            $this->interpret($line);

        }
    }

    public function getCurrentMount()
    {
        $pwd = $this->pwd;
        while (true) {
            foreach ($this->mounts as $mount) {
                if ($mount->getMountPoint() == $pwd) {
                    return $mount;
                }
            }
            $pwd = substr($pwd, 0, strrpos($pwd, '/'));
            if ($pwd == '') {
                $pwd = '/';
            }
        }
        return null;
    }

    protected function interpret($line)
    {
        $pieces = explode(" ",$line, 2);
        $cmd = $pieces[0];

        foreach ($this->commands as $command) {
            if ($command->getName() == $cmd) {
                $command->executeCommand(explode(" ", $pieces[1] ?? ""));
                break;
            }
        }
    }

    public function addCommand(CommandInterface $cmd)
    {
        array_push($this->commands, $cmd);
    }

    public function addMount(MountInterface $mount)
    {
        array_push($this->mounts, $mount);
    }

}
