  <!-- MODAL CONTAINER CHUNG -->
  <div id="modal" class="modal hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 items-center justify-center p-4">
    <div id="modalBox" class="bg-white rounded-3xl w-full max-w-xl max-h-[92vh] overflow-y-auto border border-slate-100"></div>
  </div>

  <!-- TOAST NOTIFICATION CHO SAO CHÉP MÃ -->
  <div id="copyToast" class="edu-toast">
    <span class="text-emerald-400 font-bold">✓</span>
    <span id="toastMsg">Đã sao chép mã vào bộ nhớ tạm!</span>
  </div>

  <script>
    window.USER_ROLE = <?=json_encode($role)?>;
    window.TEACHER_CODE = <?=json_encode($teacherCode)?>;

    // Hàm sao chép mã giáo viên
    function copyTeacherCode() {
      const el = document.getElementById('teacherCodeVal');
      if (!el) return;
      const code = el.innerText.trim();
      if (!code || code === 'Chưa cập nhật') return;

      navigator.clipboard.writeText(code).then(() => {
        const btnText = document.getElementById('copyBtnText');
        const oldText = btnText.innerText;
        btnText.innerText = 'Đã chép!';

        const toast = document.getElementById('copyToast');
        document.getElementById('toastMsg').innerText = `Đã sao chép mã giáo viên (${code})!`;
        toast.classList.add('show');

        setTimeout(() => {
          btnText.innerText = oldText;
          toast.classList.remove('show');
        }, 2200);
      }).catch(err => {
        alert('Không thể sao chép: ' + err);
      });
    }

    // Hàm sao chép mã kết nối học sinh ở header trên cùng
    function copyTopStudentCode() {
      const el = document.getElementById('topStudentCodeVal');
      if (!el) return;
      const code = el.innerText.trim();
      if (!code || code === '—' || code === 'Chưa có') {
        alert('Vui lòng chọn một học sinh có mã liên kết để sao chép.');
        return;
      }

      navigator.clipboard.writeText(code).then(() => {
        const btnText = document.getElementById('copyTopStudentText');
        const oldText = btnText.innerText;
        btnText.innerText = 'Đã chép!';

        const toast = document.getElementById('copyToast');
        document.getElementById('toastMsg').innerText = `Đã sao chép mã kết nối học sinh (${code})!`;
        toast.classList.add('show');

        setTimeout(() => {
          btnText.innerText = oldText;
          toast.classList.remove('show');
        }, 2200);
      }).catch(err => {
        alert('Không thể sao chép: ' + err);
      });
    }

    // Hàm sao chép mã kết nối học sinh tại thẻ học sinh đang chọn
    function copyCurrentStudentCode() {
      const el = document.getElementById('studentConnectionCodeVal');
      if (!el) return;
      const code = el.innerText.trim();
      if (!code || code === '—') return;

      navigator.clipboard.writeText(code).then(() => {
        const btnText = document.getElementById('copyStudentBtnText');
        const oldText = btnText.innerText;
        btnText.innerText = 'Đã chép!';

        const toast = document.getElementById('copyToast');
        document.getElementById('toastMsg').innerText = `Đã sao chép mã kết nối học sinh (${code})!`;
        toast.classList.add('show');

        setTimeout(() => {
          btnText.innerText = oldText;
          toast.classList.remove('show');
        }, 2200);
      }).catch(err => {
        alert('Không thể sao chép: ' + err);
      });
    }
  </script>
  <script src="assets/js/app.js"></script>
</body>
</html>
