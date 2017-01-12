<?php
/* Admin And Token */
$token = 'محل توکن';
$admin = 'آیدی عددی ادمین';
$admin_username = 'یوزرنیم ادمین با @';
$admin_number = 'شماره ادمین با 98';
$admin_name = 'نام ادمین';
/*================*/
define('API_KEY',$token);
function norbert($method,$datas=[]){
    $updaterl = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$updaterl);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
$update = json_decode(file_get_contents("php://input"));
$chat_id = $update->message->chat->id;
$from_id = $update->message->from->id;
$message_id = $update->message->message_id;
$text = $update->message->text;
$contact = $update->message->contact;
$contact_number = $contact->phone_number;
$contact_first = $contact->first_name;
if(preg_match('/^\/([Ss]tart)/s',$text)){
norbert('sendmessage',[
'chat_id'=>$chat_id,
'text'=>"سلام",
'parse_mode'=>'html'
]);
}
elseif(preg_match('/^\/([Ss]tats)/',$text) and $from_id == $admin){
    $user = file_get_contents('Member.txt');
    $member_id = explode("\n",$user);
    $member_count = count($member_id) -1;
    norbert('sendMessage',[
      'chat_id'=>$chat_id,
      'text'=>"تعداد کل اعضا: $member_count",
      'parse_mode'=>'HTML'
    ]);
}
elseif(preg_match('/^\/([Cc]ontact)/',$text) and $from_id == $admin){
    $tel = file_get_contents('Phone.txt');
    $tel_number = explode("\n",$tel);
    $tel_count = count($tel_number) -1;
    norbert('sendMessage',[
      'chat_id'=>$chat_id,
      'text'=>"تعداد شماره های سیو شده: $tel_count",
      'parse_mode'=>'HTML'
    ]);
}
elseif($contact!=null and $from_id != $admin){
	$phone = file_get_contents('Phone.txt');
    $phone_number = explode("\n",$phone);
    if (!in_array($contact_number,$phone_number)){
      $add_number = file_get_contents('Phone.txt');
      $add_number .= $contact_number."\n";
     file_put_contents('Phone.txt',$add_number);
    }
	norbert('sendContact',[
      'chat_id'=>$chat_id,
      'phone_number'=>$admin_number,
	  'first_name'=>$admin_name,
      'reply_to_message_id'=>$message_id
    ]);
	norbert('sendMessage',[
      'chat_id'=>$chat_id,
      'text'=>"شمارت سیو شد تو هم سیو کن بیا پی وی
	  $admin_username",
      'parse_mode'=>'HTML',
	  'reply_to_message_id'=>$message_id
    ]);
	norbert('sendContact',[
      'chat_id'=>$admin,
      'phone_number'=>$contact_number,
	  'first_name'=>$contact_first
    ]);
}
$user = file_get_contents('Member.txt');
    $members = explode("\n",$user);
    if (!in_array($chat_id,$members)){
      $add_user = file_get_contents('Member.txt');
      $add_user .= $chat_id."\n";
     file_put_contents('Member.txt',$add_user);
    }
?>
