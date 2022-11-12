<?php
    header("Access-Control-Allow-Origin: *");
    $PATH = 'main.json';
    $arr = json_decode(file_get_contents($PATH, true));
    if($_POST['indecator'] == "information"){
       // $kurs = filter_var(trim($_POST['kurs']), FILTER_SANITIZE_STRING);
        echo json_encode($arr);
    } else if($_POST['add'] == "kurs"){
        $kurs = filter_var(trim($_POST['kurs']), FILTER_SANITIZE_STRING);
        if($arr != null){
            $i = count($arr);
        } else{
            $i = 0;
        }
        $arr[$i] = $kurs;
    } else if($_POST['add'] == "profesion"){
        array_push($arr, [$kurs => $profesion]);
    } else{
        array_push($arr, [$profesion => $grup]);
    }
    // Повний шлях до JSON-файлу
    

    // Зчитування та зберігання у масив
    //   file_get_contents - просто зчитує весь вміст файлу, другий параметр має бути true, щоб повертався масив, а не об'єкт
    //   json_decode - перетворює текст з синтаксом JSON в асоціативний масив
    

    // Будь-які дії з масивом
    //array_push($arr, [ "kurs" => $kurs]);
    /*if($kurs != null){
        $arr["kurs"][$kurs] = null;
    } else{*/
        
    //}
    // Записування
    //   json_encode - перетворює асоціативний масив у текст з синтаксисом JSON
    //   file_put_contents - просто записує текст у файл
    file_put_contents($PATH, json_encode($arr));
?>