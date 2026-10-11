let students = [], selectedId = null, data = null;
const $ = id => document.getElementById(id);
$('month').value = new Date().toISOString().slice(0, 7);
const teacher = USER_ROLE === 'teacher';

// Danh sách các môn học theo Chương trình Giáo dục Tiểu học
const PRIMARY_SUBJECTS = [
  'Toán',
  'Tiếng Việt',
  'Tiếng Anh',
  'Tự nhiên và Xã hội',
  'Khoa học',
  'Lịch sử và Địa lí',
  'Tin học và Công nghệ',
  'Đạo đức',
  'Hoạt động trải nghiệm',
  'Luyện chữ đẹp',
  'Ôn tập tổng hợp',
  'Môn khác'
];

async function api(url, options = {}) {
  const r = await fetch(url, options);
  let d = {};
  try { d = await r.json(); } catch (e) {}
  if (!r.ok || !d.success) throw Error(d.message || 'Có lỗi xảy ra');
  return d;
}

async function loadStudents() {
  try {
    const d = teacher ? await api('api/students.php?action=list') : await api('api/linked.php');
    students = d.students || [];
    renderStudents();
    if (selectedId && students.some(s => +s.id === +selectedId)) {
      await loadData();
    } else if (students[0]) {
      selectedId = +students[0].id;
      await loadData();
    } else {
      showEmpty();
    }
  } catch (e) {
    alert(e.message);
  }
}

function showEmpty() {
  $('studentEmpty').classList.remove('hidden');
  $('dashboard').classList.add('hidden');
  if ($('topStudentCodeVal')) $('topStudentCodeVal').textContent = '—';
  if ($('topStudentTag')) $('topStudentTag').textContent = 'Chưa chọn';
  if ($('studentConnectionCodeVal')) $('studentConnectionCodeVal').textContent = '—';
}

function renderStudents() {
  $('students').innerHTML = students.length
    ? students.map(s => `
      <button onclick="selectStudent(${s.id})" class="w-full text-left p-3.5 rounded-xl border transition-all ${+selectedId === +s.id ? 'border-blue-300 bg-blue-50/70 shadow-sm ring-1 ring-blue-300' : 'border-slate-100 hover:bg-slate-50'}">
        <div class="flex justify-between items-start">
          <b class="text-slate-800 text-sm font-bold">${esc(s.name)}</b>
          <span class="text-[11px] font-semibold text-blue-600 bg-blue-100/60 px-2 py-0.5 rounded-md">${s.lesson_count ?? 0} buổi</span>
        </div>
        <div class="flex justify-between items-center mt-2">
          <span class="text-xs text-slate-400">${esc(s.class_name || 'Chưa phân lớp')}</span>
          <span class="text-[10px] font-mono font-bold text-blue-900 bg-white border border-blue-200 px-1.5 py-0.5 rounded shadow-2xs" title="Mã liên kết học sinh">
            Mã: ${esc(s.connection_code)}
          </span>
        </div>
      </button>
    `).join('')
    : '<p class="text-sm text-slate-400 text-center py-6">Chưa có hồ sơ học sinh nào.</p>';
}

async function selectStudent(id) {
  selectedId = +id;
  renderStudents();
  await loadData();
}

async function loadData() {
  if (!selectedId) return;
  try {
    data = await api(`api/data.php?student_id=${selectedId}&month=${$('month').value}`);
    $('studentEmpty').classList.add('hidden');
    $('dashboard').classList.remove('hidden');
    renderAll();
  } catch (e) {
    alert(e.message);
  }
}

function renderAll() {
  const s = data.student, p = data.payment || {}, l = data.lessons || [], g = data.grades || [];
  $('sname').textContent = s.name;
  $('sclass').textContent = s.class_name || 'Chưa cập nhật lớp';
  $('teacherName').textContent = 'Giáo viên hướng dẫn: ' + (s.teacher_name || '—');
  $('avatar').textContent = s.name.split(' ').map(x => x[0]).slice(-2).join('').toUpperCase();
  
  // CẬP NHẬT MÃ LIÊN KẾT HỌC SINH ĐƯA RA NGOÀI CÙNG VỚI MÃ GIÁO VIÊN
  if ($('topStudentCodeVal')) $('topStudentCodeVal').textContent = s.connection_code || 'Chưa có';
  if ($('topStudentTag')) {
    const shortName = s.name ? s.name.trim().split(' ').slice(-1)[0] : 'Đang chọn';
    $('topStudentTag').textContent = shortName;
    $('topStudentTag').title = s.name;
  }
  if ($('studentConnectionCodeVal')) $('studentConnectionCodeVal').textContent = s.connection_code || 'Chưa có';

  $('statLessons').textContent = l.length;
  $('statHours').textContent = l.reduce((a, x) => a + Number(x.duration), 0) + 'h';
  $('statPrice').textContent = money(l.reduce((a, x) => a + Number(x.price), 0));
  
  let avg = g.length ? g.reduce((a, x) => a + Number(x.score), 0) / g.length : null;
  $('statAvg').textContent = avg === null ? '—' : avg.toFixed(1);
  $('bigAvg').textContent = avg === null ? '—' : avg.toFixed(1);
  $('gradeCount').textContent = g.length;
  
  $('totalFee').textContent = money(l.reduce((a, x) => a + Number(x.price), 0));
  $('bank').textContent = p.bank_name || 'Chưa thiết lập';
  $('account').textContent = p.account_number || '—';
  $('owner').textContent = p.account_owner || '—';
  
  if (p.qr_image) {
    $('qr').src = p.qr_image;
    $('qr').classList.remove('hidden');
  } else {
    $('qr').src = '';
    $('qr').classList.add('hidden');
  }

  renderCalendar();
  renderLessons();
  renderGrades();
  renderFeedback();
}

function renderCalendar() {
  let c = $('calendar');
  c.innerHTML = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'].map(x => `<div class="text-center text-xs font-bold text-slate-400 py-1">${x}</div>`).join('');
  let [y, m] = $('month').value.split('-').map(Number);
  let first = new Date(y, m - 1, 1);
  let start = (first.getDay() + 6) % 7;
  let days = new Date(y, m, 0).getDate();
  let studied = data.lessons.map(x => Number(x.lesson_date.slice(8, 10)));

  for (let i = 0; i < start; i++) c.innerHTML += '<div></div>';
  for (let d = 1; d <= days; d++) {
    c.innerHTML += `<div class="aspect-square rounded-xl grid place-items-center text-sm font-bold transition-all ${studied.includes(d) ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'}">${d}</div>`;
  }
}

function renderLessons() {
  $('lessonRows').innerHTML = data.lessons.length
    ? data.lessons.map((x, i) => `
      <tr>
        <td class="font-bold text-slate-400">#${i + 1}</td>
        <td class="font-semibold text-slate-800">${fmtDate(x.lesson_date)}</td>
        <td>${x.start_time?.slice(0, 5) || '—'} - ${x.end_time?.slice(0, 5) || '—'} <small class="text-slate-400 font-medium">(${x.duration}h)</small></td>
        <td class="font-semibold text-blue-700">${money(x.price)}</td>
        <td><span class="bg-blue-50 text-blue-700 border border-blue-100 px-2.5 py-1 rounded-lg text-xs font-bold">${esc(x.subject)}</span></td>
        <td>${badgeHw(x.homework_percent)}</td>
        <td>${badgeAtt(x.attitude)}</td>
        <td class="max-w-xs whitespace-normal text-slate-600 text-xs">${esc(x.comment || '—')}</td>
        ${teacher ? `
          <td>
            <div class="flex items-center gap-2">
              <button class="text-blue-600 hover:underline font-semibold" onclick='editLesson(${JSON.stringify(x)})'>Sửa</button>
              <button class="text-red-500 hover:underline font-semibold" onclick="deleteLesson(${x.id})">Xóa</button>
            </div>
          </td>
        ` : ''}
      </tr>
    `).join('')
    : `<tr><td colspan="${teacher ? 9 : 8}" class="text-center text-slate-400 py-12">Chưa có buổi học nào được ghi lại trong tháng này.</td></tr>`;
}

function renderGrades() {
  $('gradeRows').innerHTML = data.grades.length
    ? data.grades.map(x => `
      <tr>
        <td class="font-semibold text-slate-800">${fmtDate(x.test_date)}</td>
        <td class="font-bold text-slate-800">${esc(x.test_name)}</td>
        <td><b class="text-blue-600 text-lg font-black">${x.score}</b> <span class="text-slate-400 text-xs">/ 10</span></td>
        <td class="whitespace-normal max-w-md text-slate-600 text-xs">${esc(x.mistakes || '—')}</td>
        ${teacher ? `
          <td>
            <div class="flex items-center gap-2">
              <button class="text-blue-600 hover:underline font-semibold" onclick='editGrade(${JSON.stringify(x)})'>Sửa</button>
              <button class="text-red-500 hover:underline font-semibold" onclick="deleteGrade(${x.id})">Xóa</button>
            </div>
          </td>
        ` : ''}
      </tr>
    `).join('')
    : `<tr><td colspan="${teacher ? 5 : 4}" class="text-center text-slate-400 py-12">Chưa có điểm kiểm tra nào trong tháng này.</td></tr>`;
}


