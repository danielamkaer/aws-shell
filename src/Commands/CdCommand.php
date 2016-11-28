<?php

class CdCommand implements CommandInterface
{

    public function __construct(Shell $shell) {
        $this->shell = $shell;
    }

    public function getName()
    {
        return 'cd';
    }

    public function getDescription()
    {
        return 'Change the current directory.';
    }

    public function executeCommand(array $args)
    {
        if (count($args) == 0) {
            $this->shell->setPwd('/');
            return;
        }

        $child = $this->shell->getChild($this->shell->getPwd() . '/' . $args[0]);
        if ($child) {
            $this->shell->setPwd($this->shell->getPwd() . '/' . $child);
            return;
        }

    }

}
