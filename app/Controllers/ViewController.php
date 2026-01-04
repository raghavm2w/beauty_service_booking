<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;


class ViewController extends Controller
{
    public function home()
    {
        return $this->view('home');
    }
    public function showRegister()
    {
        return view("register");
    }
    public function showLogin()
    {
        return view("login");
    }
    public function about()
    {
        return view("about");
    }
    public function contact()
    {
        return view("contact");
    }

    public function adminServices()
    {
        return view("admin.services");
    }
    public function adminDash()
    {
        $bookingModel = new Booking();
        $serviceModel = new Service();
        $userModel = new User();

        $providerId = $_REQUEST['auth_user']['id'];
        $user = $userModel->findById($providerId);
        $stats = $bookingModel->getDashboardStats($providerId);
        $totalServices = $serviceModel->countActiveServices($providerId);
        $todayBookings = $bookingModel->getTodayBookings($providerId);

        return view("admin.dash", [
            'user' => $user,
            'stats' => $stats,
            'totalServices' => $totalServices,
            'todayBookings' => $todayBookings
        ]);
    }
    public function adminAvail()
    {
        return view("admin.avail");
    }
    public function userServices()
    {
        return view("user-services");
    }
    public function payments()
    {
        return view("payments");
    }
    public function userBookings()
    {
        return view("user-bookings");
    }
    public function adminBookings()
    {
        return view("admin.bookings");
    }
    public function editProfile()
    {
        return view("user-edit-profile");
    }
    public function adminProfile()
    {
        return view("admin.admin-edit-profile");
    }
}