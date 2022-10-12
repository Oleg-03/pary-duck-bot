<?php

# підключаємо файли ядра
require_once 'core/model.php';
require_once 'core/view.php';
require_once 'core/controller.php';
require_once 'core/route.php';
# запускаємо маршрутизатор
Route::start(); 