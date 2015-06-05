<?php

class HomeModule
{
	public function index()
	{
		Template::display('index.tpl');
	}

	public function test()
	{
		echo 'Routes are working!';
	}
}