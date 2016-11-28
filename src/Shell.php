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

        $children = $this->getChildren();
        $children = array_filter($children, function($child) use($line) {
            return starts_with($child, $line) || empty($line);
        });
        return $children;
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

    public function getMountFor($path) {
        while (true) {
            $mount = array_first($this->mounts, function(MountInterface $mount) use ($path) {
                return $mount->getMountPoint() == $path;
            });

            if ($mount) {
                return $mount;
            }

            $path = substr($path, 0, strrpos($path, '/'));
            if ($path == '') {
                $path = '/';
            }
        }
        return null;
    }

    public function getChildren($pwd = null) {
        if ($pwd == null) {
            $pwd = $this->pwd;
        }

        $mount = $this->getMountFor($pwd);
        return $mount->getChildren($pwd);
    }

    public function getChild($path) {
        $dir = dirname($path);
        $name = basename($path);
        return array_first($this->getChildren($dir), function($child) use ($name) {
            return $child == $name;
        });
    }

    public function getCurrentMount()
    {
        return $this->getMountFor($this->pwd);
    }

    protected function interpret($line)
    {
        if (empty(trim($line))) {
            return;
        }

        $pieces = explode(" ",$line, 2);
        $cmdName = $pieces[0];

        $command = array_first($this->commands, function(CommandInterface $command) use ($cmdName) {
            return $command->getName() == $cmdName;
        });

        if ($command) {
            $args = str_getcsv($pieces[1] ?? "", ' ');
            if (count($args) == 1 && $args[0] == NULL) {
                $args = [];
            }

            $command->executeCommand($args);
        } else {
            printf("Unknown command %s.\n", $cmdName);
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
