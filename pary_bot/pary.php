<?php
$dataFile = '../json_data/clients.json';
$ringerFile = '../json_data/ringers.json';
$paryFile = '../json_data/pary.json';
$actionsFile = '../json_data/actions.json';

# Кількість пар, які будуть нагадуватися
$numProcessedParyNum = 6;

function sendMessageAllClients($message)
{
    $json = readClientFile();

    for ($i = 0; $i < count($json['clients']); ++$i)
    {
        sendMessage($json['clients'][$i]['id'], $message);
    }
}

function readRingerFile()
{
    global $ringerFile;

    $file = file_get_contents($ringerFile);
    $json = json_decode($file, true);

    return $json;
}

function getFullRingers()
{
    $json = readRingerFile();

    # Записування часу за 5 хв до пари
    global $numProcessedParyNum;
    $ringers = [];
    for ($i = 0; $i < $numProcessedParyNum; ++$i)
    {
        array_push($ringers, date('H:i', strtotime($json['full']['from'][$i] . ' -5 minutes')));
    }

    return $ringers;
}

function getShortRingers()
{
    $json = readRingerFile();

    # Записування часу за 5 хв до пари
    global $numProcessedParyNum;
    $ringers = [];
    for ($i = 0; $i < $numProcessedParyNum; ++$i)
    {
        array_push($ringers, date('H:i', strtotime($json['short']['from'][$i] . ' -5 minutes')));
    }

    return $ringers;
}

function readActionsFile()
{
    global $actionsFile;

    $file = file_get_contents($actionsFile);
    $json = json_decode($file, true);

    return $json;
}

function getTodayActions($date)
{
    $json = readActionsFile();

    # Пошук подій у вказаний день
    $array = [];

    for ($i = 0; $i < count($json['actions']); ++$i)
    {
        if ($json['actions'][$i]['date'] == $date)
        {
            array_push($array, $json['actions'][$i]);
        }
    }

    # Якщо масив порожній, то повертається null
    if (count($array) == 0)
    {
        return null;
    }
    else
    {
        return $array;
    }

}

function readClientFile()
{
    global $dataFile;

    checkClientsFile();

    # Відкриття та зчитування у JSON
    $file = file_get_contents($dataFile);
    return json_decode($file, true);
}

function writeClientFile($array)
{
    global $dataFile;

    # Запис у файл
    $jsonData = json_encode($array);
    file_put_contents($dataFile, $jsonData);
}

function checkClientsFile()
{
    global $dataFile;

    # Перевірка чи існує файл, якщо ні, то створюжться
    if (!file_exists($dataFile))
    {
        fopen($dataFile, 'w');
    }

    $file = file_get_contents($dataFile);
    $array = json_decode($file, true);

    # Якщо файл порожній, то записується каркас JSON
    if ($array == null)
    {
        $array = ['clients' => []];

        writeClientFile($array);
    }
}

function addClient ($id, $group)
{
    $array = readClientFile();

    # Перевірка чи клієнта немає в списку
    $isUniqueId = true;
    for ($i = 0; $i < count($array['clients']); ++$i)
    {
        if ($array['clients'][$i]['id'] == $id)
        {
            $isUniqueId = false;
            break;
        }
    }

    # Якщо клієнта немає в списку, то додається
    if ($isUniqueId)
    {
        array_push($array['clients'], ['id' => $id, 'group' => $group]);
    }

    writeClientFile($array);
}

function removeClient($user_id)
{
    $array = readClientFile();

    # Видалення клієнта із списку
    for ($i = 0; $i < $user_id; ++$i)
    {
        if ($array['clients'][$i]['id'] == $user_id)
        {
            unset($array['clients'][$i]);
            break;
        }
    }

    # Переіндексація масиву
    $array['clients'] = array_values($array['clients']);

    writeClientFile($array);
}

function getGroup($client_id)
{
    $array = readClientFile();

    # Пошук групи клієнта
    for ($i = 0; $i < count($array['clients']); ++$i)
    {
        if ($array['clients'][$i]['id'] == $client_id)
        {
            return $array['clients'][$i]['group'];
        }
    }

    return null;
}

