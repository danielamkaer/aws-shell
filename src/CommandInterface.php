<?php

interface CommandInterface
{

    public function __construct(Shell $shell);

    public function getName();

    public function getDescription();

    public function executeCommand(array $args);

}
