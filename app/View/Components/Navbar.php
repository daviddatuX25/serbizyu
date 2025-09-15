<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Auth;

class Navbar extends Component
{
    public array $authProfileData = [];

    public function __construct()
    {
        /** @var User|null $user */
        $user = Auth::user();

        $firstname = $user?->firstname;
        $email     = $user?->email;
        $img_path = $user?->img_path;

        $this->authProfileData = [
            'email'     => $email,
            'greeting'  => $this->makeGreeting($firstname),
            'img_path' => $img_path
        ];
    }

    private function makeGreeting(?string $user_name = null): string
    {
        $startPhrases = collect(['Hi', 'Hello', 'Welcome']);
        $phrase = $startPhrases->random();

        return $phrase . ', ' . ($user_name ?: 'Guest');
    }

    public function render(): View
    {
        return view('layouts.navbar');
    }
}
