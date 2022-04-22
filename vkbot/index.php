<?php
    require_once 'include/config.php';
    require_once 'include/functions.php';
    $f = new Functions();

    $data = json_decode(file_get_contents('php://input'));

    if($data->secret === $secretKey AND $data->group_id === $groupID){
        if($data->type === 'confirmation'){
            exit($checkCode);
        } elseif($data->type === 'message_new'){
            $input['chatID'] = $data->object->message->peer_id;
            $input['userID'] = $data->object->message->from_id;
            if(!empty($data->object->message->reply_message->from_id)) $input['replyID'] = $data->object->message->reply_message->from_id;

            $input['message'] = $data->object->message->text;

            if($data->object->message->payload === '{"command":"start"}'){
                $input['message'] = ':commands';
            }

            if($input['chatID'] === $input['userID']){
                $f->checkOwnCommand($input);
            } else {
                $f->checkAllCommand($input);
            }

            exit('ok');
        } else exit('ok');
    } else exit('ok');
?>