<?php

namespace CodeProject\OAuth;

use Illuminate\Support\Facades\Auth;

/**
 * Class to authenticate users via OAuth 2
 *
 * @author thiago
 */
class Verifier
{
  public function verify($username, $password)
  {
      $credentials = [
        'email'    => $username,
        'password' => $password,
      ];

      if (Auth::once($credentials)) {
          return Auth::user()->id;
      }

      return false;
  }
}