function renderFeedback() {
  const f = data?.feedback || {};

  let subjectFeedback = {};
  let subjectRoadmap = {};

  try {
    subjectFeedback = typeof f.subject_feedback === 'string'
      ? JSON.parse(f.subject_feedback || '{}')
      : (f.subject_feedback || {});
  } catch (e) {
    subjectFeedback = {};
  }

  try {
    subjectRoadmap = typeof f.subject_roadmap === 'string'
      ? JSON.parse(f.subject_roadmap || '{}')
      : (f.subject_roadmap || {});
  } catch (e) {
    subjectRoadmap = {};
  }

  /*
   * Lấy các môn thực tế đã học trong tháng.
   * lessons.subject là nguồn dữ liệu chính.
   */
  const subjects = [...new Set(
    (data.lessons || [])
      .map(x => String(x.subject || '').trim())
      .filter(Boolean)
  )];

  let feedbackHtml = `
    <div class="space-y-2">
      <p>
        <b>Tổng quan học tập:</b>
        ${esc(f.overview || '—')}
      </p>
  `;

  if (subjects.length) {
    subjects.forEach(subject => {
      feedbackHtml += `
        <p>
          <b>${esc(subject)}:</b>
          ${esc(subjectFeedback[subject] || 'Chưa có nhận xét.')}
        </p>
      `;
    });
  } else {
    feedbackHtml += `
      <p class="text-slate-400">
        Chưa có môn học nào trong tháng này.
      </p>
    `;
  }

  feedbackHtml += `</div>`;

  $('feedbackText').innerHTML = feedbackHtml;

  let roadmapHtml = `<div class="space-y-2">`;

  if (subjects.length) {
    subjects.forEach(subject => {
      roadmapHtml += `
        <p>
          <b>${esc(subject)}:</b>
          ${esc(subjectRoadmap[subject] || 'Chưa có mục tiêu tháng tới.')}
        </p>
      `;
    });
  } else {
    roadmapHtml += `
      <p class="text-slate-400">
        Chưa có môn học để xây dựng lộ trình.
      </p>
    `;
  }

  roadmapHtml += `</div>`;

  $('roadmapText').innerHTML = roadmapHtml;
}

function badgeHw(v) {
  let c = v <= 30 ? 'bg-red-50 text-red-700 border border-red-200' : v <= 50 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200';
  return `<span class="px-2.5 py-0.5 rounded-full text-xs font-bold ${c}">${v}%</span>`;
}

function badgeAtt(v) {
  let c = v === 'Tốt' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : v === 'Lười học' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-amber-50 text-amber-700 border border-amber-200';
  return `<span class="px-2.5 py-0.5 rounded-full text-xs font-bold ${c}">${esc(v)}</span>`;
}

// Modal Thêm / Chỉnh sửa học sinh
function openStudent(s = null) {
  const code = s?.connection_code || 'Sẽ được sinh tự động sau khi lưu';
  $('modalBox').innerHTML = `
    <form onsubmit="saveStudent(event)" class="p-6 md:p-8 space-y-4">
      <div class="flex justify-between items-center pb-3 border-b border-slate-100">
        <h3 class="font-extrabold text-xl text-slate-800">${s ? 'Chỉnh sửa' : 'Thêm'} hồ sơ học sinh</h3>
        <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
      </div>
      <input type="hidden" name="id" value="${s?.id || ''}">
      ${field('Họ và tên học sinh *', 'name', s?.name || '', true, 'text', '', '', '', 'VD: Nguyễn Minh An')}
      ${field('Lớp học tiểu học', 'class_name', s?.class_name || '', false, 'text', '', '', '', 'VD: Lớp 1A, Lớp 3B, Lớp 5...')}
      ${field('Số điện thoại phụ huynh', 'parent_phone', s?.parent_phone || '', false, 'text', '', '', '', '09xxxxxxxxx')}
      ${field('Ghi chú học tập', 'note', s?.note || '', false, 'text', '', '', '', 'Đặc điểm học sinh, mục tiêu cần rèn luyện...')}

      <!-- Phân biệt rõ: Mã kết nối học sinh -->
      <div class="bg-blue-50/80 border border-blue-200 rounded-2xl p-4 text-sm mt-4">
        <div class="flex justify-between items-center">
          <div>
            <span class="block text-[11px] font-bold uppercase tracking-wider text-blue-700">Mã liên kết học sinh này</span>
            <b class="font-mono text-base text-blue-900 font-extrabold" id="studentCodeDisplay">${code}</b>
          </div>
          ${s?.connection_code ? `
            <div class="flex items-center gap-2">
              <button type="button" onclick="copyStudentCode('${s.connection_code}')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm">Sao chép</button>
              <button type="button" onclick="regenerateCode()" class="border border-blue-300 text-blue-600 hover:bg-blue-100/50 text-xs font-semibold px-2.5 py-1.5 rounded-lg">Tạo mã mới</button>
            </div>
          ` : ''}
        </div>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
          <b>Lưu ý:</b> Đây là <b>Mã liên kết học sinh</b> dùng để phụ huynh/học sinh kết nối với hồ sơ học sinh này. Mã này cũng được hiển thị trực tiếp ở ngoài giao diện cùng với Mã giáo viên.
        </p>
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
        <button type="button" onclick="closeModal()" class="border border-slate-200 hover:bg-slate-50 rounded-xl px-5 py-2.5 font-semibold text-sm text-slate-600">Hủy</button>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-6 py-2.5 font-bold text-sm shadow-md shadow-blue-500/20">Lưu thông tin</button>
      </div>
    </form>
  `;
  openModal();
}

function copyStudentCode(code) {
  navigator.clipboard.writeText(code).then(() => {
    const toast = document.getElementById('copyToast');
    document.getElementById('toastMsg').innerText = `Đã sao chép mã liên kết học sinh (${code})!`;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2200);
  });
}

function editStudent() {
  openStudent(data.student);
}

async function saveStudent(e) {
  e.preventDefault();
  try {
    await api('api/students.php?action=save', { method: 'POST', body: new FormData(e.target) });
    closeModal();
    await loadStudents();
  } catch (err) {
    alert(err.message);
  }
}

async function deleteStudent() {
  if (!confirm('Bạn có chắc chắn muốn xóa học sinh này cùng toàn bộ dữ liệu buổi học và điểm số liên quan?')) return;
  let f = new FormData();
  f.append('id', selectedId);
  try {
    await api('api/students.php?action=delete', { method: 'POST', body: f });
    selectedId = null;
    await loadStudents();
  } catch (e) {
    alert(e.message);
  }
}

async function regenerateCode() {
  if (!confirm('Tạo lại mã kết nối mới cho học sinh này? Mã cũ sẽ không còn hiệu lực.')) return;
  let f = new FormData();
  f.append('id', selectedId);
  try {
    let d = await api('api/students.php?action=regenerate_code', { method: 'POST', body: f });
    alert('Đã cập nhật mã kết nối học sinh mới: ' + d.code);
    closeModal();
    await loadData();
  } catch (e) {
    alert(e.message);
  }
}

// Modal Thêm / Sửa Buổi Học (với danh sách môn học Tiểu học)
function openLesson(x = null) {
  const currentSubject = x?.subject || 'Toán';
  $('modalBox').innerHTML = `
    <form onsubmit="saveLesson(event)" class="p-6 md:p-8 space-y-4">
      <div class="flex justify-between items-center pb-3 border-b border-slate-100">
        <h3 class="font-extrabold text-xl text-slate-800">${x ? 'Chỉnh sửa' : 'Thêm'} buổi học</h3>
        <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
      </div>
      <input type="hidden" name="id" value="${x?.id || ''}">
      <input type="hidden" name="student_id" value="${selectedId}">
      ${field('Ngày học *', 'lesson_date', x?.lesson_date || new Date().toISOString().slice(0, 10), true, 'date')}
      <div class="grid grid-cols-2 gap-3">
        ${field('Giờ bắt đầu', 'start_time', x?.start_time?.slice(0, 5) || '', false, 'time')}
        ${field('Giờ kết thúc', 'end_time', x?.end_time?.slice(0, 5) || '', false, 'time')}
      </div>
      <div class="grid grid-cols-2 gap-3">
        ${field('Số giờ học *', 'duration', x?.duration || '1.5', true, 'number', '0.5', '0')}
        ${field('Học phí buổi này (VNĐ) *', 'price', x?.price ?? '150000', true, 'number', '1000', '0')}
      </div>

      <!-- MỤC NỘI DUNG MÔN HỌC TIỂU HỌC: Toán, Tiếng Việt, Tiếng Anh... -->
      <div>
        <label class="label">Nội dung môn học (Chương trình Giáo dục Tiểu học) *</label>
        <select name="subject" class="input font-semibold text-slate-800" required>
          ${PRIMARY_SUBJECTS.map(v => `<option value="${v}" ${currentSubject === v ? 'selected' : ''}>${v}</option>`).join('')}
        </select>
        <p class="text-[11px] text-slate-400 mt-1">Lựa chọn môn học thuộc chương trình tiểu học hoặc ôn luyện chuyên sâu.</p>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="label">Hoàn thành BTVN</label>
          <select name="homework_percent" class="input">
            ${[0, 30, 50, 70, 80, 90, 100].map(v => `<option value="${v}" ${+x?.homework_percent === v ? 'selected' : ''}>${v}%</option>`).join('')}
          </select>
        </div>
        <div>
          <label class="label">Ý thức học tập</label>
          <select name="attitude" class="input">
            ${['Tốt', 'Mất tập trung', 'Lười học', 'Nói chuyện riêng'].map(v => `<option ${x?.attitude === v ? 'selected' : ''}>${v}</option>`).join('')}
          </select>
        </div>
      </div>
      ${field('Nhận xét buổi học', 'comment', x?.comment || '', false, 'text', '', '', '', 'Tiếp thu bài, làm bài tập, thái độ học...')}
      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
        <button type="button" onclick="closeModal()" class="border border-slate-200 hover:bg-slate-50 rounded-xl px-5 py-2.5 font-semibold text-sm text-slate-600">Hủy</button>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-6 py-2.5 font-bold text-sm shadow-md shadow-blue-500/20">Lưu buổi học</button>
      </div>
    </form>
  `;
  openModal();
}

