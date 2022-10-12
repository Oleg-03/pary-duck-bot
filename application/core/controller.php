<?php 

    interface iController
    {
        public function action_index();
    }

    class Controller implements iController
    {
        public $model;
        public $view;
        
        function __construct()
        {
            $this->view = new View();
        }
        
        # дія, яка викликається по замовчуванню
        function action_index()
        {
            # todo	
        }
    }