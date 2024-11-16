<?php
session_start();

// Available languages
const AVAILABLE_LANGUAGES = ['fr', 'en'];
const DEFAULT_LANGUAGE = 'en';

// Set language based on browser preference if not set in session
if (!isset($_SESSION['lang'])) {
    $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2);
    $_SESSION['lang'] = in_array($browser_lang, AVAILABLE_LANGUAGES) ? $browser_lang : DEFAULT_LANGUAGE;
}

// Allow language switching via GET parameter
if (isset($_GET['lang']) && in_array($_GET['lang'], AVAILABLE_LANGUAGES)) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Load language file
$translations = require_once "lang/{$_SESSION['lang']}.php";

function __($key, ...$args) {
    global $translations;
    $text = $translations[$key] ?? $key;
    return $args ? sprintf($text, ...$args) : $text;
}