function editLesson(x) { openLesson(x); }
async function saveLesson(e) {
  e.preventDefault();
  try {
    await api('api/lessons.php?action=save', { method: 'POST', body: new FormData(e.target) });
    closeModal();
    await loadData();
    await loadStudents();
  } catch (err) {
    alert(err.message);
  }
}
async function deleteLesson(id) {
  if (!confirm('Bạn có chắc muốn xóa buổi học này?')) return;
  let f = new FormData();
  f.append('id', id);
  try {
    await api('api/lessons.php?action=delete', { method: 'POST', body: f });
    await loadData();
    await loadStudents();
  } catch (e) {
    alert(e.message);
  }
}

function openGrade(x = null) {
  $('modalBox').innerHTML = `
    <form onsubmit="saveGrade(event)" class="p-6 md:p-8 space-y-4">
      <div class="flex justify-between items-center pb-3 border-b border-slate-100">
        <h3 class="font-extrabold text-xl text-slate-800">${x ? 'Chỉnh sửa' : 'Thêm'} bài kiểm tra</h3>
        <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
      </div>
      <input type="hidden" name="id" value="${x?.id || ''}">
      <input type="hidden" name="student_id" value="${selectedId}">
      ${field('Ngày kiểm tra *', 'test_date', x?.test_date || new Date().toISOString().slice(0, 10), true, 'date')}
      ${field('Tên bài kiểm tra *', 'test_name', x?.test_name || '', true, 'text', '', '', '', 'VD: Kiểm tra Toán 15p, Chính tả Tiếng Việt...')}
      ${field('Điểm số (Thang điểm 10) *', 'score', x?.score ?? '', true, 'number', '0.1', '0', '10')}
      ${field('Lỗi sai & Điểm cần cải thiện', 'mistakes', x?.mistakes || '', false, 'text', '', '', '', 'Sai phép tính cộng trừ, viết sai chính tả...')}
      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
        <button type="button" onclick="closeModal()" class="border border-slate-200 hover:bg-slate-50 rounded-xl px-5 py-2.5 font-semibold text-sm text-slate-600">Hủy</button>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-6 py-2.5 font-bold text-sm shadow-md shadow-blue-500/20">Lưu kết quả</button>
      </div>
    </form>
  `;
  openModal();
}

function editGrade(x) { openGrade(x); }
async function saveGrade(e) {
  e.preventDefault();
  try {
    await api('api/grades.php?action=save', { method: 'POST', body: new FormData(e.target) });
    closeModal();
    await loadData();
  } catch (err) {
    alert(err.message);
  }
}
async function deleteGrade(id) {
  if (!confirm('Bạn có chắc muốn xóa bài kiểm tra này?')) return;
  let f = new FormData();
  f.append('id', id);
  try {
    await api('api/grades.php?action=delete', { method: 'POST', body: f });
    await loadData();
  } catch (e) {
    alert(e.message);
  }
}

function openPayment() {
  let p = data?.payment || {};
  $('modalBox').innerHTML = `
    <form onsubmit="savePayment(event)" enctype="multipart/form-data" class="p-6 md:p-8 space-y-4">
      <div class="flex justify-between items-center pb-3 border-b border-slate-100">
        <h3 class="font-extrabold text-xl text-slate-800">Cài đặt thông tin thanh toán</h3>
        <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
      </div>
      ${field('Tên Ngân hàng', 'bank_name', p.bank_name || '', false, 'text', '', '', '', 'VD: Vietcombank, MBBank...')}
      ${field('Số tài khoản', 'account_number', p.account_number || '', false, 'text', '', '', '', '0123456789')}
      ${field('Tên chủ tài khoản', 'account_owner', p.account_owner || '', false, 'text', '', '', '', 'NGUYEN VAN A')}
      <div>
        <label class="label">Hình ảnh Mã QR chuyển khoản</label>
        <input type="file" name="qr_image" accept="image/*" class="input">
      </div>
      ${p.qr_image ? `<div class="mt-2"><span class="text-xs text-slate-400 block mb-1">Mã QR hiện tại:</span><img src="${p.qr_image}" class="w-36 h-36 object-contain rounded-xl border border-slate-200 p-1"></div>` : ''}
      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
        <button type="button" onclick="closeModal()" class="border border-slate-200 hover:bg-slate-50 rounded-xl px-5 py-2.5 font-semibold text-sm text-slate-600">Hủy</button>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-6 py-2.5 font-bold text-sm shadow-md shadow-blue-500/20">Lưu cài đặt</button>
      </div>
    </form>
  `;
  openModal();
}

async function savePayment(e) {
  e.preventDefault();
  try {
    await api('api/payment.php', { method: 'POST', body: new FormData(e.target) });
    closeModal();
    await loadData();
  } catch (err) {
    alert(err.message);
  }
}


function openFeedback() {
  const f = data?.feedback || {};

  let subjectFeedback = {};
  let subjectRoadmap = {};

  try {
    subjectFeedback = typeof f.subject_feedback === 'string'
      ? JSON.parse(f.subject_feedback || '{}')
      : (f.subject_feedback || {});
  } catch (e) {
    subjectFeedback = {};
  }

  try {
    subjectRoadmap = typeof f.subject_roadmap === 'string'
      ? JSON.parse(f.subject_roadmap || '{}')
      : (f.subject_roadmap || {});
  } catch (e) {
    subjectRoadmap = {};
  }

  /*
   * Lấy môn thực tế đã học trong tháng.
   */
  const subjects = [...new Set(
    (data.lessons || [])
      .map(x => String(x.subject || '').trim())
      .filter(Boolean)
  )];

  let subjectFields = '';

  if (subjects.length) {
    subjectFields = subjects.map(subject => `
      <div class="border border-slate-200 rounded-2xl p-4 space-y-4 bg-slate-50/60">
        <div class="font-extrabold text-blue-700 text-sm">
          ${esc(subject)}
        </div>

        ${field(
          'Nhận xét học tập',
          `subject_feedback[${subject}]`,
          subjectFeedback[subject] || '',
          false,
          'text',
          '',
          '',
          '',
          `Nhận xét kết quả học ${subject}...`
        )}

        ${field(
          'Lộ trình tháng tới',
          `subject_roadmap[${subject}]`,
          subjectRoadmap[subject] || '',
          false,
          'text',
          '',
          '',
          '',
          `Mục tiêu học tập ${subject} tháng tới...`
        )}
      </div>
    `).join('');
  } else {
    subjectFields = `
      <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
        Chưa có buổi học nào trong tháng này nên chưa thể tạo nhận xét theo môn.
      </div>
    `;
  }

  $('modalBox').innerHTML = `
    <form onsubmit="saveFeedback(event)" class="p-6 md:p-8 space-y-4">

      <div class="flex justify-between items-center pb-3 border-b border-slate-100">
        <h3 class="font-extrabold text-xl text-slate-800">
          Nhận xét & Lộ trình học tập tiểu học
        </h3>

        <button
          type="button"
          onclick="closeModal()"
          class="text-slate-400 hover:text-slate-600 text-xl font-bold"
        >×</button>
      </div>

      <input type="hidden" name="student_id" value="${selectedId}">
      <input type="hidden" name="month_key" value="${$('month').value}">

      ${field(
        'Nhận xét tổng quan',
        'overview',
        f.overview || '',
        false,
        'text',
        '',
        '',
        '',
        'Ý thức, chuyên cần, tiến bộ chung...'
      )}

      <div class="pt-2">
        <div class="text-sm font-extrabold text-slate-700 mb-3">
          Nhận xét & lộ trình theo môn
        </div>

        <div class="space-y-3">
          ${subjectFields}
        </div>
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">

        <button
          type="button"
          onclick="closeModal()"
          class="border border-slate-200 hover:bg-slate-50 rounded-xl px-5 py-2.5 font-semibold text-sm text-slate-600"
        >
          Hủy
        </button>

        <button
          type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-6 py-2.5 font-bold text-sm shadow-md shadow-blue-500/20"
        >
          Lưu nhận xét
        </button>

      </div>

    </form>
  `;

  openModal();
}

async function saveFeedback(e) {
  e.preventDefault();
  try {
    await api('api/feedback.php', { method: 'POST', body: new FormData(e.target) });
    closeModal();
    await loadData();
  } catch (err) {
    alert(err.message);
  }
}

