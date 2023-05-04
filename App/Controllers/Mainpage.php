<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;

class Mainpage extends Authenticated
{
	protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }
    
	public function mainpageAction()
	{				
		$username = User::getUsername($this->user->id);
		
		View::renderTemplate('Mainpage/mainpage.html',
			[
				'user' => $this->user,
				'username' => $username				
			]);
	}
}