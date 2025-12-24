<?php
namespace App\Controllers;
use App\Core\Controller;

class ViewController extends Controller {
    public function home() {
        return $this->view('home');
    }
     public function showRegister(){
        return view("register");
    }
    public function showLogin(){
        return view("login");
    }
    public function admin(){
        return view("admin.main");
    }
    public function adminServices(){
        return view("admin.services");
    }
     public function adminDash(){
        return view("admin.dash");
    }
    public function adminAvail(){
        return view("admin.avail");
    }
}