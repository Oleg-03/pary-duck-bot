<?php 

    class ParyStorage
    {
        private $json_file;
        private $stored_data = array();
        private $subarray = array();
        private $stored_group;

        public function __construct(string $file_name, string $group)
        {
            $this->json_file = $file_name;
            $this->stored_group = $group;
            $this->stored_data = json_decode(file_get_contents($this->json_file), true);
        }

        # Зберігання даних безпосередньо в файлі tree.json
        private function storeData() : bool
        {
            return file_put_contents($this->json_file, json_encode($this->stored_data, JSON_UNESCAPED_UNICODE));
        }

        # Повертає підмасив, який містить кокретну групу
        public function getCouples() : array
        {
            # Перевіряємо структуру масиву 
            $this->checkStructure();
            # присвоюємо підмасиву масив  
            $this->subarray = $this->stored_data;
            for ($i = 0; $i < count($this->subarray['groups']); $i++)
            {
                # Перевіряємо, чи запис не належить поточній групі 
                if ($this->subarray['groups'][$i]['groupName'] != $this->stored_group)
                {
                    # Видаляємо групу, яка не вибрана
                    unset($this->subarray['groups'][$i]);
                }

            }
            # Повертаємо підмасив 
            return $this->subarray;
        }

        # Створення необхідної структури для групи 
        private function createStructure() : bool 
        {
            # Кількість записів 
            $number_of_records = count($this->stored_data['groups']);

            # Створення групи 
            $this->stored_data['groups'][$number_of_records]['groupName'] = $this->stored_group;
            # Створення масиву днів 
            $this->stored_data['groups'][$number_of_records]['days'] = [];
            # Зберігання даних
            return $this->storeData();
        }

        # Перевірка необхідної структури для групи  
        private function checkStructure() : bool 
        {
            for ($i = 0; $i < count($this->stored_data['groups']); $i++)
            {
                # Перевіряємо, чи існує група 
                if ($this->stored_data['groups'][$i]['groupName'] == $this->stored_group)
                {
                    # нічого не робимо 
                    return true;
                }

            }
            # Створюємо необхідну структуру
            return $this->createStructure();
        }

        # Внесення пари 
        public function insertCouple(string $day, int $order, string $name, string $teacher, int $cabinet, string $link) : bool
        {
            # Перевірка необхідної структури для групи
            $this->checkStructure();
            # Прапорець для перевірки існування дня 
            $check_exist_day = false;
            # Прапорець для перевірки існування пари 
            $check_exist_couple = false;
            for ($i = 0; $i < count($this->stored_data['groups']); $i++)
            {
                # перевірка існування групи
                if ($this->stored_data['groups'][$i]['groupName'] == $this->stored_group)
                {
                    # кількість днів у групи
                    $SIZE = count($this->stored_data['groups'][$i]['days']);
                    # індекс по i
                    $index = $i;
                    for ($j = 0; $j < $SIZE; $j++)
                    {
                        # Перевірка наявності дня 
                        if ($this->stored_data['groups'][$i]['days'][$j]['day'] == $day)
                        {
                            # заповнення прапорця днів 
                            $check_exist_day = true;
                            # індекс по j
                            $jndex = $j;
                            for ($l = 0; $l < count($this->stored_data['groups'][$i]['days'][$j]['couples']); $l++)
                            {
                                if ($this->stored_data['groups'][$i]['days'][$j]['couples'][$l]['order'] == $order)
                                {
                                    $lndex = $l;
                                    $check_exist_couple = true;
                                }
                            }
                            
                        }

                    }

                }

            }
            
            # якщо дня не існує
            if (!$check_exist_day)
            {
                # створюємо необхідну структуру 
                $this->stored_data['groups'][$index]['days'][$SIZE]['day'] = $day;
                $this->stored_data['groups'][$index]['days'][$SIZE]['couples'] = [];
                # вилучаємо кількість записів пар
                $num_of_records = count($this->stored_data['groups'][$index]['days'][$SIZE]['couples']);
                # вносимо необхідні поля 
                $this->stored_data['groups'][$index]['days'][$SIZE]['couples'][$num_of_records]['order'] = $order;
                $this->stored_data['groups'][$index]['days'][$SIZE]['couples'][$num_of_records]['name'] = $name;
                $this->stored_data['groups'][$index]['days'][$SIZE]['couples'][$num_of_records]['teacher'] = $teacher;
                $this->stored_data['groups'][$index]['days'][$SIZE]['couples'][$num_of_records]['cabinet'] = $cabinet;
                $this->stored_data['groups'][$index]['days'][$SIZE]['couples'][$num_of_records]['link'] = $link;
                # зберігаємо дані в файлі 
                return $this->storeData();
            }
            # якщо день та пара існує  
            else if ($check_exist_day && $check_exist_couple)
            {
                 # витягуємо кількість записів 
                 $num_of_records = count($this->stored_data['groups'][$index]['days'][$jndex]['couples']);
                 # зберігаємо необхідні поля 
                 $this->stored_data['groups'][$index]['days'][$jndex]['couples'][$lndex]['order'] = $order;
                 $this->stored_data['groups'][$index]['days'][$jndex]['couples'][$lndex]['name'] = $name;
                 $this->stored_data['groups'][$index]['days'][$jndex]['couples'][$lndex]['teacher'] = $teacher;
                 $this->stored_data['groups'][$index]['days'][$jndex]['couples'][$lndex]['cabinet'] = $cabinet;
                 $this->stored_data['groups'][$index]['days'][$jndex]['couples'][$lndex]['link'] = $link;
                 # вносимо дані в файл
                 return $this->storeData();
            }
            else 
            {
                # витягуємо кількість записів 
                $num_of_records = count($this->stored_data['groups'][$index]['days'][$jndex]['couples']);
                # зберігаємо необхідні поля 
                $this->stored_data['groups'][$index]['days'][$jndex]['couples'][$num_of_records]['order'] = $order;
                $this->stored_data['groups'][$index]['days'][$jndex]['couples'][$num_of_records]['name'] = $name;
                $this->stored_data['groups'][$index]['days'][$jndex]['couples'][$num_of_records]['teacher'] = $teacher;
                $this->stored_data['groups'][$index]['days'][$jndex]['couples'][$num_of_records]['cabinet'] = $cabinet;
                $this->stored_data['groups'][$index]['days'][$jndex]['couples'][$num_of_records]['link'] = $link;
                # вносимо дані в файл
                return $this->storeData();
            }

        }        
        
    }

    $pary = new ParyStorage('//162.55.60.99/pary-duck-bot.ga/front/json_data/pary.json', "P41");
    // $pary->insertCouple("Понеділок", 4, 'ss', 'ss', 'ss', 'ss');
    // print_r($pary->getCouples());
    $pary->insertCouple('Неділок', 1, 'а-ну', '22', '22', '22');
    // print_r($pary->getCouples());