<?php 

    interface iView
    {
        public function generate($content_view, $template_view, $data = null);
    }

    class View implements iView
    {
        
        # public $template_view; # можна вказати загальний вигляд по замовчуванню
        
        # $content_file - види, які відображають контент сторінки;
        # $template_file - загальний для всіх шаблон;
        # $data - масив, який заповнюється в моделі.

        function generate($content_view, $template_view, $data = null)
        {
            include 'application/views/' . $template_view;
        }
    }