function whoGroup($value)
{
    switch ($value)
    {
        case 'P41': return 'П-41';
        case 'P42': return 'П-42';
        default: return 'Невідома група';
    }
}

function changeGroup($client_id, $group)
{
    $array = readClientFile();

    # Пошук та перезаписування групи клієнта
    for ($i = 0; $i < count($array['clients']); ++$i)
    {
        if ($array['clients'][$i]['id'] == $client_id)
        {
            $array['clients'][$i]['group'] = $group;
            break;
        }
    }

    writeClientFile($array);
}

function fullRingers()
{
    $json = readRingerFile();

    # Рядок із розкдалом дзвінків
    $str = 'Розклад дзвінків (повний)' . PHP_EOL;
    for ($i = 0; $i < count($json['full']['from']); ++$i)
    {
        $tmp = ($i + 1) . '. ' . $json['full']['from'][$i] . ' - ' . $json['full']['to'][$i] . PHP_EOL;
        $str = $str . $tmp;
    }

    return $str;
}

function shortRingers()
{
    $json = readRingerFile();

    # Рядок із розкдалом дзвінків
    $str = 'Розклад дзвінків (скорочений)' . PHP_EOL;
    for ($i = 0; $i < count($json['short']['from']); ++$i)
    {
        $tmp = ($i + 1) . '. ' . $json['short']['from'][$i] . ' - ' . $json['short']['to'][$i] . PHP_EOL;
        $str = $str . $tmp;
    }

    return $str;
}

function readParyFile()
{
    global $paryFile;

    $file = file_get_contents($paryFile);
    $json = json_decode($file, true);

    return $json;
}

function getPary($client_id, $day)
{
    $json = readParyFile();

    # Перевірка дня
    if ($day <= 5)
    {
        $group = getGroup($client_id);
        $id = null;
        for ($i = 0; $i < count($json['groups']); ++$i)
        {
            if ($json['groups'][$i]['groupName'] == $group)
            {
                $id = $i;
            }
        }

        # Рядок з розкладом пар
        $str = $json['groups'][$id]['days'][$day - 1]['day'] . PHP_EOL;
        for ($i = 0; $i < count($json['groups'][$id]['days'][$day - 1]['couples']); ++$i)
        {
            $str = $str . $json['groups'][$id]['days'][$day - 1]['couples'][$i]['order'] . '. ';
            $str = $str . $json['groups'][$id]['days'][$day - 1]['couples'][$i]['name'] . PHP_EOL;

            $str = $str . '    ' . $json['groups'][$id]['days'][$day - 1]['couples'][$i]['teacher'] . ' - ';
            $str = $str . $json['groups'][$id]['days'][$day - 1]['couples'][$i]['cabinet'] . PHP_EOL;

            $str = $str . PHP_EOL;
        }

        return $str;
    }
    else
    {
        return null;
    }
}

function getReminderPara($day, $group, $order)
{
    $json = readParyFile();

    $id = -1;
    for ($i = 0; $i < count($json['groups']); ++$i)
    {
        if ($json['groups'][$i]['groupName'] == $group)
        {
            $id = $i;
            break;
        }
    }

    if ($id != -1)
    {
        --$day;
        if ($day < 5)
        {
            for ($i = 0; $i < count($json['groups'][$id]['days'][$day]['couples']); ++$i)
            {
                if ($json['groups'][$id]['days'][$day]['couples'][$i]['order'] == $order)
                {
                    return [
                        'name'    => $json['groups'][$id]['days'][$day]['couples'][$i]['name'],
                        'teacher' => $json['groups'][$id]['days'][$day]['couples'][$i]['teacher'],
                        'cabinet' => $json['groups'][$id]['days'][$day]['couples'][$i]['cabinet']
                    ];
                }
            }

            return null;
        }
        else
        {
            return null;
        }
    }
    else
    {
        throw new Exception('Group ' . $group . ' not found');
    }
}