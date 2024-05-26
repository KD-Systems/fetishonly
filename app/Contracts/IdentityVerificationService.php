<?php

namespace App\Contracts;

use App\IdentityVerification;
use App\User;

interface IdentityVerificationService {
    public function verify(User $user);

    public function getStatus(IdentityVerification $verification);

    public function uploadFiles(IdentityVerification $verification);
}
