        </main>
    </div>

    <!-- Toast Notification Copy -->
    <div id="copyToast" class="edu-toast fixed bottom-6 right-6 bg-slate-900 text-white px-4 py-3 rounded-xl shadow-xl flex items-center gap-2.5 transition-all duration-300 opacity-0 pointer-events-none transform translate-y-3 z-50">
        <span class="text-emerald-400 font-bold">✓</span>
        <span id="toastMsg" class="text-sm font-medium">Đã sao chép mã giáo viên vào bộ nhớ tạm!</span>
    </div>

    <style>
        .edu-toast.show {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }
    </style>

    <script>
        // Setup mobile sidebar toggle
        function toggleSide() {
            const side = document.getElementById('side');
            const overlay = document.getElementById('overlay');
            if (side) side.classList.toggle('hidden');
            if (overlay) overlay.classList.toggle('hidden');
        }

        // Sao chép mã giáo viên
        function copyTeacherCode() {
            const el = document.getElementById('teacherCodeVal');
            if (!el) return;
            const code = el.innerText.trim();
            if (!code) return;

            navigator.clipboard.writeText(code).then(() => {
                const btn = document.getElementById('copyBtnText');
                if (btn) btn.innerText = 'Đã chép!';
                const toast = document.getElementById('copyToast');
                if (toast) {
                    document.getElementById('toastMsg').innerText = `Đã sao chép mã giáo viên (${code})!`;
                    toast.classList.add('show');
                    setTimeout(() => {
                        if (btn) btn.innerText = 'Sao chép';
                        toast.classList.remove('show');
                    }, 2200);
                }
            }).catch(err => {
                alert('Không thể sao chép: ' + err);
            });
        }
    </script>
</body>
</html>