<?php

namespace Routes;

use Nether\Atlantis;
use Nether\Avenue;

class Home
extends Atlantis\PublicWeb {

	#[Atlantis\Meta\RouteHandler('/index')]
	public function
	Index():
	void {

		($this->App->Surface)
		->Area('home/index', [ 'Herp'=> 'Derp' ]);

		return;
	}

	#[Avenue\Meta\ErrorHandler(Avenue\Response::CodeForbidden)]
	#[Atlantis\Meta\TrafficReportSkip]
	public function
	ErrorForbidden():
	void {

		($this->App->Surface)
		->Area('error/forbidden');

		return;
	}

	#[Avenue\Meta\ErrorHandler(Avenue\Response::CodeNotFound)]
	#[Atlantis\Meta\TrafficReportSkip]
	public function
	ErrorNotFound():
	void {

		($this->App->Surface)
		->Area('error/not-found');

		return;
	}

}