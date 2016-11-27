<?php

class LsCommand implements CommandInterface
{

    public function __construct(Shell $shell) {
        $this->shell = $shell;
    }

    public function getName()
    {
        return 'ls';
    }

    public function getDescription()
    {
        return 'List stuff in current directory.';
    }

    public function executeCommand(array $args)
    {
        $children = $this->shell->getCurrentMount()->getChildren($this->shell->getPwd());
        foreach ($children as $child) {
            printf("%s\n", $child);
        }
    }

}