function openLink() {
  $('modalBox').innerHTML = `
    <form onsubmit="linkStudent(event)" class="p-6 md:p-8 space-y-4">
      <div class="flex justify-between items-center pb-3 border-b border-slate-100">
        <h3 class="font-extrabold text-xl text-slate-800">Liên kết hồ sơ học sinh</h3>
        <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">×</button>
      </div>
      <p class="text-sm text-slate-500 leading-relaxed">
        Nhập <b>Mã liên kết học sinh</b> do giáo viên cung cấp để liên kết tài khoản của bạn với hồ sơ học sinh.
      </p>
      ${field('Mã liên kết học sinh *', 'connection_code', '', true, 'text', '', '', '', 'VD: 8A9B2C1D')}
      <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
        <button type="button" onclick="closeModal()" class="border border-slate-200 hover:bg-slate-50 rounded-xl px-5 py-2.5 font-semibold text-sm text-slate-600">Hủy</button>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-6 py-2.5 font-bold text-sm shadow-md shadow-blue-500/20">Liên kết ngay</button>
      </div>
    </form>
  `;
  openModal();
}

async function linkStudent(e) {
  e.preventDefault();
  try {
    await api('api/link.php', { method: 'POST', body: new FormData(e.target) });
    closeModal();
    await loadStudents();
  } catch (err) {
    alert(err.message);
  }
}


// ==========================================================
// XUẤT BÁO CÁO THÀNH HÌNH ẢNH - MẪU RIÊNG CHO PHỤ HUYNH
// ==========================================================

function safeFileName(text) {
  return String(text || '')
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/đ/g, 'd')
    .replace(/Đ/g, 'D')
    .replace(/[^a-zA-Z0-9]+/g, '-')
    .replace(/^-+|-+$/g, '');
}

function reportMonthText() {
  const value = $('month')?.value || new Date().toISOString().slice(0, 7);
  const [year, month] = value.split('-');
  return `THÁNG ${Number(month)}/${year}`;
}

function reportMonthFile() {
  return $('month')?.value || new Date().toISOString().slice(0, 7);
}

function reportTeacherName() {
  return ($('teacherName')?.textContent || '')
    .replace(/^Giáo viên hướng dẫn:\s*/i, '')
    .trim() || 'Giáo viên';
}

function reportStudentName() {
  return $('sname')?.textContent?.trim() || 'Học sinh';
}

function reportStudentClass() {
  return $('sclass')?.textContent?.trim() || 'Chưa cập nhật lớp';
}

function escapeReportText(value) {
  return String(value ?? '—')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;')
    .replace(/\n/g, '<br>');
}

function weekdayVN(dateString) {
  if (!dateString) return '';
  const d = new Date(dateString + 'T00:00:00');
  const day = d.getDay();

  return [
    'CN',
    'Thứ 2',
    'Thứ 3',
    'Thứ 4',
    'Thứ 5',
    'Thứ 6',
    'Thứ 7'
  ][day];
}

function formatReportDate(dateString) {
  if (!dateString) return '—';
  const parts = String(dateString).split('-');
  if (parts.length !== 3) return escapeReportText(dateString);
  return `${parts[2]}/${parts[1]}`;
}

function reportMoney(value) {
  const n = Number(value || 0);
  return n.toLocaleString('vi-VN') + ' đ';
}

function reportStyles(width = 900) {
  return `
    <style>
      .edu-report {
        width: ${width}px;
        box-sizing: border-box;
        background: #ffffff;
        color: #183b63;
        font-family: Arial, "Segoe UI", sans-serif;
        padding: 28px;
      }

      .edu-report * {
        box-sizing: border-box;
      }

      .report-border {
        border: 2px solid #c9def2;
        border-radius: 28px;
        overflow: hidden;
        background: #fff;
      }

      .report-header {
        padding: 26px 30px 12px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        font-size: 17px;
        color: #334155;
      }

      .report-title {
        text-align: center;
        font-size: 32px;
        line-height: 1.2;
        font-weight: 800;
        color: #174b85;
        margin: 8px 0 24px;
      }

      .report-subtitle {
        text-align: center;
        color: #64748b;
        font-size: 14px;
        margin-top: -15px;
        margin-bottom: 22px;
      }

      .report-section-title {
        font-size: 19px;
        font-weight: 800;
        color: #164b80;
        border-bottom: 2px solid #cfe0ef;
        padding-bottom: 7px;
        margin-bottom: 12px;
      }

      .info-box {
        border: 2px solid #c9def2;
        border-radius: 22px;
        padding: 18px;
        background: #fff;
      }

      .info-title {
        color: #174b85;
        font-weight: 800;
        font-size: 19px;
        border-bottom: 1px solid #d8e5ef;
        padding-bottom: 7px;
        margin-bottom: 8px;
      }

      .info-row {
        display: flex;
        padding: 6px 0;
        border-bottom: 1px dashed #dbe5ed;
        font-size: 15px;
      }

      .info-row:last-child {
        border-bottom: 0;
      }

      .info-label {
        width: 145px;
        color: #64748b;
      }

      .info-value {
        flex: 1;
        color: #334155;
        font-weight: 600;
      }

      .fee-box {
        border: 2px solid #c9def2;
        border-radius: 22px;
        padding: 18px;
        text-align: center;
      }

      .fee-title {
        color: #64748b;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
      }

      .fee-number {
        color: #174b85;
        font-size: 32px;
        font-weight: 900;
        margin: 4px 0 10px;
      }

      .qr-image {
        width: 210px;
        height: 210px;
        object-fit: contain;
        display: block;
        margin: 0 auto 12px;
        border: 1px solid #d8e5ef;
        border-radius: 14px;
        padding: 8px;
      }

      .payment-line {
        font-size: 15px;
        color: #475569;
        margin-top: 5px;
      }

      .feedback-block {
        margin-top: 18px;
      }

      .feedback-item {
        margin-bottom: 10px;
        font-size: 15px;
        line-height: 1.55;
        color: #334155;
      }

      .feedback-label {
        font-weight: 800;
        color: #174b85;
      }

      .schedule-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 28px;
        margin-top: 16px;
      }

      .schedule-item {
        font-size: 15px;
        line-height: 1.6;
        color: #334155;
      }

      .report-note {
        margin-top: 20px;
        background: #e8f3ff;
        border-radius: 18px;
        padding: 11px 18px;
        text-align: center;
        color: #315c84;
        font-size: 14px;
        font-weight: 600;
      }

      /* TABLE REPORT */
      .table-report {
        width: ${width}px;
        box-sizing: border-box;
        background: #fff;
        color: #334155;
        font-family: Arial, "Segoe UI", sans-serif;
        padding: 0;
      }

      .table-top {
        background: linear-gradient(135deg, #1762c5, #2459b9);
        color: white;
        text-align: center;
        padding: 20px 25px;
      }

      .table-top-title {
        font-size: 25px;
        font-weight: 800;
        letter-spacing: .3px;
      }

      .table-top-sub {
        font-size: 13px;
        margin-top: 5px;
        opacity: .9;
      }

      .report-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 13px;
      }

      .report-table th {
        background: #edf4fb;
        color: #52677b;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 11px;
        padding: 10px 7px;
        border: 1px solid #d7e1ea;
        text-align: center;
      }

      .report-table td {
        border: 1px solid #d7e1ea;
        padding: 10px 7px;
        vertical-align: top;
        line-height: 1.45;
        color: #334155;
      }

      .report-table tbody tr:nth-child(even) td {
        background: #fbfdff;
      }

      .center {
        text-align: center;
      }

      .lesson-no {
        font-weight: 800;
        color: #64748b;
      }

      .price {
        color: #1762a8;
        font-weight: 800;
      }

      .pill {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 999px;
        background: #eef7ff;
        color: #1762a8;
        font-weight: 700;
        font-size: 11px;
      }

      .grade-score {
        font-size: 18px;
        font-weight: 900;
        color: #1762c5;
      }

      .summary-row {
        display: flex;
        gap: 15px;
        padding: 18px 20px;
        background: #f8fbff;
        border-bottom: 1px solid #d7e1ea;
      }

      .summary-box {
        flex: 1;
        border: 1px solid #cbddec;
        border-radius: 15px;
        padding: 12px 16px;
        background: white;
      }

      .summary-label {
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
      }

      .summary-value {
        color: #1762c5;
        font-size: 23px;
        font-weight: 900;
        margin-top: 3px;
      }

      .table-footer {
        padding: 16px 20px;
        color: #64748b;
        font-size: 12px;
        text-align: right;
      }

      .empty-report {
        padding: 45px;
        text-align: center;
        color: #94a3b8;
        font-size: 15px;
      }
    </style>
  `;
}

