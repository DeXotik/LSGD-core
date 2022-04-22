<?php
    class Commands{
        public function help(){
            return "&#10071;&#65039; Для того, что бы узнать список команд, введите \":commands\" без кавычек. Для некоторых команд нужно авторизоваться.\n\n&#10071; Для того, что бы авторизоваться, выйти, изменить настройки профиля вам нужно написать в ЛС группы.\n\n&#10071; Для того, что бы узнать, как правильно вводить команду, то есть узнать синтаксис, введите её.\n\n&#10071; У бота за основной префикс взят \":\", что бы он не конфликтовал с другими ботами. Если вы взаимодействуете с ботом только в ЛС, то можно использовать префиксы \"!\", \"/\" и \":\", или вообще обращаться к нему без префикса.\n\n&#10067; ЛС - личные сообщения";
        }

        public function commands($type){
            if($type){
                return "&#128196; Список команд в ЛС:\n  • auth - вход в аккаунт\n  • unauth - выход из аккаунта\n  • change - изменить настройки аккаунта\n\n&#128196; Общий список команд:\n  • commands - список команд\n  • help - помощь по боту\n  • account - информация о аккаунте\n  • level - информация о уровне\n  • music - загрузка музыки\n\n&#10067; Префиксы \"!\", \"/\" и \":\" без кавычек";
            } else {
                return "&#128196; Общий список команд:\n  • commands - список команд\n  • help - помощь по боту\n  • account - информация о аккаунте\n  • level - информация о уровне\n  • music - загрузка музыки\n\n&#10067; Префикс \":\" без кавычек";
            }
        }

        public function auth($type, $data){
            if($type){
                include 'database.php';
                require_once 'functions.php';
                $f = new Functions();

                $userID = $data['userID'];
                $userName = $data['userName'];
                $password = $data['password'];

                $query = $db->prepare("SELECT count(*) FROM accounts WHERE userName = :userName");
                $query->execute([':userName' => $userName]);
                if($query->fetchColumn() > 0){
                    return "&#10071; Этот аккаунт уже занят";
                } else {
                    $output = $f->api('user.accountID', ['userName' => $userName]);
                    if(empty($output->error) AND !empty($output->accountID)){
                        $accountID = $output->accountID;

                        $output = $f->api('user.info', ['accountID' => $accountID, 'password' => $password]);
                        if(empty($output->error) AND !empty($output->accountID)){
                            $query = $db->prepare("SELECT count(*) FROM accounts WHERE vkID = :userID");
                            $query->execute([':userID' => $userID]);
                            if($query->fetchColumn() > 0){
                                return "&#10071; Вы уже привязали аккаунт";
                            } else {
                                $query = $db->prepare("INSERT INTO accounts (vkID, userName, password) VALUES (:userID, :userName, :password)");
                                $query->execute([':userID' => $userID, ':userName' => $userName, ':password' => $password]);

                                return "&#9989; Аккаунт успешно првязан";
                            }
                        } else return "&#10060; Ошибка: {$output->errorText}";
                    } else return "&#10071; Такого аккаунта не существует";
                }
            } else return "Синтаксис команды:\n:auth ‹имя профиля› ‹пароль›\n\nЕсли у вас пробел в имени профиля или пароле, то синтаксис команды:\n:auth ‹имя профиля› | ‹пароль›\n\nПример использования:\n:auth Admin 123456";
        }

        public function unauth($userID){
            include 'database.php';

            $query = $db->prepare("SELECT count(*) FROM accounts WHERE vkID = :userID");
            $query->execute([':userID' => $userID]);
            if($query->fetchColumn() > 0){
                $query = $db->prepare("DELETE FROM accounts WHERE vkID = :userID");
                $query->execute([':userID' => $userID]);

                return "&#9989; Аккаунт успешно отвязан";
            } else return false;
        }

        public function change($type, $data){
            if($type){
                require_once 'functions.php';
                $f = new Functions();

                $userID = $data['userID'];
                $string = $data['string'];

                $query = $db->prepare("SELECT userName, password FROM accounts WHERE vkID = :userID");
                $query->execute([':userID' => $userID]);
                if($query->rowCount() > 0){
                    $query = $query->fetch();

                    $userName = $query['userName'];
                    $password = $query['password'];

                    $output = $f->api('user.accountID', ['userName' => $userName]);
                    if(empty($output->error) AND !empty($output->accountID)){
                        $accountID = $output->accountID;

                        switch($type){
                            case 'email':
                                $output = $f->api('change.email', ['newEmail' => $string, 'accountID' => $accountID, 'password' => $password]);
                                $string = "&#9989; Почта успешно изменена";
                                if($output->error == 5){
                                    $errString = "&#10071; Вы уже изменяли почту в последнее время";
                                } elseif(!empty($output->error)) $errString = "&#10060; Ошибка: {$output->errorText}";
                            break;
                            case 'name':
                                $output = $f->api('change.userName', ['newUserName' => $string, 'accountID' => $accountID, 'password' => $password]);
                                $string = "&#9989; Имя профиля успешно изменено";
                                if($output->error == 5){
                                    $errString = "&#10071; Вы уже изменяли имя профиля в последнее время";
                                } elseif(!empty($output->error)) $errString = "&#10060; Ошибка: {$output->errorText}";
                            break;
                            case 'password':
                                $output = $f->api('change.password', ['newPassword' => $string, 'accountID' => $accountID, 'password' => $password]);
                                $string = "&#9989; Пароль успешно изменён";
                                if($output->error == 5){
                                    $errString = "&#10071; Вы уже изменяли пароль в последнее время";
                                } elseif(!empty($output->error)) $errString = "&#10060; Ошибка: {$output->errorText}";
                            break;
                            default: $type = false;
                        }

                        if($type){
                            if(empty($output->error) AND !empty($output->type)){
                                return $string;
                            } elseif(!empty($output->error)){
                                return $errString;
                            }
                        }
                    } else {
                        $query = $db->prepare("DELETE FROM accounts WHERE vkID = :userID");
                        $query->execute([':userID' => $userID]);
                        
                        return "&#10071; Привязаного аккаунта не существует\n\n&#10067; В связи с этим ваш аккаунт был отвязан";
                    }
                } else return "&#10071; Для начала привяжите аккаунт";
            }
            
            return "Синтаксис команды:\n:change ‹email, name или password› ‹новое значение›\n\nПример использования:\n:change name DeXotik";
        }

        public function account($type, $userName){
            if($type){
                require_once 'functions.php';
                $f = new Functions();

                $output = $f->api('user.accountID', ['userName' => $userName]);
                if(empty($output->error) AND !empty($output->accountID)){
                    $accountID = $output->accountID;

                    $output = $f->api('user.stats', ['accountID' => $accountID]);
                    if(empty($output->error) AND !empty($output->userName)){
                        $string = "&#128100; {$output->userName}";
                        if(!empty($output->role)) $string .= "\n&#128081; Роль: {$output->role}";
                        $string .= "\n\n&#128172; Комментарии: {$output->comments}\n&#11088; Звёзды: {$output->stars}";
                        if($output->creatorPoints > 0) $string .= "\n&#128736; Очки строительства: {$output->creatorPoints}";
                        $string .= "\n\n&#127381; {$output->registerString}\n&#128344; {$output->lastOnlineString}";

                        return $string;
                    } elseif($output->error == 3){
                        return "&#10071; Такого пользователя не существует";
                    } else return "&#10060; Ошибка: {$output->errorText}";
                } else return "&#10071; Такого аккаунта не существует";
            } else return "Синтаксис команды:\n:account ‹имя профиля›\n\nПример использования: \n:account DeXotik";
        }

        public function level($type, $string){
            if($type){
                require_once 'functions.php';
                $f = new Functions();
                
                if(is_numeric($string)){
                    $output = $f->api('level.info', ['levelID' => $string]);
                } else {
                    switch($string){
                        case 'daily':
                            $output = $f->api('level.daily', []);
                        break;
                        case 'weekly':
                            $output = $f->api('level.weekly', []);
                        break;
                        default: $type = false;
                    }
                }

                if($type){
                    if(empty($output->error) AND !empty($output->levelID)){
                        $string = "&#9654; Уровень: {$output->name}\n&#128100; Автор: {$output->author}\n&#127380; ID: {$output->levelID}\n&#128545; Сложнность: {$output->difficulty}";
                        if($output->stars > 0) $string .= "\n&#11088; Кол-во звёзд: {$output->stars}";
                        if($output->featured) $string .= "\n*&#65039;&#8419; Дополнительно: Featured";
                        if($output->epic) $string .= ", Epic";
                        $string .= "\n\n&#128344; Длина уровня: {$output->length}\n&#128077; Лайки: {$output->likes}\n&#11015; Скачивания: {$output->downloads}\n\n&#127381; {$output->uploadDateString}";
                        if($output->updateDateString > $output->uploadDateString) $string .= "\n&#128260; {$output->updateDateString}";
                        if($output->rateDateString > $output->uploadDateString) $string .= "\n&#10024; {$output->rateDateString}";

                        return $string;
                    } elseif($output->error == 3){
                        return "&#10071; Такого уровня не существует";
                    } else return "&#10060; Ошибка: {$output->errorText}";
                }
            }
            
            return "Синтаксис команды:\n:level ‹ID уровня, daily или weekly›\n\nПример использования: \n:level 8";
        }

        public function music($type, $data){
            if($type){
                include 'database.php';
                require_once 'functions.php';
                $f = new Functions();

                $userID = $data['userID'];
                $url = $data['url'];
                $name = $data['name'];
                $author = $data['author'];

                $query = $db->prepare("SELECT userName, password FROM accounts WHERE vkID = :userID");
                $query->execute([':userID' => $userID]);
                if($query->rowCount() > 0){
                    $query = $query->fetch();

                    $userName = $query['userName'];
                    $password = $query['password'];

                    $output = $f->api('user.accountID', ['userName' => $userName]);
                    if(empty($output->error) AND !empty($output->accountID)){
                        $accountID = $output->accountID;

                        $output = $f->api('load.music', ['url' => $url, 'name' => $name, 'author' => $author, 'accountID' => $accountID, 'password' => $password]);

                        if(empty($output->error) AND !empty($output->music)){
                            return "&#9989; Музыка успешно загружена\n&#127380; ID музки: {$output->music}";
                        } elseif($output->error == 4){
                            return "&#10071; Неверный URL для загрузки";
                        } elseif($output->error == 5){
                            return "&#10071; Эта музыка недоступна для скачивания";
                        } elseif($output->error == 6){
                            return "&#10071; Эта музыка уже загружена\n&#127380; ID музки: {$output->music}";
                        } elseif($output->error == 7){
                            return "&#10071; Вы превысили лимит загрузки музыки за сутки";
                        } else return "&#10060; Ошибка: {$output->errorText}";
                    } else {
                        $query = $db->prepare("DELETE FROM accounts WHERE vkID = :userID");
                        $query->execute([':userID' => $userID]);
                            
                        return "&#10071; Привязаного аккаунта не существует\n\n&#10067; В связи с этим ваш аккаунт был отвязан";
                    }
                } else return "&#10071; Для начала привяжите аккаунт";
            } else return "Синтаксис команды:\n:music ‹URL›\n‹название музыки› ‹автор›\n\nЕсли у вас пробел в названии музыки или авторе, то синтаксис команды:\n:music ‹URL›\n‹название музыки› | ‹автор›\n\nПример использования:\n:music http://127.0.0.1/song.mp3\nTest song | Admin";
        }
    }
?>