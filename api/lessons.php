<?php require_once __DIR__.'/../config/config.php';
$u=require_role(['teacher']);
$uid=$u['id'];
$action=$_GET['action']??$_POST['action']??'';
$method=$_SERVER['REQUEST_METHOD'];

function own_lesson(int $id,int $uid):array{
  $st=db()->prepare('SELECT l.* FROM lessons l JOIN students s ON s.id=l.student_id WHERE l.id=? AND s.teacher_id=?');
  $st->execute([$id,$uid]);
  $r=$st->fetch();
  if(!$r)json_response(['success'=>false,'message'=>'Buổi học không tồn tại.'],404);
  return $r;
}

if($action==='save'&&$method==='POST'){
  $id=(int)($_POST['id']??0);
  $sid=(int)($_POST['student_id']??0);
  own_student($sid,$uid);

  $date=$_POST['lesson_date']??'';
  $start=$_POST['start_time']??null;
  $end=$_POST['end_time']??null;
  $duration=(float)($_POST['duration']??0);
  $price=(float)($_POST['price']??0);
  $subject=trim($_POST['subject']??'');
  $hw=(int)($_POST['homework_percent']??100);
  $att=$_POST['attitude']??'';
  $comment=trim($_POST['comment']??'');

  if(!$date || $duration<0 || $price<0 || $subject==='' || mb_strlen($subject)>100 || !in_array($att,['Tốt','Mất tập trung','Lười học','Nói chuyện riêng'],true) || $hw<0 || $hw>100){
    json_response(['success'=>false,'message'=>'Dữ liệu buổi học không hợp lệ.'],422);
  }

  if($id){
    own_lesson($id,$uid);
    $st=db()->prepare('UPDATE lessons SET lesson_date=?,start_time=?,end_time=?,duration=?,price=?,subject=?,homework_percent=?,attitude=?,comment=? WHERE id=?');
    $st->execute([$date,$start?:null,$end?:null,$duration,$price,$subject,$hw,$att,$comment,$id]);
  } else {
    $st=db()->prepare('INSERT INTO lessons(student_id,lesson_date,start_time,end_time,duration,price,subject,homework_percent,attitude,comment) VALUES(?,?,?,?,?,?,?,?,?,?)');
    $st->execute([$sid,$date,$start?:null,$end?:null,$duration,$price,$subject,$hw,$att,$comment]);
    $id=(int)db()->lastInsertId();
  }
  json_response(['success'=>true,'id'=>$id]);
}

if($action==='delete'&&$method==='POST'){
  $id=(int)($_POST['id']??0);
  own_lesson($id,$uid);
  db()->prepare('DELETE FROM lessons WHERE id=?')->execute([$id]);
  json_response(['success'=>true]);
}

json_response(['success'=>false,'message'=>'Action không hợp lệ'],400);