function buildOverviewReport() {
  const s = data?.student || {};
  const p = data?.payment || {};
  const lessons = data?.lessons || [];
  const f = data?.feedback || {};

  const totalFee = lessons.reduce((sum, x) => sum + Number(x.price || 0), 0);
  const totalHours = lessons.reduce((sum, x) => sum + Number(x.duration || 0), 0);

  const avgPrice = lessons.length
    ? Math.round(totalFee / lessons.length)
    : 0;

  const dates = lessons.map(x => formatReportDate(x.lesson_date)).join(', ') || 'Chưa có';

  const scheduleMap = {};

  lessons.forEach(x => {
    const day = weekdayVN(x.lesson_date);
    const time =
      x.start_time && x.end_time
        ? `${String(x.start_time).slice(0,5)} - ${String(x.end_time).slice(0,5)}`
        : '';

    if (day) {
      if (!scheduleMap[day]) scheduleMap[day] = [];
      if (time && !scheduleMap[day].includes(time)) {
        scheduleMap[day].push(time);
      }
    }
  });

  const scheduleHtml = Object.keys(scheduleMap).length
    ? Object.entries(scheduleMap).map(([day, times]) => `
        <div class="schedule-item">
          <b>${escapeReportText(day)}:</b>
          ${escapeReportText(times.join(', '))}
        </div>
      `).join('')
    : `
      <div class="schedule-item">
        <b>Lịch học:</b> Theo lịch đã ghi nhận trong hệ thống
      </div>
    `;

  const qr = p.qr_image
    ? `<img class="qr-image" src="${escapeReportText(p.qr_image)}" crossorigin="anonymous">`
    : `<div style="height:210px;display:flex;align-items:center;justify-content:center;color:#94a3b8;">Chưa có mã QR</div>`;

  return `
    ${reportStyles(900)}

    <div class="edu-report">
      <div class="report-border">

        <div class="report-header">
          <div>GV. ${escapeReportText(reportTeacherName())}</div>
          <div>${escapeReportText(p.account_number || '')}</div>
        </div>

        <div class="report-title">
          HỌC PHÍ ${reportMonthText()}
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;padding:0 25px 22px;">

          <div class="info-box">
            <div class="info-title">THÔNG TIN HỌC SINH</div>

            <div class="info-row">
              <div class="info-label">Họ và tên:</div>
              <div class="info-value">${escapeReportText(s.name || reportStudentName())}</div>
            </div>

            <div class="info-row">
              <div class="info-label">Lớp:</div>
              <div class="info-value">${escapeReportText(s.class_name || reportStudentClass())}</div>
            </div>

            <div class="info-row">
              <div class="info-label">Học phí:</div>
              <div class="info-value">${reportMoney(avgPrice)}/buổi</div>
            </div>

            <div class="info-row">
              <div class="info-label">Buổi học:</div>
              <div class="info-value">${lessons.length} buổi</div>
            </div>

            <div class="info-row">
              <div class="info-label">Giờ học:</div>
              <div class="info-value">${totalHours} giờ</div>
            </div>

            <div class="info-row">
              <div class="info-label">Ngày học:</div>
              <div class="info-value">${escapeReportText(dates)}</div>
            </div>
          </div>

          <div class="fee-box">
            <div class="fee-title">TỔNG HỌC PHÍ</div>
            <div class="fee-number">${reportMoney(totalFee)}</div>

            ${qr}

            <div class="payment-line">
              Ngân hàng: <b>${escapeReportText(p.bank_name || 'Chưa thiết lập')}</b>
            </div>

            <div class="payment-line">
              Số TK: <b>${escapeReportText(p.account_number || '—')}</b>
            </div>

            <div class="payment-line">
              Chủ TK: <b>${escapeReportText(p.account_owner || '—')}</b>
            </div>
          </div>

        </div>

        <div style="padding:0 25px 22px;">

          <div class="feedback-block">
            <div class="report-section-title">NHẬN XÉT HỌC TẬP</div>

            <div class="feedback-item">
              <span class="feedback-label">Tổng quan:</span>
              ${escapeReportText(f.overview || 'Chưa có nhận xét.')}
            </div>

            <div class="feedback-item">
              <span class="feedback-label">Đại số:</span>
              ${escapeReportText(f.algebra || '—')}
            </div>

            <div class="feedback-item">
              <span class="feedback-label">Hình học:</span>
              ${escapeReportText(f.geometry || '—')}
            </div>
          </div>

          <div class="feedback-block">
            <div class="report-section-title">LỘ TRÌNH SẮP TỚI</div>

            <div class="feedback-item">
              <span class="feedback-label">Đại số:</span>
              ${escapeReportText(f.next_algebra || '—')}
            </div>

            <div class="feedback-item">
              <span class="feedback-label">Hình học:</span>
              ${escapeReportText(f.next_geometry || '—')}
            </div>
          </div>

          <div class="schedule-grid">
            <div>
              <div class="report-section-title">LỊCH HỌC</div>
              ${scheduleHtml}
            </div>

            <div>
              <div class="report-section-title">HỌC PHÍ</div>
              <div class="schedule-item">
                Học phí cơ bản: <b>${reportMoney(avgPrice)}/buổi</b>
              </div>
              <div class="schedule-item">
                Tổng số buổi: <b>${lessons.length}</b>
              </div>
              <div class="schedule-item">
                Tổng số giờ: <b>${totalHours} giờ</b>
              </div>
            </div>
          </div>

          <div class="report-note">
            Phụ huynh vui lòng kiểm tra thông tin học phí và lịch học. Chân thành cảm ơn.
          </div>

        </div>
      </div>
    </div>
  `;
}

function buildLessonsReport() {
  const lessons = data?.lessons || [];

  const rows = lessons.length
    ? lessons.map((x, i) => `
        <tr>
          <td class="center lesson-no">Buổi ${i + 1}</td>

          <td class="center">
            <b>${escapeReportText(weekdayVN(x.lesson_date))}</b><br>
            ${formatReportDate(x.lesson_date)}<br>
            <span style="font-size:11px;color:#64748b;">
              ${escapeReportText(x.start_time?.slice(0,5) || '—')}
              -
              ${escapeReportText(x.end_time?.slice(0,5) || '—')}
            </span>
            <br>
            <span class="price">${reportMoney(x.price)}</span>
          </td>

          <td>
            <b>${escapeReportText(x.subject || '—')}</b><br>
            ${escapeReportText(x.content || x.lesson_content || x.topic || '—')}
          </td>

          <td class="center">
            ${x.homework_percent !== undefined && x.homework_percent !== null
              ? `<span class="pill">${escapeReportText(x.homework_percent)}%</span>`
              : '—'}
          </td>

          <td class="center">
            ${escapeReportText(x.attitude || '—')}
          </td>

          <td>
            ${escapeReportText(x.comment || '—')}
          </td>

          <td class="center">
            ${escapeReportText(x.note || '—')}
          </td>
        </tr>
      `).join('')
    : `
        <tr>
          <td colspan="7" class="empty-report">
            Chưa có buổi học nào trong tháng này.
          </td>
        </tr>
      `;

  return `
    ${reportStyles(1250)}

    <div class="table-report">

      <div class="table-top">
        <div class="table-top-title">
          ${escapeReportText(reportStudentName()).toUpperCase()}
          ${escapeReportText(reportStudentClass()).toUpperCase()}
        </div>

        <div class="table-top-sub">
          NHẬT KÝ BUỔI HỌC · ${reportMonthText()}
        </div>
      </div>

      <div class="summary-row">

        <div class="summary-box">
          <div class="summary-label">Số buổi</div>
          <div class="summary-value">${lessons.length}</div>
        </div>

        <div class="summary-box">
          <div class="summary-label">Tổng giờ học</div>
          <div class="summary-value">
            ${lessons.reduce((a,x) => a + Number(x.duration || 0), 0)}h
          </div>
        </div>

        <div class="summary-box">
          <div class="summary-label">Tổng học phí</div>
          <div class="summary-value">
            ${reportMoney(lessons.reduce((a,x) => a + Number(x.price || 0), 0))}
          </div>
        </div>

      </div>

      <table class="report-table">
        <colgroup>
          <col style="width:8%">
          <col style="width:13%">
          <col style="width:29%">
          <col style="width:8%">
          <col style="width:10%">
          <col style="width:24%">
          <col style="width:8%">
        </colgroup>

        <thead>
          <tr>
            <th>STT</th>
            <th>Ngày</th>
            <th>Nội dung</th>
            <th>BTVN</th>
            <th>Ý thức</th>
            <th>Nhận xét</th>
            <th>Ghi chú</th>
          </tr>
        </thead>

        <tbody>
          ${rows}
        </tbody>
      </table>

      <div class="table-footer">
        Giáo viên: <b>${escapeReportText(reportTeacherName())}</b>
      </div>

    </div>
  `;
}

function buildGradesReport() {
  const grades = data?.grades || [];
  const feedback = data?.feedback || {};

  const average = grades.length
    ? grades.reduce((sum, x) => sum + Number(x.score || 0), 0) / grades.length
    : null;

  const rows = grades.length
    ? grades.map(x => `
        <tr>
          <td class="center">
            ${formatReportDate(x.test_date)}
          </td>

          <td>
            <b>${escapeReportText(x.test_name || '—')}</b>
          </td>

          <td class="center">
            <span class="grade-score">
              ${escapeReportText(x.score)}
            </span>
            <span style="color:#94a3b8;"> / 10</span>
          </td>

          <td>
            ${escapeReportText(x.mistakes || 'Không có lỗi được ghi nhận.')}
          </td>
        </tr>
      `).join('')
    : `
        <tr>
          <td colspan="4" class="empty-report">
            Chưa có bài kiểm tra nào trong tháng này.
          </td>
        </tr>
      `;

  return `
    ${reportStyles(1050)}

    <div class="table-report">

      <div class="table-top">
        <div class="table-top-title">
          BẢNG ĐIỂM CHI TIẾT
        </div>

        <div class="table-top-sub">
          ${escapeReportText(reportStudentName())}
          · ${escapeReportText(reportStudentClass())}
          · ${reportMonthText()}
        </div>
      </div>

      <div class="summary-row">

        <div class="summary-box">
          <div class="summary-label">Số bài kiểm tra</div>
          <div class="summary-value">${grades.length}</div>
        </div>

        <div class="summary-box">
          <div class="summary-label">Điểm trung bình</div>
          <div class="summary-value">
            ${average === null ? '—' : average.toFixed(1)}
          </div>
        </div>

      </div>

      <table class="report-table">
        <colgroup>
          <col style="width:16%">
          <col style="width:30%">
          <col style="width:18%">
          <col style="width:36%">
        </colgroup>

        <thead>
          <tr>
            <th>Ngày</th>
            <th>Tên bài kiểm tra</th>
            <th>Điểm số</th>
            <th>Lỗi sai cần khắc phục</th>
          </tr>
        </thead>

        <tbody>
          ${rows}
        </tbody>
      </table>

      <div style="padding:20px;">
        <div class="report-section-title">
          NHẬN XÉT HỌC TẬP
        </div>

        <div class="feedback-item">
          <span class="feedback-label">Tổng quan:</span>
          ${escapeReportText(feedback.overview || 'Chưa có nhận xét.')}
        </div>

        <div class="feedback-item">
          <span class="feedback-label">Đại số:</span>
          ${escapeReportText(feedback.algebra || '—')}
        </div>

        <div class="feedback-item">
          <span class="feedback-label">Hình học:</span>
          ${escapeReportText(feedback.geometry || '—')}
        </div>

        <div class="report-section-title" style="margin-top:22px;">
          LỘ TRÌNH THÁNG TỚI
        </div>

        <div class="feedback-item">
          <span class="feedback-label">Đại số:</span>
          ${escapeReportText(feedback.next_algebra || '—')}
        </div>

        <div class="feedback-item">
          <span class="feedback-label">Hình học:</span>
          ${escapeReportText(feedback.next_geometry || '—')}
        </div>
      </div>

      <div class="table-footer">
        Giáo viên: <b>${escapeReportText(reportTeacherName())}</b>
      </div>

    </div>
  `;
}

