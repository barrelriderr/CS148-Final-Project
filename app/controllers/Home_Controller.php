<?php

class Home_Controller extends Controller{

	public function index() {

		View::make('index');
	}

	public function error() {

		View::make('404');
	}
}