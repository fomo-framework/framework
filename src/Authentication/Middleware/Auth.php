<?php

namespace Tower\Authentication\Middleware;

use Tower\DB;
use Tower\Middleware\Contract;
use Tower\Request;
use Tower\Response;
use Tower\Authentication\Auth as AuthParent;
use App\Exceptions\AuthenticationException;

class Auth implements Contract
{
    public function handle(Request $request): bool|Response
    {
        if ($request->bearerToken()){
            $user = DB::table('access_tokens')
                ->where('token' , hash('sha256' , $request->bearerToken()))
                ->join('users' , 'access_tokens.user_id' , '=' , 'users.id')->select('users.*')->first();

            if ($user){
                AuthParent::setInstance($user);
                return true;
            }
        }

        return $this->unauthorized();
    }

    protected function unauthorized(): Response
    {
        try{
            throw new AuthenticationException();
        }catch(AuthenticationException $e){
            return $e->handle();
        }
    }
}