async function waitReportImages(container) {
  const images = Array.from(container.querySelectorAll('img'));

  await Promise.all(
    images.map(img => {
      if (img.complete) {
        return Promise.resolve();
      }

      return new Promise(resolve => {
        img.onload = resolve;
        img.onerror = resolve;
      });
    })
  );
}

async function downloadReportCanvas(container, fileName) {
  if (typeof html2canvas === 'undefined') {
    throw new Error('Thư viện xuất hình ảnh chưa được tải.');
  }

  await waitReportImages(container);

  const canvas = await html2canvas(container, {
    scale: 2,
    backgroundColor: '#ffffff',
    useCORS: true,
    allowTaint: false,
    logging: false,
    imageTimeout: 15000
  });

  const dataUrl = canvas.toDataURL('image/png');

  const link = document.createElement('a');
  link.href = dataUrl;
  link.download = fileName;
  link.style.display = 'none';

  document.body.appendChild(link);
  link.click();
  link.remove();
}

async function exportCustomReport(type, title) {
  if (!data || !data.student) {
    alert('Vui lòng chọn học sinh trước khi xuất báo cáo.');
    return;
  }

  let html = '';
  let width = 900;

  if (type === 'overview') {
    html = buildOverviewReport();
    width = 900;
  } else if (type === 'lessons') {
    html = buildLessonsReport();
    width = 1250;
  } else if (type === 'grades') {
    html = buildGradesReport();
    width = 1050;
  }

  const wrapper = document.createElement('div');

  wrapper.style.position = 'fixed';
  wrapper.style.left = '-100000px';
  wrapper.style.top = '0';
  wrapper.style.width = width + 'px';
  wrapper.style.background = '#ffffff';
  wrapper.style.zIndex = '-1';

  wrapper.innerHTML = html;

  document.body.appendChild(wrapper);

  try {
    const student = safeFileName(reportStudentName()) || 'Hoc-Sinh';
    const month = reportMonthFile();

    const fileName =
      `${title}-${student}-${month}.png`;

    await downloadReportCanvas(wrapper, fileName);

  } catch (error) {
    console.error('Export report error:', error);
    alert('Không thể xuất báo cáo: ' + error.message);

  } finally {
    wrapper.remove();
  }
}

function showTab(id) {
  document.querySelectorAll('.page').forEach(x => x.classList.add('hidden'));
  $(id).classList.remove('hidden');
  document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
  const word = id === 'overview' ? 'phiếu' : id === 'lessons' ? 'nhật ký' : 'bảng điểm';
  document.querySelectorAll('.tab').forEach(x => {
    if (x.textContent.toLowerCase().includes(word)) x.classList.add('active');
  });
}

function openModal() {
  $('modal').classList.remove('hidden');
  $('modal').classList.add('flex');
}

function closeModal() {
  $('modal').classList.add('hidden');
  $('modal').classList.remove('flex');
}

function toggleSide() {
  $('side').classList.toggle('open');
  $('overlay').classList.toggle('hidden');
}

function money(n) {
  return new Intl.NumberFormat('vi-VN').format(Number(n) || 0) + ' đ';
}

function fmtDate(d) {
  return new Date(d + 'T00:00:00').toLocaleDateString('vi-VN');
}

