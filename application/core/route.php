<?php 

    # Клас-маршрутизатор для розпізнавання сторінки на запит:
    # > зачепляє класи контроллерів та моделей;
    # > створює екземпляри викликаних контроллерів та викликає дії цих масих контроллерів.
    
    interface iRoute
    {
        public static function start();
        public function ErrorPage404();
    }

    class Route implements iRoute
    {
        public static function start()
        {
            # назва контроллера та дія по замовчуванню
            $controller_name = 'Main';
            $action_name = 'Index';

            # отримання масиву з маршруту
            $routes = explode('/', $_SERVER['REQUEST_URI']);

            # отримуємо назву контроллера 
            if (!empty($routes[1]))
            {
                $controller_name = $routes[1];
            }

            # отримуємо назву дії
            if (!empty($routes[2]))
            {
                $action_name = $routes[2];
            }

            # додаэмо префікси
            $model_name = 'Model_' . $controller_name;
            $controller_name = 'Controller_' . $controller_name;
            $action_name = 'action_' . $action_name;
            
            # підчеплюємо файл з класом моделі, якого може і не бути 
            $model_file = strtolower($model_name) . 'php';
            $model_path = 'application/models/' . $model_file;

            if (file_exists($model_path))
            {
                require_once $model_path;
            }

            # підчеплюємо файл з класом контроллера
            $controller_file = strtolower($controller_name) . 'php';
            $controller_path = 'application/controllers/' . $controller_file;

            if (file_exists($controller_path))
            {
                require_once $controller_path;
            }
            else 
            {
                Route::ErrorPage404();
            }

            # створюєм контроллер
            $controller = new $controller_name;
            $action = $action_name;

            if (method_exists($controller, $action))
            {
                # викликаєм дію контроллера
                $controller->$action();
            }
            else 
            {
                Route::ErrorPage404();
            }
            
        } 

        public function ErrorPage404()
        {
            $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
            header('HTTP/1.1 404 Not Found');
            header("Status: 404 Not Found");
            header('Location:'.$host.'404');
        }
    }
