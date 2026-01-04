<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\User;

class ContactController extends Controller
{
    private User $user;
    public function __construct()
    {
        $this->user = new User();
    }
    public function submit()
    {
        try{
        $data = json_decode(file_get_contents('php://input'), true);

        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $message = $data['message'] ?? '';

       
if (empty($name)) {
    error(400, 'Name is required');
}

if (empty($email)) {
    error(400, 'Email is required');
}

if (empty($message)) {
    error(400, 'Message is required');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error(400, 'Invalid email format');
}
 if (strlen($email) > 100) {
                error(400, "Email is too long.");
    }
    if (strlen($name) > 100) {
                error(400, "Name is too long.");
     }
    if (strlen($message) > 1000) {
                error(400, "Message is too long.");
    }
$name = htmlspecialchars(strip_tags($name), ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars(strip_tags($email), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(strip_tags($message), ENT_QUOTES, 'UTF-8');

$res = $this->user->createContact($name, $email, $message);

if ($res) {
    success(200, 'Message sent successfully!');
} else {
    error(500, 'Failed to send message');
}
        }catch(\Exception $e){
            error_log("Contact error: " . $e->getMessage());
            error(500, "Internal Server error: " . $e->getMessage());
        }
    }
}
