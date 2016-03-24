<?php

class user
{
    public  $id,$login,$projects;
    public function __constructor($id,$login,$projects)
    {
        $this->$id= $id;
        $this->$login= $login;
        $this->$projects= $projects;
    }
}