<?php 

    header("Access-Control-Allow-Origin: *");

    class TreeStorage
    {
        private $json_file;
        private $stored_data = array();

        public function __construct($file_name)
        {
            $this->json_file = $file_name;
            $this->stored_data = json_decode(file_get_contents($this->json_file), true);
        }

        # Зберігання даних безпосередньо в файлі tree.json
        private function storeData() : bool
        {
            return file_put_contents($this->json_file, json_encode($this->stored_data, JSON_UNESCAPED_UNICODE));
        }

        # Повернення дерева (повного масиву)
        public function getTree() : array 
        {
            return $this->stored_data;
        }

        # Внести новий курс в дерево 
        public function insertCourseName(string $course_name) : bool
        {
            # кількість записів 
            $number_of_records = count($this->stored_data['tree']);
            # Вводимо новий курс 
            $this->stored_data['tree'][$number_of_records]['kursName'] = $course_name;
            # Створюємо необхідну структуру для спеціальностей
            $this->stored_data['tree'][$number_of_records][$course_name] = [];

            return $this->storeData();
        }

        # Внести нову спеціальність за назвою курсу в дерево 
        public function insertSpecialtyName(string $course_name, string $specialty_name) : bool
        {
            # Прапорець для перевірки наявності курсу
            $check_exist = false;
            $index;

            for ($i = 0; $i < count($this->stored_data['tree']); $i++)
            {
                # Перевірка наявності курсу
                if ($this->stored_data['tree'][$i]['kursName'] == $course_name)
                {
                    # Встановлення прапорця (true)
                    $check_exist = true;
                    # Витягнення номеру запису 
                    $number_of_records = count($this->stored_data['tree'][$i][$course_name]);
                    $index = $i;
                }
            }

            if ($check_exist)
            {   
                # Внесення спеціальності 
                $this->stored_data['tree'][$index][$course_name][$number_of_records]['specialtyName'] = $specialty_name;
                # Внесення структури до конкретної спеціальності
                $this->stored_data['tree'][$index][$course_name][$number_of_records][$specialty_name] = [];
                
                return $this->storeData();
            }
            else {return false;}
        }

        # внести групу
        public function insertGroup(string $course_name, string $specialty_name, string $group_name) : bool
        {  
            # прапорець перевірки існування курсу 
            $check_exist_course = false;
            # прапорець перевірки існування спеціальності
            $check_exist_specialty = false;
            # індекс по і
            $index;
            # індекс по j
            $jndex;

            for ($i = 0; $i < count($this->stored_data['tree']); $i++)
            {
                # Перевірка наявності курсу
                if ($this->stored_data['tree'][$i]['kursName'] == $course_name)
                {   
                    for ($j = 0; $j < count($this->stored_data['tree'][$i][$course_name]); $j++)
                    {
                        # Перевірка наявності спеціальності
                        if ($this->stored_data['tree'][$i][$course_name][$j]['specialtyName'] == $specialty_name)
                        {
                            # Встановлення прапорця курсів (true)
                            $check_exist_course = true;
                            # Встановлення прапорця спеціальності (true)
                            $check_exist_specialty = true;
                            # Витягнення номеру запису 
                            $number_of_records = count($this->stored_data['tree'][$i][$course_name][$j][$specialty_name]);
                            # витягнення індексів: 
                            $index = $i;
                            $jndex = $j;
                        }

                    } 

                }

            }

            if ($check_exist_course && $check_exist_specialty)
            {
                # Запис назви групи в масив 
                $this->stored_data['tree'][$index][$course_name][$jndex][$specialty_name][$number_of_records] = $group_name;
                return $this->storeData();
            }
            else return false;
        }

        # видалити групу 
        public function removeGroup(string $course_name, string $specialty_name, string $group_name) : bool
        {
            for ($i = 0; $i < count($this->stored_data['tree']); $i++)
            {
                # Перевірка наявності курсу
                if ($this->stored_data['tree'][$i]['kursName'] == $course_name)
                {   
                    for ($j = 0; $j < count($this->stored_data['tree'][$i][$course_name]); $j++)
                    {
                        # Перевірка наявності спеціальності
                        if ($this->stored_data['tree'][$i][$course_name][$j]['specialtyName'] == $specialty_name)
                        {
                            for ($k = 0; $k < count($this->stored_data['tree'][$i][$course_name][$j][$specialty_name]); $k++)
                            {
                                # перевірка наявності групи
                                if ($this->stored_data['tree'][$i][$course_name][$j][$specialty_name][$k] == $group_name)
                                {
                                    # видалення групи
                                    unset($this->stored_data['tree'][$i][$course_name][$j][$specialty_name][$k]);
                                    return $this->storeData();
                                }

                            }

                        }

                    } 

                }

            } 

        }

        # видалити спеціальність 
        public function removeSpecialtyName(string $course_name, string $specialty_name) : bool
        {
            for ($i = 0; $i < count($this->stored_data['tree']); $i++)
            {
                # Перевірка наявності курсу
                if ($this->stored_data['tree'][$i]['kursName'] == $course_name)
                {   
                    for ($j = 0; $j < count($this->stored_data['tree'][$i][$course_name]); $j++)
                    {
                        $number_of_records = count($this->stored_data['tree'][$i][$course_name]);
                        # Перевірка наявності спеціальності
                        if ($this->stored_data['tree'][$i][$course_name][$j]['specialtyName'] == $specialty_name)
                        {
                            # Видалення спеціальності 
                            unset($this->stored_data['tree'][$i][$course_name][$j]);
                            return $this->storeData();
                        }
                    
                    }
                
                }
           
            }
        
        }

        # видалити курс 
        public function removeCourseName(string $course_name) : bool
        {
            for ($i = 0; $i < count($this->stored_data['tree']); $i++)
            {
                # Перевірка наявності курсу
                if ($this->stored_data['tree'][$i]['kursName'] == $course_name)
                {   
                    unset($this->stored_data['tree'][$i]);
                    return $this->storeData();            
                }

            }

        }

    }

    $Tree = new TreeStorage('//162.55.60.99/pary-duck-bot.ga/front/json_data/tree.json');
    // $Tree->insertCourseName('2 Kurs');
    // $Tree->insertSpecialtyName('2 Kurs', 'M');

    //  print_r($Tree->getTree());

