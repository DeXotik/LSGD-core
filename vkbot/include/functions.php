<?php
    class Functions{
        public function vk($method, $array){
            require 'config.php';

    		$config = [
    			'access_token' => $accessKey,
    			'v' => $v,
    			'random_id' => rand(1, 999999999)
    		];
    
    		$params = http_build_query(array_merge($array, $config));
    
    		return json_decode(file_get_contents('https://api.vk.com/method/'.$method.'?'.$params));
    	}

        public function sendMessage($chatID, $message){
            return $this->vk('messages.send', ['peer_id' => $chatID, 'message' => $message]);
        }

        public function api($method, $array){
            require 'config.php';

    	    $params = http_build_query($array);
    	    return json_decode(file_get_contents($api.'/'.$method.'?'.$params));
    	}

        public function checkPrefix($type, $message){
            $text = explode(" ", $message);

            if($type){
                if($text[0][0] == '!' OR $text[0][0] == '/' OR $text[0][0] == ':') $checked = true;
            } else {
                if($text[0][0] == ':') $checked = true;
            }

            if($checked){
                return ['prefix' => true, 'message' => substr($message, 1)];
            } elseif(strlen(trim($message)) > 0){
                return ['prefix' => true, 'message' => $message];
            } else {
                return ['prefix' => false];
            }
        }

        public function checkOwnCommand($data){
            $chatID = $data['chatID'];
            $userID = $data['userID'];

            $checkAuth = $this->checkAuth($userID);

            $output = $this->checkPrefix(true, $data['message']);
            if($output['prefix']){
                $message = $output['message']; $output = false;

                $lines = explode("\n", $message);
                for($i = 0; $i < count($lines); $i++){
                    $line[$i] = explode(" ", $lines[$i]);
                }

                $line[0][0] = mb_strtolower($line[0][0]);

                require_once 'commands.php';
                $c = new Commands();

                switch($line[0][0]){
                    case 'help': case 'помощь':
                        $output = $c->help();
                    break;
                    case 'commands': case 'команды':
                        $output = $c->commands(true);
                    break;
                    case 'auth': case 'войти':
                        if(count($lines) == 1 AND count($line[0]) >= 3){
                            $space = false;
                            for($i = 1; $i < count($line[0]); $i++) if($line[0][$i] == '|') $space = $i;

                            if($space === false){
                                $userName = $line[0][1];
                                $password = $line[0][2];
                            } else {
                                $userName = "";
                                for($i = 1; $i < $space; $i++) $userName .= $line[0][$i].' ';
                                $userName = trim($userName);

                                $password = "";
                                for($i = $space + 1; $i < count($line[0]); $i++) $password .= $line[0][$i].' ';
                                $password = trim($password);
                            }

                            $output = $c->auth(true, ['userID' => $userID, 'userName' => $userName, 'password' => $password]);
                        } else $output = $c->auth(false, null);
                    break;
                    case 'unauth': case 'выйти':
                        $output = $c->unauth($userID);
                    break;
                    case 'change': case 'изменить':
                        if(count($lines) == 1 AND count($line[0]) >= 3){
                            $type = $line[0][1];
                            $string = "";
                            for($i = 2; $i < count($line[0]); $i++) $string .= $line[0][$i].' ';
                            $string = trim($string);

                            $output = $c->change($type, ['userID' => $userID, 'string' => $string]);
                        } else $output = $c->change(false, null);
                    break;
                    case 'account': case 'аккаунт':
                        if($checkAuth){
                            if(count($lines) == 1 AND count($line[0]) >= 1){
                                if(count($line[0]) >= 2){
                                    $userName = "";
                                    for($i = 1; $i < count($line[0]); $i++) $userName .= $line[0][$i].' ';
                                    $userName = trim($userName);
        
                                    $output = $c->account(true, $userName);
                                } else {
                                    include 'database.php';

                                    $query = $db->prepare("SELECT userName FROM accounts WHERE vkID = :userID");
                                    $query->execute([':userID' => $userID]);
                                    $userName = $query->fetchColumn();

                                    $output = $c->account(true, $userName);
                                }
                            } else $output = $c->account(false, null);
                        } else {
                            if(count($lines) == 1 AND count($line[0]) >= 2){
                                $userName = "";
                                for($i = 1; $i < count($line[0]); $i++) $userName .= $line[0][$i].' ';
                                $userName = trim($userName);
    
                                $output = $c->account(true, $userName);
                            } else $output = $c->account(false, null);
                        }
                    break;
                    case 'level': case 'уровень':
                        if(count($lines) == 1 AND count($line[0]) >= 2){
                            $string = "";
                            for($i = 1; $i < count($line[0]); $i++) $string .= $line[0][$i].' ';
                            $string = trim($string);

                            $output = $c->level(true, $string);
                        } else $output = $c->level(false, null);
                    break;
                    case 'music': case 'музыка':
                        if(count($lines) == 2 AND count($line[0]) == 2 AND count($line[1]) >= 2){
                            $url = $line[0][1];

                            $space = false;
                            for($i = 0; $i < count($line[1]); $i++) if($line[1][$i] == '|') $space = $i;

                            if($space === false){
                                $name = $line[1][0];
                                $author = $line[1][1];
                            } else {
                                $name = "";
                                for($i = 0; $i < $space; $i++) $name .= $line[1][$i].' ';
                                $name = trim($name);

                                $author = "";
                                for($i = $space + 1; $i < count($line[1]); $i++) $author.= $line[1][$i].' ';
                                $author = trim($author);
                            }

                            $output = $c->music(true, ['userID' => $userID, 'url' => $url, 'name' => $name, 'author' => $author]);
                        } else $output = $c->music(false, null);
                    break;
                }

                if($output !== false){
                    $this->sendMessage($chatID, $output);
                }
            }
        }

        public function checkAllCommand($data){
            $chatID = $data['chatID'];
            $userID = $data['userID'];
            if(!empty($data['replyID'])) $replyID = $data['replyID'];

            $checkAuth = $this->checkAuth($userID);

            $output = $this->checkPrefix(false, $data['message']);
            if($output['prefix']){
                $message = $output['message']; $output = false;

                $lines = explode("\n", $message);
                for($i = 0; $i < count($lines); $i++){
                    $line[$i] = explode(" ", $lines[$i]);
                }

                $line[0][0] = mb_strtolower($line[0][0]);

                require_once 'commands.php';
                $c = new Commands();

                switch($line[0][0]){
                    case 'help': case 'помощь':
                        $output = $c->help();
                    break;
                    case 'commands': case 'команды':
                        $output = $c->commands(false);
                    break;
                    case 'account': case 'аккаунт':
                        if($checkAuth){
                            if(count($lines) == 1 AND count($line[0]) >= 1){
                                if(count($lines) == 1 AND count($line[0]) >= 2){
                                    $userName = "";
                                    for($i = 1; $i < count($line[0]); $i++) $userName .= $line[0][$i].' ';
                                    $userName = trim($userName);

                                    $output = $c->account(true, $userName);
                                } else {
                                    if(count($lines) == 1 AND !empty($replyID)){
                                        include 'database.php';

                                        $query = $db->prepare("SELECT userName FROM accounts WHERE vkID = :userID");
                                        $query->execute([':userID' => $replyID]);
                                        if($query->rowCount() > 0){
                                            $userName = $query->fetchColumn();

                                            $output = $c->account(true, $userName);
                                        } else $output = "&#10071; Этот пользователь не привязал аккаунт";
                                    } else {
                                        include 'database.php';

                                        $query = $db->prepare("SELECT userName FROM accounts WHERE vkID = :userID");
                                        $query->execute([':userID' => $userID]);
                                        $userName = $query->fetchColumn();

                                        $output = $c->account(true, $userName);
                                    }
                                }
                            } else $output = $c->account(false, null);
                        } else {
                            if((count($lines) == 1 AND count($line[0]) >= 2) OR (count($lines) == 1 AND !empty($replyID))){
                                if(!empty($replyID)){
                                    include 'database.php';

                                    $query = $db->prepare("SELECT userName FROM accounts WHERE vkID = :userID");
                                    $query->execute([':userID' => $replyID]);
                                    if($query->rowCount() > 0){
                                        $userName = $query->fetchColumn();

                                        $output = $c->account(true, $userName);
                                    } else $output = "&#10071; Этот пользователь не привязал аккаунт";
                                } else {
                                    $userName = "";
                                    for($i = 1; $i < count($line[0]); $i++) $userName .= $line[0][$i].' ';
                                    $userName = trim($userName);

                                    $output = $c->account(true, $userName);
                                }
                            } else $output = $c->account(false, null);
                        }
                    break;
                    case 'level': case 'уровень':
                        if(count($lines) == 1 AND count($line[0]) >= 2){
                            $string = "";
                            for($i = 1; $i < count($line[0]); $i++) $string .= $line[0][$i].' ';
                            $string = trim($string);

                            $output = $c->level(true, $string);
                        } else $output = $c->level(false, null);
                    break;
                    case 'music': case 'музыка':
                        if(count($lines) == 2 AND count($line[0]) == 2 AND count($line[1]) >= 2){
                            $url = $line[0][1];

                            $space = false;
                            for($i = 0; $i < count($line[1]); $i++) if($line[1][$i] == '|') $space = $i;

                            if($space === false){
                                $name = $line[1][0];
                                $author = $line[1][1];
                            } else {
                                $name = "";
                                for($i = 0; $i < $space; $i++) $name .= $line[1][$i].' ';
                                $name = trim($name);

                                $author = "";
                                for($i = $space + 1; $i < count($line[1]); $i++) $author.= $line[1][$i].' ';
                                $author = trim($author);
                            }

                            $output = $c->music(true, ['userID' => $userID, 'url' => $url, 'name' => $name, 'author' => $author]);
                        } else $output = $c->music(false, null);
                    break;
                }

                if($output !== false){
                    $this->sendMessage($chatID, $output);
                }
            }
        }

        public function checkAuth($userID){
            include 'database.php';

            $query = $db->prepare("SELECT userName, password FROM accounts WHERE vkID = :userID");
            $query->execute([':userID' => $userID]);
            if($query->rowCount() > 0){
                $query = $query->fetch();
                $userName = $query['userName'];
                $password = $query['password'];

                $output = $this->api('user.accountID', ['userName' => $userName]);
                if(empty($output->error) AND !empty($output->accountID)){
                    $accountID = $output->accountID;

                    $output = $this->api('user.info', ['accountID' => $accountID, 'password' => $password]);
                    if(empty($output->error) AND !empty($output->accountID)){
                        return true;
                    } else {
                        $query = $db->prepare("DELETE FROM accounts WHERE vkID = :userID");
                        $query->execute([':userID' => $userID]);
                        
                        $this->sendMessage($userID, "&#10071; Данные для привязаного аккаунта устарели\n\n&#10067; В связи с этим ваш аккаунт был отвязан");
                        return false;
                    }
                } else {
                    $query = $db->prepare("DELETE FROM accounts WHERE vkID = :userID");
                    $query->execute([':userID' => $userID]);
                    
                    $this->sendMessage($userID, "&#10071; Привязаного аккаунта не существует\n\n&#10067; В связи с этим ваш аккаунт был отвязан");
                    return false;
                }
            } else {
                return false;
            }
        }
    }
?>