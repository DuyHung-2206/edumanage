<?php require_once __DIR__.'/../config/config.php';
$u=require_login();$student=(int)($_GET['student_id']??0);$month=$_GET['month']??date('Y-m');if(!$student||!preg_match('/^\d{4}-\d{2}$/',$month))json_response(['success'=>false,'message'=>'Thiếu thông tin.'],422);if(!can_view_student($student,$u))json_response(['success'=>false,'message'=>'Không có quyền xem học sinh này.'],403);
$st=db()->prepare('SELECT s.*,t.name teacher_name,t.email teacher_email FROM students s JOIN users t ON t.id=s.teacher_id WHERE s.id=?');$st->execute([$student]);$s=$st->fetch();$from=$month.'-01';$to=date('Y-m-t',strtotime($from));
$st=db()->prepare('SELECT * FROM lessons WHERE student_id=? AND lesson_date BETWEEN ? AND ? ORDER BY lesson_date,start_time,id');$st->execute([$student,$from,$to]);$lessons=$st->fetchAll();
$st=db()->prepare('SELECT * FROM grades WHERE student_id=? AND test_date BETWEEN ? AND ? ORDER BY test_date,id');$st->execute([$student,$from,$to]);$grades=$st->fetchAll();
$st=db()->prepare('SELECT * FROM feedbacks WHERE student_id=? AND month_key=?');$st->execute([$student,$month]);$feedback=$st->fetch()?:null;
$st=db()->prepare('SELECT p.* FROM payment_settings p JOIN students s ON s.teacher_id=p.teacher_id WHERE s.id=?');$st->execute([$student]);$payment=$st->fetch()?:null;
json_response(['success'=>true,'student'=>$s,'lessons'=>$lessons,'grades'=>$grades,'feedback'=>$feedback,'payment'=>$payment,'month'=>$month,'role'=>$u['role']]);
