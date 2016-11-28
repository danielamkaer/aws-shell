<?php

class Ec2Mount implements MountInterface
{

    protected $reservations = null;

    public function __construct(Shell $shell) {
        $this->shell = $shell;

        $this->client = new Aws\Ec2\Ec2Client([
            'version' => '2016-09-15',
            'region' => 'eu-west-1',
        ]);
    }

    public function getMountPoint() {
        return '/ec2';
    }

    protected function getRootChildren() {
        return ['by-instance-id'];
    }

    protected function getReservations() {
        if (!$this->reservations) {
            $this->reservations = $this->client->describeInstances()['Reservations'];
        }
        return $this->reservations;
    }

    protected function getChildrenByInstanceId() {

        $instances = [];

        $reservations = $this->getReservations();

        foreach ($reservations as $i => $reservation) {
            foreach ($reservation['Instances'] as $j => $instance) {
                $instances[] = $instance['InstanceId'];
            }
        }


        return $instances;
    }


    public function getChildren($path) {
        $pwd = substr($path,strlen($this->getMountPoint()));

        if ($pwd == '') {
            return $this->getRootChildren();
        } else if ($pwd == '/by-instance-id') {
            return $this->getChildrenByInstanceId();
        }

        return [];

    }

}
