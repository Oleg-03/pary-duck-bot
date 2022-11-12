<?php 

    class AdvertisementStorage
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

        # Повернення оголошень (повного масиву)
        public function getAdvertisement() : array 
        {
            return $this->stored_data;
        }

        private function insertForGroup(string $send, string $text, string $group) : bool
        {
            array_push($this->stored_data, ["group" => $group, "send" => $send, "text" => $text]);
            return $this->storeData();
        }

        private function insertForCourse(string $send, string $text, string $course) : bool
        {
            array_push($this->stored_data, ["course" => $course, "send" => $send, "text" => $text]);
            return $this->storeData();
        }

        private function insertForSpecialty(string $send, string $text, string $specialty) : bool
        {
            array_push($this->stored_data, ["specialty" => $specialty, "send" => $send, "text" => $text]);
            return $this->storeData();
        }

        private function insertForAll(string $send, string $text) : bool
        {
            array_push($this->stored_data, ["all" => true, "send" => $send, "text" => $text]);
            return $this->storeData();
        }

        public function insert(string $send, string $text, string $group, string $course, string $specialty, bool $all = false) : bool
        {
            if ($all == true)
            {
                return $this->insertForAll($send, $text);
            }
            else
            {
                if ($group != "")
                {
                    return $this->insertForGroup($send, $text, $course);
                }
                else if ($course != "")
                {
                    return $this->insertForCourse($send, $text, $course);
                }
                else if ($specialty != "")
                {
                    return $this->insertForSpecialty($send, $text, $specialty);
                }
                else
                {
                    throw new Exception("Not selected for insertion with [all, group, course, specialty]");
                }
            }
        }

    }

// //162.55.60.99/pary-duck-bot.ga/front/json_data/advertisement.json
    $advertisement = new AdvertisementStorage('//162.55.60.99/pary-duck-bot.ga/front/json_data/advertisement.json');
   
    // print_r($advertisement->getAdvertisement());