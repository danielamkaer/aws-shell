<?php

class RootMount implements MountInterface
{

    public function __construct(Shell $shell) {
        $this->shell = $shell;
    }

    public function getMountPoint() {
        return '/';
    }

    public function getChildren($path) {
        $nodes = [];
        foreach ($this->shell->getMounts() as $mount) {
            if ($mount == $this) {
                continue;
            }

            $nodes[] = substr($mount->getMountPoint(), 1);

        }

        return $nodes;
    }

}
