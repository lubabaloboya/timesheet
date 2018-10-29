<?php

interface interface_model {
    
    public function setUp();
    public function getRows();
    public function createForm(request $request);
    public function create(request $request);
    public function read(request $request);
    public function update(request $request);
    public function delete(request $request);
}
