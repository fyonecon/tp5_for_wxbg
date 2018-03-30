<?php

namespace app\hout\controller;

class Index extends Base
{
    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        return redirect(url('hout/admin/personal'));
    }
}
