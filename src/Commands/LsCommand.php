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
        if (count($args) > 0) {
            foreach ($args as $dir) {
                printf("%s/\n", $dir);
                $children = $this->shell->getChildren($this->shell->getPwd() . '/' . $dir);
                foreach ($children as $child) {
                    printf("%s\n", $child);
                }
            }
        } else {
            $children = $this->shell->getChildren();
            foreach ($children as $child) {
                printf("%s\n", $child);
            }
        }
    }

}
