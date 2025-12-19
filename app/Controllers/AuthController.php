<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller{
   private User $user;

    public function __construct()
    {
        $this->user = new User();
    }
    public function showRegister(){
        return view("register");
    }
    public function showLogin(){
        return view("login");
    }
     
    public function register()
    {
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password'])) {
            error(400,"all fields are required");
        }

        if ($this->user->findByEmail($_POST['email'])) {
            error(400,"email already exists");

        }

        $userId = $this->user->register($_POST);

        $_SESSION['user'] = [
            'id'   => $userId,
            'name' => $_POST['name'],
            'role' => 'customer'
        ];

        redirect('/dashboard');
    }
}


