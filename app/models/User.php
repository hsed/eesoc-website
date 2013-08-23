<?php

use Illuminate\Auth\UserInterface;

class User extends Eloquent implements UserInterface {

	public static $FILTER_TO_FUNCTION_MAP = array(
		'admins'      => 'admin',
		'non-admins'  => 'nonAdmin',
		'members'     => 'member',
		'non-members' => 'nonMember',
	);

	private $imperialCollegeUser;

	public function scopeAdmin($query)
	{
		return $query->where('is_admin', '=', true);
	}

	public function scopeNonAdmin($query)
	{
		return $query->where('is_admin', '=', false);
	}

	public function scopeMember($query)
	{
		return $query->where('is_member', '=', true);
	}

	public function scopeNonMember($query)
	{
		return $query->where('is_member', '=', false);
	}

	public function scopeAlphabetically($query)
	{
		return $this->scopeAlphabeticallyUsing($query, 'username');
	}

	public function scopeAlphabeticallyUsing($query, $column)
	{
		return $query->orderBy($column);
	}

	public function scopeSearching($query, $searching)
	{
		return $query
			->where('username', 'like', "%{$searching}%")
			->orWhere('email', 'like', "%{$searching}%")
			->orWhere('name', 'like', "%{$searching}%");
	}

	public function scopeAdminsFirst($query)
	{
		return $query->orderBy('is_admin', 'DESC');
	}

	public static function findOrCreateWithLDAP($username)
	{
		$username = strtolower($username);

		$imperialCollegeUser = new ImperialCollegeUser($username);
		if ( ! $imperialCollegeUser->exist()) {
			return null;
		}

		// Find or create new User
		$user = static::where('username', '=', $username)->first();
		if ( ! $user) {
			$user = new static;
		}

		$user->setImperialCollegeUser($imperialCollegeUser);
		$user->synchronizeWithLDAP();

		return $user;
	}

	public static function resetMemberships()
	{
		$user = new static;
		return $user->update(array('is_member' => false));
	}

	public static function statistics()
	{
		return array(
			'everybody_count'   => static::count(),
			'admins_count'      => static::admin()->count(),
			'non_admins_count'  => static::nonAdmin()->count(),
			'members_count'     => static::member()->count(),
			'non_members_count' => static::nonMember()->count(),
		);
	}

	public function getAuthIdentifier()
	{
		return $this->username;
	}

	public function getAuthPassword()
	{
		return null;
	}

	public function setImperialCollegeUser($imperialCollegeUser)
	{
		$this->imperialCollegeUser = $imperialCollegeUser;
	}

	public function getImperialCollegeUser()
	{
		if ( ! isset($this->imperialCollegeUser)) {
			$this->imperialCollegeUser = new ImperialCollegeUser($this->username);
		}

		return $this->imperialCollegeUser;
	}

	public function checkPassword($password)
	{
		return $this->getImperialCollegeUser()->checkPassword($password);
	}

	public function synchronizeWithLDAP()
	{
		$this->username = $this->getImperialCollegeUser()->username;
		$this->name     = $this->getImperialCollegeUser()->name;
		$this->email    = $this->getImperialCollegeUser()->email;
		$this->extras   = implode("\n", $this->getImperialCollegeUser()->info);
		return $this->save();
	}

	public function isAdmin()
	{
		return (bool) $this->is_admin;
	}

	public function isMember()
	{
		return (bool) $this->is_member;
	}

	public function recordSignIn()
	{
		$this->last_sign_in_at = new DateTime;

		// First time signing in
		if ( ! $this->first_sign_in_at) {
			$this->first_sign_in_at = $this->last_sign_in_at;
		}

		return $this->save();
	}

}