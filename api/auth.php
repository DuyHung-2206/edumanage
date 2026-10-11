<?php require_once __DIR__.'/../config/config.php';
$action=$_GET['action']??$_POST['action']??'';

if($action==='register'&&$_SERVER['REQUEST_METHOD']==='POST'){
 $name=trim($_POST['name']??'');
 $email=trim($_POST['email']??'');
 $pass=$_POST['password']??'';
 $role=$_POST['role']??'';
 $code=strtoupper(trim($_POST['connection_code']??''));

 if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($pass)<6||!in_array($role,['teacher','parent','student'],true)){
  json_response(['success'=>false,'message'=>'Vui lòng nhập đầy đủ thông tin hợp lệ (mật khẩu tối thiểu 6 ký tự).'],422);
 }

 $st=db()->prepare('SELECT id FROM users WHERE email=?');
 $st->execute([$email]);
 if($st->fetch())json_response(['success'=>false,'message'=>'Email đã tồn tại trong hệ thống.'],409);

 // Phụ huynh và học sinh bắt buộc cần mã kết nối học sinh do giáo viên cấp
 if(in_array($role,['parent','student'],true)&&$code===''){
  json_response(['success'=>false,'message'=>'Tài khoản phụ huynh/học sinh cần Mã kết nối học sinh do giáo viên cung cấp.'],422);
 }

 db()->beginTransaction();
 try {
  // Giáo viên được tự sinh mã GV-XXXXXXXX ngay khi đăng ký, không cần thêm học sinh
  $teacher_code = null;
  if($role==='teacher'){
   $teacher_code = teacher_code();
  }

  $st=db()->prepare('INSERT INTO users(name,email,password_hash,role,teacher_code) VALUES(?,?,?,?,?)');
  $st->execute([$name,$email,password_hash($pass,PASSWORD_DEFAULT),$role,$teacher_code]);
  $uid=(int)db()->lastInsertId();

  if($role==='teacher'){
   db()->prepare('INSERT INTO payment_settings(teacher_id) VALUES(?)')->execute([$uid]);
  } else {
   $st=db()->prepare('SELECT id FROM students WHERE connection_code=?');
   $st->execute([$code]);
   $student=$st->fetch();
   if(!$student)throw new RuntimeException('Mã kết nối học sinh không hợp lệ.');

   if($role==='parent'){
    db()->prepare('INSERT INTO student_parents(student_id,parent_id) VALUES(?,?)')->execute([(int)$student['id'],$uid]);
   } else {
    $chk=db()->prepare('SELECT user_id FROM student_accounts WHERE student_id=?');
    $chk->execute([(int)$student['id']]);
    if($chk->fetch())throw new RuntimeException('Học sinh này đã có tài khoản liên kết.');
    db()->prepare('INSERT INTO student_accounts(student_id,user_id) VALUES(?,?)')->execute([(int)$student['id'],$uid]);
   }
  }

  db()->commit();
  session_regenerate_id(true);
  $_SESSION['user_id']=$uid;
  $_SESSION['user_name']=$name;
  $_SESSION['role']=$role;
  $_SESSION['teacher_code']=$teacher_code;
  $_SESSION['user_logged_in']=[
    'id'=>$uid,
    'user_id'=>$uid,
    'username'=>$name,
    'full_name'=>$name,
    'role'=>$role,
    'teacher_code'=>$teacher_code
  ];

  json_response(['success'=>true,'role'=>$role,'teacher_code'=>$teacher_code]);
 } catch(Throwable $e){
  db()->rollBack();
  json_response(['success'=>false,'message'=>$e->getMessage()],422);
 }
}

if($action==='login'&&$_SERVER['REQUEST_METHOD']==='POST'){
 $email=trim($_POST['email']??'');
 $pass=$_POST['password']??'';
 $st=db()->prepare('SELECT * FROM users WHERE email=?');
 $st->execute([$email]);
 $u=$st->fetch();

 if(!$u||!password_verify($pass,$u['password_hash'])){
  json_response(['success'=>false,'message'=>'Email hoặc mật khẩu không chính xác.'],401);
 }

 // Đảm bảo giáo viên cũ luôn có mã teacher_code
 $teacher_code = $u['teacher_code'] ?? null;
 if($u['role']==='teacher' && empty($teacher_code)){
  $teacher_code = teacher_code();
  db()->prepare('UPDATE users SET teacher_code=? WHERE id=?')->execute([$teacher_code, $u['id']]);
 }

 session_regenerate_id(true);
 $_SESSION['user_id']=(int)$u['id'];
 $_SESSION['user_name']=$u['name'];
 $_SESSION['role']=$u['role'];
 $_SESSION['teacher_code']=$teacher_code;
 $_SESSION['user_logged_in']=[
  'id'=>(int)$u['id'],
  'user_id'=>(int)$u['id'],
  'username'=>$u['name'],
  'full_name'=>$u['name'],
  'role'=>$u['role'],
  'teacher_code'=>$teacher_code
 ];

 json_response(['success'=>true,'role'=>$u['role'],'teacher_code'=>$teacher_code]);
}

if($action==='logout'){
 session_unset();
 session_destroy();
 header('Location: ../login.php');
 exit;
}

if($action==='me'){
 json_response([
  'success'=>true,
  'logged_in'=>!empty($_SESSION['user_id']),
  'name'=>$_SESSION['user_name']??'',
  'role'=>$_SESSION['role']??'',
  'teacher_code'=>$_SESSION['teacher_code']??''
 ]);
}

json_response(['success'=>false,'message'=>'Action không hợp lệ'],400);
