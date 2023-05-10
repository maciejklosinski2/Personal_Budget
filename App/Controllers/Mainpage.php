<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Models\Balance;
use \App\Flash;

class Mainpage extends Authenticated
{
	protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }
    
	public function mainpageAction()
	{
		$currentMonthDate = Balance::getCurrentMonthDate();
		$lastMonthDate = Balance::getlastMonthDate();
		$currentMonthBalance = Balance::getBalance($currentMonthDate,$this->user->id);
		$lastMonthBalance = Balance::getBalance($lastMonthDate,$this->user->id);
		$username = User::getUsername($this->user->id);
		
		View::renderTemplate('Mainpage/mainpage.html',
			[
				'user' => $this->user,
				'username' => $username,				
				'currentMonthBalance' => $currentMonthBalance,
				'lastMonthBalance' => $lastMonthBalance
			]);
	}
}