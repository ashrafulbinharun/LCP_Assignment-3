<?php

define("USERS", __DIR__ . "/data/users.txt");
define("FEEDBACKS", __DIR__ . "/data/feedbacks.txt");

// Function to add a user
function addUser($name, $email, $password)
{
    $userId = uniqid();
    $user = [
        'user_id' => $userId,
        'name' => $name,
        'email' => $email,
        'password' => $password,
    ];

    $userData = serialize($user);
    file_put_contents(USERS, $userData . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Function to check if a user already exists by email
function userExists($email)
{
    $users = file(USERS, FILE_IGNORE_NEW_LINES);
    foreach ($users as $user) {
        $userData = unserialize($user);
        if ($userData['email'] === $email) {
            return true;
        }
    }
    return false;
}

// Function to authenticate a user
function authUser($email, $password)
{
    $users = file(USERS, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($users as $user) {
        $userData = unserialize($user);
        if ($userData['email'] === $email && password_verify($password, $userData['password'])) {
            return $userData;
        }
    }
    return false;
}

// Function to generate a session flush message
function flash($key, $message = null)
{
    // If a message is passed in, set it
    if ($message) {
        $_SESSION['flash'][$key] = $message;
    }
    // If no message is passed in, get and delete the message
    else if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
}

// Function to add feedback
function addFeedback($userId, $message)
{
    $feedback = [
        'user_id' => $userId,
        'message' => $message,
        'created_at' => date('Y-m-d H:i:s'),
    ];

    $feedbackData = serialize($feedback);
    file_put_contents(FEEDBACKS, $feedbackData . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Function to get all feedbacks
function getFeedbacks($userId)
{
    $feedbacks = [];

    $allFeedbacks = file(FEEDBACKS, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($allFeedbacks as $feedback) {
        $feedbackData = unserialize($feedback);
        if ($feedbackData['user_id'] === $userId) {
            $feedbacks[] = $feedbackData;
        }
    }

    return $feedbacks;
}
