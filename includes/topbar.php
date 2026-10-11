    <!-- Top Header -->
    <header class="h-20 bg-white border-b border-slate-200 sticky top-0 z-20 flex items-center justify-between px-4 md:px-8">
      <div class="flex items-center gap-3">
        <button class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-xl" onclick="toggleSide()">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <div>
          <h1 class="font-extrabold text-xl text-slate-800"><?php echo $readonly ? 'Há»“ sÆ¡ há» c táº­p' : 'Quáº£n lÃ½ há» c táº­p'; ?></h1>
          <p class="text-xs text-slate-400"><?php echo $readonly ? 'Theo dÃµi tiáº¿n trÃ¬nh buá»•i há» c, Ä‘iá»ƒm sá»‘ vÃ  há» c phÃ­' : 'Quáº£n lÃ½ há» c sinh, nháº­t kÃ½ buá»•i há» c, báº£ng Ä‘iá»ƒm vÃ  há» c phÃ­'; ?></p>
        </div>
      </div>

      <!-- Action Bar: Export, Notification, Month Selector -->
      <div class="flex items-center gap-4">
        
        <!-- Export Button -->
        <button class="hidden sm:flex items-center gap-2 text-slate-500 hover:text-blue-600 font-semibold text-sm transition-all bg-slate-50 hover:bg-blue-50 px-3 py-2 rounded-xl border border-slate-200" onclick="alert('Chá»©c nÄƒng Xuáº¥t bÃ¡o cÃ¡o Ä‘ang Ä‘Æ°á»£c phÃ¡t triá»ƒn!')">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
          Xuáº¥t báº£ng Ä‘iá»ƒm
        </button>
        
        <!-- Notification Bell -->
        <button class="relative p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all border border-transparent hover:border-blue-200" onclick="alert('TÃ­nh nÄƒng thÃ´ng bÃ¡o má»›i sáº½ sá»›m ra máº¯t!')">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
          <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
        </button>

        <!-- Month Selector -->
        <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
          <label for="month" class="hidden sm:inline text-xs font-bold uppercase tracking-wider text-slate-400">ThÃ¡ng:</label>
          <input id="month" type="month" class="border border-slate-200 bg-white rounded-xl px-3.5 py-2 text-sm font-semibold text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all cursor-pointer shadow-sm" onchange="loadData()">
        </div>
      </div>
    </header>
