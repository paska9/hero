<?php 
//build an application with a hero, called Orderus who will fight with monsters from the forests

class Hero{

    public $orderus = [
        'health' => ['min' => 70, 'max' => 100],
        'strength' => ['min' => 70, 'max' => 80],
        'defence' => ['min' => 45, 'max' => 55],
        'speed' => ['min' => 40, 'max' => 50],
        'luck' => ['min' => 10, 'max' => 30],
    ];

    public $orderusSkill = [
        'strike' => 10,
        'shield' => 20
    ];

    public $monster = [
        'health' => ['min' => 60, 'max' => 90],
        'strength' => ['min' => 60, 'max' => 90],
        'defence' => ['min' => 40, 'max' => 60],
        'speed' => ['min' => 40, 'max' => 60],
        'luck' => ['min' => 25, 'max' => 40],
    ];

    public function run(){

        $orderusProperties = $this->getProprietesPlayers($this->orderus);
        $monsterProperties = $this->getProprietesPlayers($this->monster);

        if (!empty($orderusProperties) && !empty($monsterProperties)){

            echo ("Orderus will be starting with the fallowing properties:<br>");
            foreach($orderusProperties as $key => $item){
                echo ($key . " - " . $item . "<br>");
            }
            echo ("<br>");
            
            echo ("Monster will be starting with the fallowing properties:<br>");
            foreach($monsterProperties as $key => $item){
                echo ($key . " - " . $item . "<br>");
            }

            echo ("<br>Let's Start<br>");

            $this->fight($orderusProperties, $monsterProperties);
        }
    }


    public function fight(array $orderus, array $monster){

        if ($orderus['speed'] != $monster['speed'] && $orderus['speed'] > $monster['speed']){
            $striker = 'orderus';
            $deffender = 'monster';
        }else if($orderus['luck'] != $monster['luck'] && $orderus['luck'] > $monster['luck']) {
            $striker = 'orderus';
            $deffender = 'monster';
        }else{
            $striker = 'monster';
            $deffender = 'orderus';
        }

        unset($orderus['speed'], $orderus['luck'], $monster['speed'], $monster['luck']);

        $num = 1;
        $this->fightResult = [];

        while($num <= 20 && $orderus['health'] >= 0 && $monster['health'] >= 0) {

            $damage = $striker == 'orderus' ? $orderus['strength'] - $monster['defence'] : $monster['strength'] - $orderus['defence'];

            $skill = $this->checkSkills($striker);

            if ($striker == 'orderus' && $skill['orderusStrike'] == 1){
                $damage = $damage * 2;
            }

            if ($striker == 'monster' && $skill['orderusShield'] == 1){
                $damage = round($damage / 2, 0);
            }

            $res = [
                'striker' => $striker, 
                'deffender' => $deffender, 
                'damage' => $damage,
                'orderusHealth' => $striker == 'orderus' ? $orderus['health'] : $orderus['health'] = $orderus['health'] - $damage, 
                'monsterHealth' => $striker == 'monster' ? $monster['health'] : $monster['health'] = $monster['health'] - $damage,
                'skills' => ['orderusStrike' => $skill['orderusStrike'], 'orderusShield' => $skill['orderusShield']]  
            ];

            $this->fightResult[$num] = $res;

            $striker = $striker == 'orderus' ? 'monster' : 'orderus';
            $deffender = $deffender == 'orderus' ? 'monster' : 'orderus';

            $num++;
        }

        $this->getResult($this->fightResult);
    }

    public function getProprietesPlayers(array $player = []) : array {
        if (!empty($player)){
            foreach($player as $key => $val){
                $player[$key] = $this->getRandom($val['min'], $val['max']);
            }
            return $player;
        }

        return [];
    }


    public function getRandom($value1, $value2){
        return rand($value1, $value2);
    }

    public function checkSkills($striker = '') : array {
        $res = ['orderusStrike' => 0, 'orderusShield' => 0];

        //for first fight, get random skills
        if(empty($this->fightResult)){
            if ($striker == 'orderus'){
                $res['orderusStrike'] = $this->getRandom(0,1);
            }else{
                $res['orderusShield'] = $this->getRandom(0,1);
            }
        }else{
            if($striker == 'orderus'){
                $ordStrike = $this->checkLatestSkill($striker, 'orderusStrike', $this->orderusSkill['strike']);   

                if($ordStrike == 0){
                    $res['orderusStrike'] = $this->getRandom(0,1);
                }
                
            }else{
                $ordShield = $this->checkLatestSkill($striker, 'orderusShield', $this->orderusSkill['shield']);

                if($ordShield == 0){
                    $res['orderusShield'] = $this->getRandom(0,1);
                }
            }

        }
        return $res;
    }

    public function checkLatestSkill($striker, $key, $value = 0){
        $res = [];
        $search = 0;
       
        if($value > 0){
            //prepare array for validate data
            foreach($this->fightResult as $item){
                if($item['striker'] == $striker){
                    array_push($res, $item['skills'][$key]);
                }
            }
            array_reverse($res);

            //check, how meny times this skills can be used
            $used = round(100 / $value, 0);

            if(count($res) < $used){ 
                return in_array(1, $res) ? 1 : 0;
            }else{
                while(count($res) > $used){
                    $res = array_slice($res, $used);
                }

                return in_array(1, $res) ? 1 : 0;
            }
        }

        return $search;
    }

    public function getResult(array $result = []){
        $num = count($result);

        echo("<br>");

        if(!empty($result)){
            foreach($result as $fight => $item){

                $striker = $item['striker'];
                $deffender = $item['deffender'];
                $damage = $item['damage'];

               echo ($fight . " Fight <br>");
               echo ($striker . " - " . $deffender . " | damage=" . $damage . " | Orderus=" . $item['orderusHealth'] . " | Monster=" . $item['monsterHealth'] . " | ");

               if ($item['skills']['orderusStrike'] == 1){
                echo (" Orderus used Strike ");
               }else if ($item['skills']['orderusShield'] == 1){
                echo (" Orderus used Shield ");
               } else {
                echo (" no skill used ");
               }
               echo("<br><br>");

               
               //check who WIN
               if($fight == $num){
                if ($item['orderusHealth'] > 0){
                    echo ("WIN Orderus");
                }else if($item['monsterHealth'] > 0){
                    echo ("WIN Monster");
                }else{
                    echo ("Nobody Won");
                }
               }
            }  
        }
    }
}


$obj = new Hero();
$hero = $obj->run();

