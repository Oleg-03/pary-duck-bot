<?php
try
{
    ini_set('log_errors', 1);
    ini_set('error_log', 'logfile.log');

    require_once('Telegram.API.php');
    require_once('like.php');
    require_once('pary.php');

    setToken('');

    # Відповідь клієнта, яку потрібно опрацювати
    $keyboard_message_id = ['message_id' => null, 'client_id' => null, 'command' => null];
    
    # Теперішня пара
    $nowRingerNum = 0;

    # Скорочені чи повні пари
    $ringerType = 'full';

    # Розклад дзвінків
    $fullRingers = getFullRingers();
    $shortRingers = getShortRingers();
    
    $ringerTime = $fullRingers[$nowRingerNum];
    
    # Теперіній час
    $date = date('d.m.Y H:i');
    
    # Масив з подіями
    $actions = getTodayActions(substr($date, 0, 10));

    # Масив із змінами у розкладі
    $replaces = [];

    # Чи було надіслано сповіщення про пару
    $isSend = false;

    while (true)
    {

#################### НАДСИЛАННЯ СПОВІЩЕНЬ ####################
        if ($date != date('d.m.Y H:i'))
        {
            $actions = getTodayActions(substr($date, 0, 10));
            $date = date('d.m.Y H:i');   
        }

        if ($actions != null)
        {
            $actionCounts = count($actions);
            for ($i = 0; $i < $actionCounts; ++$i)
            {
                switch ($actions[$i]['type'])
                {
                    case 'adv':
                    {
                        sendMessageAllClients($actions[$i]['val']);
                        unset($actions[$i]);
                        break;
                    }

                    case 'replace':
                    {
                        array_push($replaces, $actions[$i]['val']);
                        unset($actions[$i]);
                        break;
                    }

                    case 'short':
                    {
                        $ringerType = 'short';
                        unset($actions[$i]);
                        break;
                    }
                }
            }
        }

        if (substr($date, 11) == $ringerTime && $isSend == false)
        {
            if ($nowRingerNum < $numProcessedParyNum)
            {
                # Отримання часів дзвінків
                if ($ringerType == 'full')
                {
                    $ringerTime = $fullRingers[$nowRingerNum];
                }
                elseif ($ringerType == 'short')
                {
                    $ringerTime = $shortRingers[$nowRingerNum];
                }

                $todayMessages = [];

                # Надсилання студентам їхню пару за 5 хв до пари
                $clients = readClientFile()['clients'];
                $para = null;
                for ($i = 0; $i < count($clients); ++$i)
                {
                    # Змінна, яка позначає чи відбулася заміна
                    $isReplaced = false;
                    # Для оптимізації
                    if (count($replaces) != 0)
                    {
                        # Проходженням списку сьогоднішніх замін
                        for ($j = 0; $j < count($replaces); ++$j)
                        {
                            if ($replaces[$j]['order'] == ($nowRingerNum + 1))
                            {
                                if ($replaces[$j]['group'] == $clients[$i]['group'])
                                {
                                    # Якщо пари не буде ('none' - умовне позначення, що пари не буде)
                                    if ($replaces[$j]['name'] == 'none')
                                    {
                                        $para = null;
                                        $isReplaced = true;
                                    }
                                    # Якщо буде заміна
                                    else
                                    {
                                        $para =
                                        [
                                            'name'    => $replaces[$j]['name'],
                                            'teacher' => $replaces[$j]['teacher'],
                                            'cabinet' => $replaces[$j]['cabinet']
                                        ];

                                        $isReplaced = true;
                                    }
                                }
                            }
                        }
                    }
                    
                    # Якщо заміна не відбулася, то пара береться із розкладу
                    if ($isReplaced == false)
                    {
                        $para = getReminderPara(date('w'), $clients[$i]['group'], ($nowRingerNum + 1));
                    }

                    # Якщо пара = null, то не потрібно надсилати повідомлення
                    if ($para != null)
                    {
                        sendMessage($clients[$i]['id'],
                            (
                                $para['name'] . PHP_EOL . 
                                $para['teacher'] . ' - ' . $para['cabinet']
                            )
                        );
                    }
                }

                ++$nowRingerNum;
                $isSend = true;
            }
            else
            {
                $isSend = false;

                # Видалення повідомлень надісланих за сьогоднішній день
                for ($i = 0; $i < count($todayMessages); ++$i)
                {
                    deleteMessage($todayMessages[$i]['client_id'], $todayMessages[$i]['message_id']);
                }

                $nowRingerNum = 0;
                $ringerType = 'full';
            }
        }
########################################

        # Отримання останнього повідомлення
        $json = getUpdates();

        if ($json != null)
        {
#################### ОБРОБКА КОМАНД ####################
            # Якщо це текстове повідомлення
            if (array_key_exists('message', $json))
            {
                $message_client_id = &$json['message']['from']['id'];
                
                # Виконання команд
                switch ($json['message']['text'])
                {
                    # Змінити групу
                    case '/change':
                    {
                        if (getGroup($message_client_id) != null)
                        {
                            $message_id = sendKeyboard($message_client_id, 'Обери іншу групу:', $keyboard =
                                [
                                    'inline_keyboard' =>
                                    [
                                        [
                                            ['text' => 'П-41', 'callback_data' => 'P41'],
                                            ['text' => 'П-42', 'callback_data' => 'P42']
                                        ]
                                    ]
                                ])['result']['message_id'];

                            # Записування інформації для обробки команди
                            $keyboard_message_id['client_id'] = $message_client_id;
                            $keyboard_message_id['message_id'] = $message_id;
                            $keyboard_message_id['command'] = '/change';
                        }
                        else
                        {
                            # Надсилання повідомлення, якщо клієнта немає в списку
                            sendMessage($message_client_id, 'Щоб змінити групу потрібно почати. Скористайся /start');
                        }

                        break;
                    }

                    # Почати
                    case '/start':
                    {
                        # Якщо клієнта не зареєстрований
                        if (getGroup($message_client_id) == null)
                        {
                            # Вибір групи
                            $message_id = sendKeyboard($message_client_id, 'Обери групу:', $keyboard =
                                [
                                    'inline_keyboard' =>
                                    [
                                        [
                                            ['text' => 'П-41', 'callback_data' => 'P41'],
                                            ['text' => 'П-42', 'callback_data' => 'P42']
                                        ]
                                    ]
                                ])['result']['message_id'];

                                # Записування інформації для обробки команди
                                $keyboard_message_id['client_id'] = $message_client_id;
                                $keyboard_message_id['message_id'] = $message_id;
                                $keyboard_message_id['command'] = '/start';
                        }
                        else
                        {
                            # Надсилання повідомлення, якщо клієнт є в списку клієнтів
                            sendMessage($message_client_id, 'Для зміни групи використай команду /change');
                        }

                        break;
                    }
                    
                    # Дізнатися групу
                    case '/group':
                    {
                        $clientGroup = getGroup($message_client_id);
                        if ($clientGroup != null)
                        {
                            $group = whoGroup($clientGroup);
                            sendMessage($message_client_id, "Твоя група: {$group}");
                        }
                        else
                        {
                            # Надсилання повідомлення, якщо клієнта немає в списку
                            sendMessage($message_client_id, 'Щоб змінити групу потрібно почати. Скористайся /start');
                        }
                        break;
                    }

                    # Зупинити
                    case '/stop':
                    {
                        if (getGroup($message_client_id) != null)
                        {
                            removeClient($message_client_id);
                            sendMessage($message_client_id, 'Зупинено');
                        }
                        break;
                    }

                    # Розклад звінків
                    case '/ringings':
                    {
                        if (getGroup($message_client_id) != null)
                        {
                            sendMessage($message_client_id, fullRingers());
                            sendMessage($message_client_id, shortRingers());
                        }
                        else
                        {
                            # Надсилання повідомлення, якщо клієнта немає в списку
                            sendMessage($message_client_id, 'Щоб переглянути розклад дзвінків потрібно почати. Скористайся /start');
                        }
                        break;
                    }

                    # Розклад пар
                    case '/all':
                    {
                        if (getGroup($message_client_id) != null)
                        {
                            for ($j = 0; $j < 5; ++$j)
                            {
                                sendMessage($message_client_id, getPary($message_client_id, ($j + 1)));
                            }
                        }
                        else
                        {
                            # Надсилання повідомлення, якщо клієнта немає в списку
                            sendMessage($message_client_id, 'Щоб переглянути розклад пар потрібно почати. Скористайся /start');
                        }
                        break;
                    }

                    # Сьогоднішні пари
                    case '/today':
                    {
                        if (getGroup($message_client_id) != null)
                        {
                            $date = date('w');
                            if ($date <= 5 && $date != 0)
                            {
                                $str = getPary($message_client_id, $date);
                                sendMessage($message_client_id, $str);
                            }
                            else
                            {
                                sendMessage($message_client_id, 'Сьогодні немає пар');
                            }
                        }
                        else
                        {
                            # Надсилання повідомлення, якщо клієнта немає в списку
                            sendMessage($message_client_id, 'Щоб переглянути сьогоднішні пари потрібно почати. Скористайся /start');
                        }
                        break;
                    }

                    # Завтрашні пари
                    case '/tomorrow':
                    {
                        if (getGroup($message_client_id) != null)
                        {
                            $date = date('w');

                            if ($date != 5 && $date != 6)
                            {
                                $str = getPary($message_client_id, ($date + 1));
                                sendMessage($message_client_id, $str);
                            }
                            else
                            {
                                sendMessage($message_client_id, 'Завтра немає пар');
                            }
                        }
                        else
                        {
                            # Надсилання повідомлення, якщо клієнта немає в списку
                            sendMessage($message_client_id, 'Щоб дізнатися пари на завтра потрібно почати. Скористайся /start');
                        }
                        break;
                    }
                }
            }
########################################

######################## ОБРОБКА КНОПОК ####################
            # Якщо натиснута кнопка
            elseif (array_key_exists('callback_query', $json))
            {
                if ($json['callback_query']['message']['message_id'] == $keyboard_message_id['message_id'])
                {
                    switch ($keyboard_message_id['command'])
                    {
                        # Обробка відповіді початку
                        case '/start':
                        {
                            # Додавання клієнта в список клієнтів
                            addClient($keyboard_message_id['client_id'], $json['callback_query']['data']);

                            # Видалення повідомлення із вибором групи
                            deleteMessage($keyboard_message_id['client_id'], $json['callback_query']['message']['message_id']);
                            
                            # Надсилання повідомлення із вибраним варіантом
                            $group = whoGroup($json['callback_query']['data']);
                            sendMessage($keyboard_message_id['client_id'], ("Обрано групу: {$group}"));

                            break;
                        }
                        
                        # Обробка відповіді зміни групи
                        case '/change':
                        {
                            if (getGroup($keyboard_message_id['client_id']) != $json['callback_query']['data'])
                            {
                                # Змінення групи в клієнта
                                changeGroup($keyboard_message_id['client_id'], $json['callback_query']['data']);

                                # Надсилання повідомлення із вибраним варіантом
                                $group = whoGroup($json['callback_query']['data']);
                                sendMessage($keyboard_message_id['client_id'], "Групу змінено на: {$group}");
                            }
                            else
                            {
                                # Надсилання повідомлення, якщо вибраний той самий варіант
                                sendMessage($keyboard_message_id['client_id'], 'Вибрана та сама група. Якщо це помилка, то спробуй ще раз /change');
                            }
                            
                            # Видалення повідомлення із вибором групи
                            deleteMessage($keyboard_message_id['client_id'], $json['callback_query']['message']['message_id']);

                            break;
                        }
                    }
                }
            }
########################################
        }

        # Затримка
        // usleep(100);
    }
}
catch (Exception $e)
{
    $file = fopen('logfile.log', 'a');
    fwrite($file, (date('H:i:s d.m.Y ') . $e . PHP_EOL));
    fclose($file);

    echo (date('H:i:s d.m.Y ') . $e . PHP_EOL);
}
