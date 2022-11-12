<?php 

    class ChangeStorage 
    {

        private $json_file;
        private $stored_data = array();

        public function __construct(string $file_name)
        {
            $this->json_file = $file_name;
            $this->stored_data = json_decode(file_get_contents($this->json_file), true);
        }

        # Зберігання даних безпосередньо в файлі tree.json
        private function storeData() : bool
        {
            return file_put_contents($this->json_file, json_encode($this->stored_data, JSON_UNESCAPED_UNICODE));
        }

        # Внести зміни в розкладі 
        public function insertСhangeInCouples(string $group, string $type, int $order, string $date, string $name, string $teacher, int $cabinet) : bool 
        {
            array_push($this->stored_data, ["group" => $group, "type" => $type, "order" => $order, "date" => $date, "name" => $name, "teacher" => $teacher, "cabinet" => $cabinet]);
            return $this->storeData();
        }

        # внести зміни в типі пари 
        public function insertСhangeInRingers(string $group, string $type, string $date) : bool 
        {
            array_push($this->stored_data, ["group" => $group, "type" => $type,  "date" => $date]);
            return $this->storeData(); 
        }

        # внести зміни пар з періодом 
        public function insertСhangeInCouplesWithPeriod(string $group, string $type, int $order, string $date) : bool 
        {
            array_push($this->stored_data, ["group" => $group, "type" => $type, "order" => $order, "date" => $date]);
            return $this->storeData(); 
        }      

    }

    $change = new ChangeStorage('//162.55.60.99/pary-duck-bot.ga/front/json_data/changes.json');
    // $change->insertСhangeInCouples("P-41","replace",3,"10.11.2022","Фізичне виховання","Л. Корнута", 106);
    // $change->insertСhangeInRingers("P41", "short", "10.11.2022");
    $change->insertСhangeInCouplesWithPeriod("P41","none",3,"10.11.2022");
//  //162.55.60.99/pary-duck-bot.ga/front/json_data/changes.json