<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface {

	use UserTrait;

	protected $table = 'users';

	protected $hidden = array('password', 'remember_token');

    protected $fillable = ['pid', 'uid', 'active', 'reddit_username', 'access_token', 'refresh_token'];

    /**
     * Return only the parameters pertinent to HootSuite.
     * @return array
     */
    public function getHootsuiteParams()
    {
        return ['pid' => $this->pid, 'uid' => $this->uid, 'theme' => $this->theme];
    }

}
