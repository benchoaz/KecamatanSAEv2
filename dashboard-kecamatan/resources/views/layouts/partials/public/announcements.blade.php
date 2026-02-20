@if(isset($publicAnnouncements) && $publicAnnouncements->count() > 0)
    <div
        class="relative bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 border-b border-slate-700 overflow-hidden z-40">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10">
        </div>
        <div class="container mx-auto max-w-7xl flex items-center h-10">
            <!-- Label -->
            <div
                class="relative z-10 flex items-center h-full bg-rose-600 px-6 transform -skew-x-12 -ml-4 shadow-lg shadow-rose-900/50">
                <div
                    class="transform skew-x-12 flex items-center gap-2 text-white font-black text-[10px] uppercase tracking-widest">
                    <span class="animate-pulse w-2 h-2 bg-white rounded-full"></span>
                    Info
                </div>
            </div>

            <!-- Ticker Content -->
            <div class="flex-1 overflow-hidden relative h-full flex items-center pl-6">
                <div class="ticker-wrap w-full">
                    <div class="ticker-move inline-block whitespace-nowrap hover:pause-animation">
                        @foreach($publicAnnouncements as $ann)
                            <div class="inline-flex items-center mx-8 group cursor-pointer"
                                onclick="openBotWithQuery('{{ $ann->content }}')">
                                <span class="text-rose-400 mr-2 text-xs"><i class="fas fa-chevron-right"></i></span>
                                <span class="text-xs font-bold text-slate-300 group-hover:text-white transition-colors">
                                    {{ $ann->content }}
                                </span>
                                <span
                                    class="ml-3 text-[9px] font-bold text-slate-500 border border-slate-700 px-1.5 rounded bg-slate-800/50">{{ $ann->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                        <!-- Duplicate for infinite scroll -->
                        @foreach($publicAnnouncements as $ann)
                            <div class="inline-flex items-center mx-8 group cursor-pointer"
                                onclick="openBotWithQuery('{{ $ann->content }}')">
                                <span class="text-rose-400 mr-2 text-xs"><i class="fas fa-chevron-right"></i></span>
                                <span class="text-xs font-bold text-slate-300 group-hover:text-white transition-colors">
                                    {{ $ann->content }}
                                </span>
                                <span
                                    class="ml-3 text-[9px] font-bold text-slate-500 border border-slate-700 px-1.5 rounded bg-slate-800/50">{{ $ann->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Close Button -->
            <button onclick="this.parentElement.parentElement.remove()"
                class="z-10 px-4 text-slate-500 hover:text-white transition-colors">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </div>
@endif