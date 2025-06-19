<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'Admin';
    case STUDENT = 'Student';
    case STAFF = 'Staff_Member';
    case TECHNICIAN = 'Technician';
}