function esc(s) {
  return String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

function field(label, name, value = '', required = false, type = 'text', step = '', min = '', max = '', placeholder = '') {
  let attr = `type="${type}" name="${name}" ${required ? 'required' : ''}`;
  if (step) attr += ` step="${step}"`;
  if (min !== '') attr += ` min="${min}"`;
  if (max !== '') attr += ` max="${max}"`;
  if (placeholder) attr += ` placeholder="${esc(placeholder)}"`;

  if (type === 'file') {
    return `<label class="label">${label}<input ${attr} class="input"></label>`;
  }
  return `<label class="label">${label}<input ${attr} value="${esc(value)}" class="input"></label>`;
}

loadStudents();


// ==========================================================
// BỘ XUẤT BÁO CÁO PNG RIÊNG
// Không chụp giao diện chính.
// Tạo một report DOM riêng rồi dùng html2canvas.
// ==========================================================

function reportEscape(value) {
  return esc(value ?? '—');
}

function reportSubjects() {
  return [...new Set(
    (data?.lessons || [])
      .map(x => String(x.subject || '').trim())
      .filter(Boolean)
  )];
}

function reportFeedbackMaps() {
  const f = data?.feedback || {};

  let feedback = {};
  let roadmap = {};

  try {
    feedback = typeof f.subject_feedback === 'string'
      ? JSON.parse(f.subject_feedback || '{}')
      : (f.subject_feedback || {});
  } catch (e) {
    feedback = {};
  }

  try {
    roadmap = typeof f.subject_roadmap === 'string'
      ? JSON.parse(f.subject_roadmap || '{}')
      : (f.subject_roadmap || {});
  } catch (e) {
    roadmap = {};
  }

  return { feedback, roadmap };
}

function reportMonthLabel() {
  const value = $('month')?.value || '';

  if (!/^\d{4}-\d{2}$/.test(value)) {
    return value || '—';
  }

  const [year, month] = value.split('-');

  return `Tháng ${Number(month)}/${year}`;
}

function reportTotalHours() {
  return (data?.lessons || [])
    .reduce((sum, x) => sum + Number(x.duration || 0), 0);
}

function reportTotalFee() {
  return (data?.lessons || [])
    .reduce((sum, x) => sum + Number(x.price || 0), 0);
}

function reportAverageGrade() {
  const grades = data?.grades || [];

  if (!grades.length) return null;

  return grades.reduce(
    (sum, x) => sum + Number(x.score || 0),
    0
  ) / grades.length;
}

function reportShell(title, subtitle, content, extraClass = '') {
  return `
    <div
      id="eduReportCanvas"
      style="
        width: 794px;
        background:#ffffff;
        color:#172033;
        font-family:Arial,Helvetica,sans-serif;
        box-sizing:border-box;
        padding:0;
        ${extraClass}
      "
    >

      <div
        style="
          background:linear-gradient(135deg,#0b63ce,#1684ee);
          color:#ffffff;
          padding:34px 42px 30px;
        "
      >
        <div
          style="
            font-size:14px;
            font-weight:700;
            letter-spacing:1.5px;
            text-transform:uppercase;
            opacity:.9;
          "
        >
          EDUMANAGE
        </div>

        <div
          style="
            font-size:30px;
            font-weight:900;
            margin-top:8px;
            line-height:1.2;
          "
        >
          ${reportEscape(title)}
        </div>

        <div
          style="
            font-size:15px;
            margin-top:8px;
            opacity:.92;
          "
        >
          ${reportEscape(subtitle)}
        </div>
      </div>

      ${content}

      <div
        style="
          margin-top:28px;
          padding:18px 42px 24px;
          border-top:1px solid #e5e7eb;
          font-size:11px;
          color:#64748b;
          text-align:center;
        "
      >
        Báo cáo được tạo từ hệ thống EduManage
      </div>

    </div>
  `;
}

function reportStudentHeader() {
  const s = data?.student || {};

  return `
    <div
      style="
        padding:22px 42px;
        background:#f8fbff;
        border-bottom:1px solid #dbeafe;
      "
    >
      <div style="font-size:21px;font-weight:900;color:#0f172a;">
        ${reportEscape(s.name || 'Học sinh')}
      </div>

      <div style="margin-top:7px;font-size:13px;color:#64748b;">
        Lớp: <b>${reportEscape(s.class_name || 'Chưa cập nhật')}</b>
        &nbsp;&nbsp;•&nbsp;&nbsp;
        Giáo viên: <b>${reportEscape(s.teacher_name || '—')}</b>
      </div>

      ${
        s.connection_code
          ? `
            <div style="margin-top:7px;font-size:12px;color:#2563eb;">
              Mã liên kết học sinh:
              <b>${reportEscape(s.connection_code)}</b>
            </div>
          `
          : ''
      }
    </div>
  `;
}

async function downloadReportElement(title) {
  if (typeof html2canvas === 'undefined') {
    alert('Thư viện xuất hình ảnh chưa được tải.');
    return;
  }

  const element = document.getElementById('eduReportCanvas');

  if (!element) {
    alert('Không tạo được nội dung báo cáo.');
    return;
  }

  document.body.appendChild(element);

  try {
    const canvas = await html2canvas(element, {
      scale: 2,
      backgroundColor: '#ffffff',
      useCORS: true,
      logging: false,
      windowWidth: 1000
    });

    const blob = await new Promise(resolve => {
      canvas.toBlob(resolve, 'image/png');
    });

    if (!blob) {
      throw new Error('Không tạo được file PNG.');
    }

    const studentName =
      $('sname')?.textContent?.trim() || 'Hoc-Sinh';

    const month =
      $('month')?.value ||
      new Date().toISOString().slice(0, 7);

    const fileName =
      `${safeFileName(title)}-${safeFileName(studentName) || 'Hoc-Sinh'}-${month}.png`;

    const url = URL.createObjectURL(blob);

    const link = document.createElement('a');

    link.href = url;
    link.download = fileName;

    document.body.appendChild(link);
    link.click();
    link.remove();

    setTimeout(() => {
      URL.revokeObjectURL(url);
    }, 3000);

  } catch (error) {
    console.error('Export report error:', error);
    alert('Không thể xuất hình ảnh: ' + error.message);
  } finally {
    element.remove();
  }
}


// ==========================================================
// 6.1 PHIẾU HỌC PHÍ & BÁO CÁO
// ==========================================================

function exportOverviewImage() {
  const s = data?.student || {};
  const p = data?.payment || {};
  const lessons = data?.lessons || [];
  const subjects = reportSubjects();

  const { feedback, roadmap } = reportFeedbackMaps();

  const totalHours = reportTotalHours();
  const totalFee = reportTotalFee();

  let subjectFeedbackHtml = '';

  if (subjects.length) {
    subjectFeedbackHtml = subjects.map(subject => `
      <div style="margin-bottom:13px;">
        <div style="font-size:14px;font-weight:900;color:#0f4fa8;">
          ${reportEscape(subject)}
        </div>

        <div
          style="
            margin-top:4px;
            font-size:13px;
            line-height:1.6;
            color:#475569;
          "
        >
          ${reportEscape(feedback[subject] || 'Chưa có nhận xét.')}
        </div>
      </div>
    `).join('');
  } else {
    subjectFeedbackHtml = `
      <div style="font-size:13px;color:#94a3b8;">
        Chưa có dữ liệu môn học trong tháng.
      </div>
    `;
  }

  let roadmapHtml = '';

  if (subjects.length) {
    roadmapHtml = subjects.map(subject => `
      <div style="margin-bottom:13px;">
        <div style="font-size:14px;font-weight:900;color:#0f4fa8;">
          ${reportEscape(subject)}
        </div>

        <div
          style="
            margin-top:4px;
            font-size:13px;
            line-height:1.6;
            color:#475569;
          "
        >
          ${reportEscape(roadmap[subject] || 'Chưa có mục tiêu tháng tới.')}
        </div>
      </div>
    `).join('');
  } else {
    roadmapHtml = `
      <div style="font-size:13px;color:#94a3b8;">
        Chưa có dữ liệu lộ trình.
      </div>
    `;
  }

  const qr = p.qr_image
    ? `
      <img
        src="${p.qr_image}"
        crossorigin="anonymous"
        style="
          width:135px;
          height:135px;
          object-fit:contain;
          border:1px solid #dbeafe;
          border-radius:12px;
          padding:5px;
          background:#fff;
        "
      >
    `
    : `
      <div
        style="
          width:135px;
          height:135px;
          border:1px dashed #cbd5e1;
          border-radius:12px;
          display:flex;
          align-items:center;
          justify-content:center;
          text-align:center;
          color:#94a3b8;
          font-size:11px;
        "
      >
        Chưa thiết lập QR
      </div>
    `;

  const content = `

    ${reportStudentHeader()}

    <div style="padding:28px 42px;">

      <div
        style="
          text-align:center;
          font-size:23px;
          font-weight:900;
          color:#0f172a;
          margin-bottom:24px;
        "
      >
        ${reportMonthLabel()}
      </div>

      <div
        style="
          display:grid;
          grid-template-columns:repeat(3,1fr);
          gap:12px;
          margin-bottom:26px;
        "
      >

        <div
          style="
            border:1px solid #dbeafe;
            border-radius:14px;
            padding:17px;
            background:#f8fbff;
          "
        >
          <div style="font-size:11px;color:#64748b;font-weight:700;">
            SỐ BUỔI HỌC
          </div>

          <div style="font-size:25px;font-weight:900;color:#0b63ce;margin-top:5px;">
            ${lessons.length}
          </div>
        </div>

        <div
          style="
            border:1px solid #dbeafe;
            border-radius:14px;
            padding:17px;
            background:#f8fbff;
          "
        >
          <div style="font-size:11px;color:#64748b;font-weight:700;">
            TỔNG SỐ GIỜ
          </div>

          <div style="font-size:25px;font-weight:900;color:#0b63ce;margin-top:5px;">
            ${totalHours}h
          </div>
        </div>

        <div
          style="
            border:1px solid #dbeafe;
            border-radius:14px;
            padding:17px;
            background:#f8fbff;
          "
        >
          <div style="font-size:11px;color:#64748b;font-weight:700;">
            HỌC PHÍ
          </div>

          <div style="font-size:21px;font-weight:900;color:#0b63ce;margin-top:7px;">
            ${money(totalFee)}
          </div>
        </div>

      </div>

      <div
        style="
          border:1px solid #bfdbfe;
          border-radius:16px;
          overflow:hidden;
          margin-bottom:25px;
        "
      >
        <div
          style="
            background:#eff6ff;
            padding:13px 17px;
            font-size:15px;
            font-weight:900;
            color:#1d4ed8;
          "
        >
          THÔNG TIN THANH TOÁN
        </div>

        <div
          style="
            display:flex;
            justify-content:space-between;
            gap:25px;
            padding:20px;
          "
        >

          <div style="font-size:13px;line-height:2;flex:1;">
            <div>
              <span style="color:#64748b;">Ngân hàng:</span>
              <b>${reportEscape(p.bank_name || 'Chưa thiết lập')}</b>
            </div>

            <div>
              <span style="color:#64748b;">Số tài khoản:</span>
              <b>${reportEscape(p.account_number || '—')}</b>
            </div>

            <div>
              <span style="color:#64748b;">Chủ tài khoản:</span>
              <b>${reportEscape(p.account_owner || '—')}</b>
            </div>

            <div
              style="
                margin-top:12px;
                padding:12px;
                background:#f0fdf4;
                border-radius:10px;
                color:#166534;
                font-weight:900;
              "
            >
              Tổng học phí:
              ${money(totalFee)}
            </div>
          </div>

          <div>
            ${qr}
          </div>

        </div>
      </div>

      <div
        style="
          border:1px solid #e2e8f0;
          border-radius:16px;
          padding:20px;
          margin-bottom:22px;
        "
      >

        <div
          style="
            font-size:17px;
            font-weight:900;
            color:#0f172a;
            margin-bottom:16px;
          "
        >
          NHẬN XÉT HỌC TẬP
        </div>

        <div
          style="
            margin-bottom:15px;
            padding:12px 14px;
            background:#f8fafc;
            border-radius:10px;
            font-size:13px;
            line-height:1.6;
          "
        >
          <b>Tổng quan:</b>
          ${reportEscape(data?.feedback?.overview || 'Chưa có nhận xét tổng quan.')}
        </div>

        ${subjectFeedbackHtml}

      </div>

      <div
        style="
          border:1px solid #e2e8f0;
          border-radius:16px;
          padding:20px;
        "
      >

        <div
          style="
            font-size:17px;
            font-weight:900;
            color:#0f172a;
            margin-bottom:16px;
          "
        >
          LỘ TRÌNH SẮP TỚI
        </div>

        ${roadmapHtml}

      </div>

    </div>
  `;

  const wrapper = document.createElement('div');

  wrapper.style.position = 'fixed';
  wrapper.style.left = '-10000px';
  wrapper.style.top = '0';
  wrapper.style.zIndex = '-1';

  wrapper.innerHTML = reportShell(
    'Phiếu học phí & báo cáo',
    `${s.name || 'Học sinh'} • ${reportMonthLabel()}`,
    content
  );

  document.body.appendChild(wrapper);

  downloadReportElement('Phieu-hoc-phi-bao-cao');
}


// ==========================================================
// 6.2 NHẬT KÝ BUỔI HỌC
// ==========================================================

function exportLessonsImage() {
  const s = data?.student || {};
  const lessons = data?.lessons || [];

  let rows = '';

  if (lessons.length) {
    rows = lessons.map((x, i) => `
      <tr>

        <td
          style="
            text-align:center;
            font-weight:800;
            color:#64748b;
            padding:10px 7px;
            border:1px solid #dbe4ef;
          "
        >
          ${i + 1}
        </td>

        <td
          style="
            white-space:nowrap;
            padding:10px 8px;
            border:1px solid #dbe4ef;
            font-weight:700;
          "
        >
          ${reportEscape(fmtDate(x.lesson_date))}
        </td>

        <td
          style="
            padding:10px 8px;
            border:1px solid #dbe4ef;
          "
        >
          <div style="font-weight:900;color:#155eac;">
            ${reportEscape(x.subject || '—')}
          </div>

          <div style="font-size:11px;color:#64748b;margin-top:4px;">
            ${
              x.start_time
                ? reportEscape(x.start_time.slice(0,5))
                : '—'
            }
            -
            ${
              x.end_time
                ? reportEscape(x.end_time.slice(0,5))
                : '—'
            }
            &nbsp;•&nbsp;
            ${reportEscape(x.duration)} giờ
          </div>
        </td>

        <td
          style="
            text-align:center;
            padding:10px 7px;
            border:1px solid #dbe4ef;
            font-weight:800;
          "
        >
          ${reportEscape(x.homework_percent)}%
        </td>

        <td
          style="
            text-align:center;
            padding:10px 7px;
            border:1px solid #dbe4ef;
          "
        >
          ${reportEscape(x.attitude || '—')}
        </td>

        <td
          style="
            padding:10px 8px;
            border:1px solid #dbe4ef;
            line-height:1.45;
          "
        >
          ${reportEscape(x.comment || '—')}
        </td>

        <td
          style="
            padding:10px 8px;
            border:1px solid #dbe4ef;
            font-weight:800;
            color:#0f4fa8;
            white-space:nowrap;
          "
        >
          ${money(x.price)}
        </td>

      </tr>
    `).join('');
  } else {
    rows = `
      <tr>
        <td
          colspan="7"
          style="
            padding:35px;
            text-align:center;
            color:#94a3b8;
            border:1px solid #dbe4ef;
          "
        >
          Chưa có buổi học nào trong tháng này.
        </td>
      </tr>
    `;
  }

  const content = `

    ${reportStudentHeader()}

    <div style="padding:25px 30px 35px;">

      <div
        style="
          text-align:center;
          font-size:22px;
          font-weight:900;
          margin-bottom:20px;
          color:#0f172a;
        "
      >
        NHẬT KÝ BUỔI HỌC
      </div>

      <div
        style="
          text-align:center;
          font-size:13px;
          color:#64748b;
          margin-bottom:22px;
        "
      >
        ${reportMonthLabel()}
        &nbsp;•&nbsp;
        ${lessons.length} buổi
        &nbsp;•&nbsp;
        ${reportTotalHours()} giờ
      </div>

      <table
        style="
          width:100%;
          border-collapse:collapse;
          table-layout:fixed;
          font-size:11px;
        "
      >

        <thead>

          <tr style="background:#eff6ff;">

            <th
              style="
                width:38px;
                padding:10px 5px;
                border:1px solid #bfdbfe;
                color:#1d4ed8;
              "
            >
              STT
            </th>

            <th
              style="
                width:72px;
                padding:10px 5px;
                border:1px solid #bfdbfe;
                color:#1d4ed8;
              "
            >
              NGÀY
            </th>

            <th
              style="
                width:190px;
                padding:10px 5px;
                border:1px solid #bfdbfe;
                color:#1d4ed8;
              "
            >
              NỘI DUNG / MÔN
            </th>

            <th
              style="
                width:58px;
                padding:10px 5px;
                border:1px solid #bfdbfe;
                color:#1d4ed8;
              "
            >
              BTVN
            </th>

            <th
              style="
                width:75px;
                padding:10px 5px;
                border:1px solid #bfdbfe;
                color:#1d4ed8;
              "
            >
              Ý THỨC
            </th>

            <th
              style="
                width:220px;
                padding:10px 5px;
                border:1px solid #bfdbfe;
                color:#1d4ed8;
              "
            >
              NHẬN XÉT
            </th>

            <th
              style="
                width:90px;
                padding:10px 5px;
                border:1px solid #bfdbfe;
                color:#1d4ed8;
              "
            >
              HỌC PHÍ
            </th>

          </tr>

        </thead>

        <tbody>
          ${rows}
        </tbody>

      </table>

    </div>
  `;

  const wrapper = document.createElement('div');

  wrapper.style.position = 'fixed';
  wrapper.style.left = '-10000px';
  wrapper.style.top = '0';
  wrapper.style.zIndex = '-1';

  wrapper.innerHTML = reportShell(
    'Nhật ký buổi học',
    `${s.name || 'Học sinh'} • ${reportMonthLabel()}`,
    content
  );

  document.body.appendChild(wrapper);

  downloadReportElement('Nhat-ky-buoi-hoc');
}


// ==========================================================
// 6.3 BẢNG ĐIỂM CHI TIẾT
// ==========================================================

function exportGradesImage() {
  const s = data?.student || {};
  const grades = data?.grades || [];
  const avg = reportAverageGrade();

  let rows = '';

  if (grades.length) {
    rows = grades.map((x, i) => `
      <tr>

        <td
          style="
            text-align:center;
            padding:12px 8px;
            border:1px solid #dbe4ef;
            color:#64748b;
            font-weight:800;
          "
        >
          ${i + 1}
        </td>

        <td
          style="
            padding:12px 8px;
            border:1px solid #dbe4ef;
            white-space:nowrap;
          "
        >
          ${reportEscape(fmtDate(x.test_date))}
        </td>

        <td
          style="
            padding:12px 10px;
            border:1px solid #dbe4ef;
            font-weight:800;
          "
        >
          ${reportEscape(x.test_name)}
        </td>

        <td
          style="
            padding:12px 8px;
            border:1px solid #dbe4ef;
            text-align:center;
          "
        >
          <span
            style="
              display:inline-block;
              min-width:45px;
              padding:5px 8px;
              border-radius:8px;
              background:#eff6ff;
              color:#1d4ed8;
              font-size:16px;
              font-weight:900;
            "
          >
            ${reportEscape(x.score)}
          </span>
          <span style="font-size:10px;color:#94a3b8;">/10</span>
        </td>

        <td
          style="
            padding:12px 10px;
            border:1px solid #dbe4ef;
            line-height:1.5;
          "
        >
          ${reportEscape(x.mistakes || '—')}
        </td>

      </tr>
    `).join('');
  } else {
    rows = `
      <tr>
        <td
          colspan="5"
          style="
            padding:40px;
            text-align:center;
            color:#94a3b8;
            border:1px solid #dbe4ef;
          "
        >
          Chưa có bài kiểm tra nào trong tháng này.
        </td>
      </tr>
    `;
  }

  const content = `

    ${reportStudentHeader()}

    <div style="padding:28px 42px 35px;">

      <div
        style="
          text-align:center;
          font-size:23px;
          font-weight:900;
          color:#0f172a;
          margin-bottom:8px;
        "
      >
        BẢNG ĐIỂM CHI TIẾT
      </div>

      <div
        style="
          text-align:center;
          font-size:13px;
          color:#64748b;
          margin-bottom:22px;
        "
      >
        ${reportMonthLabel()}
      </div>

      <div
        style="
          display:flex;
          align-items:center;
          justify-content:space-between;
          padding:18px 20px;
          background:#f8fbff;
          border:1px solid #dbeafe;
          border-radius:15px;
          margin-bottom:22px;
        "
      >

        <div>
          <div style="font-size:11px;color:#64748b;font-weight:700;">
            SỐ BÀI KIỂM TRA
          </div>

          <div style="font-size:25px;font-weight:900;color:#0b63ce;margin-top:4px;">
            ${grades.length}
          </div>
        </div>

        <div style="text-align:right;">

          <div style="font-size:11px;color:#64748b;font-weight:700;">
            ĐIỂM TRUNG BÌNH
          </div>

          <div style="font-size:29px;font-weight:900;color:#0b63ce;margin-top:4px;">
            ${avg === null ? '—' : avg.toFixed(1)}
          </div>

        </div>

      </div>

      <table
        style="
          width:100%;
          border-collapse:collapse;
          font-size:12px;
        "
      >

        <thead>

          <tr style="background:#eff6ff;">

            <th
              style="
                width:42px;
                padding:11px 6px;
                border:1px solid #bfdbfe;
                color:#1d4ed8;
              "
            >
              STT
            </th>

            <th
              style="
                width:90px;
                padding:11px 6px;
                border:1px solid #bfdbfe;
                color:#1d4ed8;
              "
            >
              NGÀY
            </th>

            <th
              style="
                width:190px;
                padding:11px 6px;
                border:1px solid #bfdbfe;
                color:#1d4ed8;
              "
            >
              BÀI KIỂM TRA
            </th>

            <th
              style="
                width:80px;
                padding:11px 6px;
                border:1px solid #bfdbfe;
                color:#1d4ed8;
              "
            >
              ĐIỂM
            </th>

            <th
              style="
                padding:11px 6px;
                border:1px solid #bfdbfe;
                color:#1d4ed8;
              "
            >
              LỖI SAI & ĐIỂM CẦN CẢI THIỆN
            </th>

          </tr>

        </thead>

        <tbody>
          ${rows}
        </tbody>

      </table>

    </div>
  `;

  const wrapper = document.createElement('div');

  wrapper.style.position = 'fixed';
  wrapper.style.left = '-10000px';
  wrapper.style.top = '0';
  wrapper.style.zIndex = '-1';

  wrapper.innerHTML = reportShell(
    'Bảng điểm chi tiết',
    `${s.name || 'Học sinh'} • ${reportMonthLabel()}`,
    content
  );

  document.body.appendChild(wrapper);

  downloadReportElement('Bang-diem-chi-tiet');
}


// ==========================================================
// KẾT THÚC BỘ EXPORT REPORT
// ==========================================================
