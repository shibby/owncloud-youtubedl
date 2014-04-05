<?php

$this->create('youtubedl_index', '/')->action(
    function($params){
        require __DIR__ . '/../index.php';
    }
);

