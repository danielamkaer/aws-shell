<?php

interface MountInterface
{

    public function __construct(Shell $shell);

    public function getMountPoint();

    public function getChildren($path);

}
