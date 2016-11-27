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
        $children = $this->shell->getCurrentMount()->getChildren($this->shell->getPwd());
        foreach ($children as $child) {
            if ($child == $args[0]) {
                $this->shell->setPwd($this->shell->getPwd() . '/' . $child);
            }
        }
    }

}
